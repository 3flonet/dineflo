<?php

namespace App\Http\Controllers;

use App\Helpers\EnvHelper;
use App\Services\LicenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallerController extends Controller
{
    /**
     * Show the installer wizard
     */
    public function index()
    {
        return view('installer.wizard', [
            'has_product_secret' => !empty(env('LICENSE_HUB_PRODUCT_SECRET') ?: env('LICENSEHUB_PRODUCT_SECRET')),
        ]);
    }

    /**
     * Step 1: Check Requirements
     */
    public function checkRequirements()
    {
        $requirements = [
            'PHP Version >= 8.2' => PHP_VERSION_ID >= 80200,
            'BCMath Extension' => extension_loaded('bcmath'),
            'Ctype Extension' => extension_loaded('ctype'),
            'JSON Extension' => extension_loaded('json'),
            'Mbstring Extension' => extension_loaded('mbstring'),
            'OpenSSL Extension' => extension_loaded('openssl'),
            'PDO Extension' => extension_loaded('pdo'),
            'Tokenizer Extension' => extension_loaded('tokenizer'),
            'XML Extension' => extension_loaded('xml'),
            'Fileinfo Extension' => extension_loaded('fileinfo'),
            '.env Writable' => File::isWritable(base_path('.env')),
            'Storage Writable' => File::isWritable(storage_path()),
            'Bootstrap/Cache Writable' => File::isWritable(base_path('bootstrap/cache')),
        ];

        $allMet = !in_array(false, $requirements, true);

        return response()->json([
            'results' => $requirements,
            'success' => $allMet
        ]);
    }

    /**
     * Step 2: Test Database Connection & Save to .env
     */
    public function setupDatabase(Request $request)
    {
        $data = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_env' => 'required|in:local,production',
            'app_timezone' => 'required|string',
            'app_url' => 'required|url',
            'host' => 'required',
            'database' => 'required',
            'username' => 'required',
            'password' => 'nullable',
        ]);

        // Attempt to connect temporarily
        try {
            config(['database.connections.setup' => [
                'driver' => 'mysql',
                'host' => $data['host'],
                'database' => $data['database'],
                'username' => $data['username'],
                'password' => $data['password'],
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ]]);

            DB::connection('setup')->getPdo();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Connection Failed: ' . $e->getMessage()], 422);
        }

        // --- SMART REVERB AUTO-CONFIG ---
        $urlParts = parse_url($data['app_url']);
        $reverbHost = $urlParts['host'] ?? '127.0.0.1';
        $reverbScheme = ($urlParts['scheme'] ?? 'http') === 'https' ? 'https' : 'http';
        $reverbPort = $reverbScheme === 'https' ? '443' : '8081';

        // Auto-generate Reverb Keys if missing
        $reverbAppId = env('REVERB_APP_ID') ?: rand(100000, 999999);
        $reverbAppKey = env('REVERB_APP_KEY') ?: \Illuminate\Support\Str::random(20);
        $reverbAppSecret = env('REVERB_APP_SECRET') ?: \Illuminate\Support\Str::random(20);

        // Save to .env
        EnvHelper::setMany([
            'APP_NAME' => $data['app_name'],
            'APP_ENV' => $data['app_env'],
            'APP_DEBUG' => $data['app_env'] === 'local' ? 'true' : 'false',
            'APP_TIMEZONE' => $data['app_timezone'],
            'APP_URL' => $data['app_url'],
            'DB_HOST' => $data['host'],
            'DB_DATABASE' => $data['database'],
            'DB_USERNAME' => $data['username'],
            'DB_PASSWORD' => $data['password'] ?? '',
            
            // Auto-calculated Reverb Settings
            'REVERB_APP_ID' => $reverbAppId,
            'REVERB_APP_KEY' => $reverbAppKey,
            'REVERB_APP_SECRET' => $reverbAppSecret,
            'REVERB_HOST' => $reverbHost,
            'REVERB_SCHEME' => $reverbScheme,
            'REVERB_PORT' => $reverbPort,
            'REVERB_SERVER_HOST' => '0.0.0.0', 
            'REVERB_SERVER_PORT' => '8081',

            // Pass to Vite environment (for Frontend)
            'VITE_REVERB_APP_KEY' => $reverbAppKey,
            'VITE_REVERB_HOST' => $reverbHost,
            'VITE_REVERB_PORT' => $reverbPort,
            'VITE_REVERB_SCHEME' => $reverbScheme,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Step 3: Run Migrations & Seeders
     */
    public function runMigrations(Request $request)
    {
        // 🛡️ SECURITY: Verify signed action token from server (Step 3 bypass prevention)
        // For the sake of this local implementation, we check if license activation happened in session
        if (!session('license_handshake')) {
             return response()->json(['success' => false, 'message' => 'Security Error: Please activate license first.'], 403);
        }

        try {
            Artisan::call('optimize:clear');
            Artisan::call('migrate:fresh', ['--force' => true]);
            
            // Determine if demo data should be seeded
            $isDemo = $request->input('install_type', 'demo') === 'demo';
            config(['app.seed_demo' => $isDemo]);
            
            Artisan::call('db:seed', ['--force' => true]);
            
            // Generate Shield Permissions for all panels
            Artisan::call('shield:generate', ['--all' => true, '--panel' => 'admin']);
            Artisan::call('shield:generate', ['--all' => true, '--panel' => 'hq']);
            Artisan::call('shield:generate', ['--all' => true, '--panel' => 'restaurant']);

            // If Clean Install, Create the Super Admin manually
            if (!$isDemo) {
                $adminData = $request->input('admin');
                $admin = \App\Models\User::create([
                    'name' => $adminData['name'],
                    'email' => $adminData['email'],
                    'password' => \Illuminate\Support\Facades\Hash::make($adminData['password']),
                    'email_verified_at' => now(),
                ]);
                $admin->assignRole('super_admin');
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Step 4: Verify License (via LicenseService)
     */
    public function verifyLicense(Request $request)
    {
        try {
            $request->validate(['license_key' => 'required']);
            
            // 🧪 If Product Secret is provided in request (because it was missing in .env)
            if ($request->has('product_secret') && !empty($request->product_secret)) {
                EnvHelper::set('LICENSE_HUB_PRODUCT_SECRET', $request->product_secret);
                // Reload config for the current request
                config(['services.license_hub.product_secret' => $request->product_secret]);
            }

            $service = app(LicenseService::class);
            $result = $service->verify($request->license_key);

            if ($result['success']) {
                session(['license_handshake' => true]);
                
                // Persist license key to .env
                EnvHelper::set('LICENSE_HUB_KEY', $request->license_key);
                
                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'message' => $result['message']], 422);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Installer Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' line ' . $e->getLine()], 500);
        }
    }

    /**
     * Step 5: Finalize Installation
     */
    public function finalize()
    {
        try {
            \Illuminate\Support\Facades\Log::info('📦 FINALIZING INSTALLATION...');

            // 1. ALWAYS CREATE LOCK FILE FIRST
            \Illuminate\Support\Facades\File::put(storage_path('installed.lock'), date('Y-m-d H:i:s'));
            \Illuminate\Support\Facades\Log::info('🔒 installed.lock created successfully.');

            // 2. Generate App Key ONLY if completely missing
            try {
                if (empty(env('APP_KEY')) || env('APP_KEY') === 'base64:...') {
                    \Illuminate\Support\Facades\Artisan::call('key:generate', ['--force' => true]);
                    \Illuminate\Support\Facades\Log::info('🔑 APP_KEY generated.');
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('⚠️ Failed to generate APP_KEY (possibly Windows file lock), but old key may still exist.');
            }

            // 3. Update .env placeholder
            $envUpdate = \App\Helpers\EnvHelper::set('APP_INSTALLED', 'true');
            if ($envUpdate) {
                \Illuminate\Support\Facades\Log::info('📝 .env APP_INSTALLED updated to true.');
            } else {
                \Illuminate\Support\Facades\Log::warning('⚠️ Failed to update APP_INSTALLED in .env.');
            }

            // 4. Sync License data (DB & .env)
            if (session('tmp_license_key')) {
                $licenseData = [
                    'license_key' => session('tmp_license_key'),
                    'license_status' => 'active',
                    'license_expires_at' => session('tmp_license_expires_at'),
                    'license_grace_until' => session('tmp_license_grace_until'),
                    'license_customer_name' => session('tmp_license_customer_name'),
                    'license_customer_email' => session('tmp_license_customer_email'),
                    'license_domain' => request()->getHost(),
                    'license_last_ping_at' => now()->toDateTimeString(),
                ];

                $service = app(LicenseService::class);
                $licenseData['license_status_token'] = $service->generateStatusToken('active', session('tmp_license_key'));

                // Sync to Database
                foreach ($licenseData as $key => $value) {
                    \Illuminate\Support\Facades\DB::table('settings')->updateOrInsert(
                        ['group' => 'general', 'name' => $key],
                        ['payload' => json_encode($value), 'locked' => false, 'created_at' => now(), 'updated_at' => now()]
                    );
                }

                // Sync to .env for fallback/performance
                \App\Helpers\EnvHelper::setMany([
                    'LICENSE_KEY' => $licenseData['license_key'],
                    'LICENSE_STATUS' => $licenseData['license_status'],
                    'LICENSE_DOMAIN' => $licenseData['license_domain'],
                    'LICENSE_EXPIRES_AT' => $licenseData['license_expires_at'],
                    'LICENSE_GRACE_UNTIL' => $licenseData['license_grace_until'],
                    'LICENSE_LAST_PING_AT' => $licenseData['license_last_ping_at'],
                    'LICENSE_CUSTOMER_NAME' => $licenseData['license_customer_name'],
                    'LICENSE_CUSTOMER_EMAIL' => $licenseData['license_customer_email'],
                ]);
                
                session()->forget([
                    'tmp_license_key', 'tmp_license_status', 'tmp_license_expires_at', 
                    'tmp_license_grace_until', 'tmp_license_customer_name', 'tmp_license_customer_email'
                ]);
                \Illuminate\Support\Facades\Log::info('✅ Complete License details synced to DB and .env.');
            }
            
            // 5. ENSURE STORAGE LINK EXISTS
            try {
                \Illuminate\Support\Facades\Artisan::call('storage:link');
                \Illuminate\Support\Facades\Log::info('🔗 Storage symbolic link created.');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('⚠️ Failed to create storage link: ' . $e->getMessage());
            }

            // 6. AUTO-GENERATE VAPID KEYS FOR PWA NOTIFICATIONS
            try {
                if (empty(env('VAPID_PUBLIC_KEY'))) {
                    \Illuminate\Support\Facades\Artisan::call('webpush:vapid');
                    \Illuminate\Support\Facades\Log::info('🔔 VAPID Keys generated for Web Push notifications.');
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('⚠️ Failed to generate VAPID keys: ' . $e->getMessage());
            }

            // 7. ATTEMPT TO START REVERB SERVER IN BACKGROUND (FOR VPS/LINUX)
            try {
                if (function_exists('exec')) {
                    $command = "nohup php " . base_path('artisan') . " reverb:start > /dev/null 2>&1 &";
                    exec($command);
                    \Illuminate\Support\Facades\Log::info('⚡ Reverb server startup attempted in background.');
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('⚠️ Failed to auto-start Reverb. Manual start required.');
            }
            
            \Illuminate\Support\Facades\Log::info('🏁 INSTALLATION COMPLETED!');
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('❌ FINALIZE FAILED: ' . $e->getMessage());
            // Last resort for Windows: create the lock and return success anyway if it's just a file-lock issue
            if (str_contains($e->getMessage(), 'Unable to create the key')) {
                return response()->json(['success' => true, 'message' => 'System ready, but keys could not be rewritten. You may need to run key:generate manually.']);
            }
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
