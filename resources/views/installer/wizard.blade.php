<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Installation Wizard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Space Grotesk', sans-serif; }
        [x-cloak] { display: none !important; }
        .glass { background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(24px); border: 1px solid rgba(51, 65, 85, 0.4); }
        .step-active { background: #3b82f6; color: white; box-shadow: 0 0 20px rgba(59, 130, 246, 0.4); }
        .step-done { background: #10b981; color: white; }
        .step-pending { background: #1e293b; color: #64748b; }
    </style>
</head>
<body class="bg-slate-950 text-slate-200 min-h-screen flex flex-col justify-center items-center p-4 overflow-x-hidden">
    <!-- Animated Background -->
    <div class="fixed inset-0 pointer-events-none opacity-20">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-600 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-purple-600 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-xl w-full relative" x-data="installerWizard()" x-cloak>
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-extrabold tracking-tighter text-white uppercase italic">SYSTEM <span class="text-blue-500">INSTALLER</span></h1>
            <p class="text-slate-500 text-sm font-medium tracking-widest mt-2 uppercase">Initial Deployment System</p>
        </div>

        <!-- Progress Bar -->
        <div class="flex justify-between mb-10 px-4 relative">
            <div class="absolute top-1/2 left-0 w-full h-0.5 bg-slate-800 -translate-y-1/2 z-0"></div>
            <template x-for="n in 5">
                <div class="relative z-10 w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm transition-all duration-500"
                    :class="step === n ? 'step-active' : (step > n ? 'step-done' : 'step-pending')">
                    <span x-text="step > n ? '✓' : n"></span>
                </div>
            </template>
        </div>

        <!-- Main Card -->
        <div class="glass rounded-[32px] p-8 md:p-10 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-5">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            </div>

            <!-- STEP 1: REQUIREMENTS -->
            <div x-show="step === 1" x-transition>
                <h2 class="text-2xl font-bold text-white mb-2">System Diagnostics</h2>
                <p class="text-slate-400 text-sm mb-6">Checking server capabilities and folder permissions.</p>

                <div class="space-y-3 mb-8">
                    <template x-for="(met, name) in requirements">
                        <div class="flex items-center justify-between p-4 bg-slate-900/50 rounded-2xl border border-slate-800 transition-colors"
                             :class="met ? 'border-emerald-500/20' : 'border-red-500/20'">
                            <span class="text-sm font-medium" x-text="name"></span>
                            <span x-show="met" class="text-emerald-500 text-xs font-black uppercase tracking-widest">Pass</span>
                            <span x-show="!met" class="text-red-500 text-xs font-black uppercase tracking-widest">Fail</span>
                        </div>
                    </template>
                </div>

                <div class="flex justify-end">
                    <button @click="nextStep()" :disabled="!isRequirementsMet"
                            class="px-8 py-3 bg-blue-600 hover:bg-blue-500 disabled:opacity-30 disabled:cursor-not-allowed text-white rounded-xl font-bold transition-all shadow-lg shadow-blue-600/20">
                        Proceed Setup
                    </button>
                </div>
            </div>

            <!-- STEP 2: ENGINE & ENVIRONMENT -->
            <div x-show="step === 2" x-transition>
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-2xl font-bold text-white">Engine & Setting</h2>
                </div>
                <p class="text-slate-400 text-sm mb-6">Link your persistent storage and server identity.</p>

                <!-- App Identity -->
                <div class="space-y-4 mb-6 p-5 bg-blue-500/5 border border-blue-500/10 rounded-2xl">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest font-black text-blue-400 mb-2">App Name</label>
                            <input type="text" x-model="db.app_name" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-blue-500 outline-none transition-colors" placeholder="e.g. My Smart POS">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest font-black text-blue-400 mb-2">Environment Mode</label>
                            <select x-model="db.app_env" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-blue-500 outline-none transition-colors appearance-none">
                                <option value="local">Local (Development)</option>
                                <option value="production">Production (Live)</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest font-black text-blue-400 mb-2">App Timezone</label>
                            <select x-model="db.app_timezone" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-blue-500 outline-none transition-colors appearance-none">
                                <optgroup label="UTC">
                                    <option value="UTC">UTC (Universal)</option>
                                </optgroup>
                                <optgroup label="Indonesia">
                                    <option value="Asia/Jakarta">Jakarta (WIB)</option>
                                    <option value="Asia/Makassar">Makassar (WITA)</option>
                                    <option value="Asia/Jayapura">Jayapura (WIT)</option>
                                </optgroup>
                                <optgroup label="Asia">
                                    <option value="Asia/Singapore">Singapore</option>
                                    <option value="Asia/Kuala_Lumpur">Kuala Lumpur</option>
                                    <option value="Asia/Bangkok">Bangkok</option>
                                    <option value="Asia/Tokyo">Tokyo</option>
                                    <option value="Asia/Dubai">Dubai</option>
                                </optgroup>
                                <optgroup label="Europe">
                                    <option value="Europe/London">London</option>
                                    <option value="Europe/Paris">Paris</option>
                                    <option value="Europe/Berlin">Berlin</option>
                                </optgroup>
                                <optgroup label="America">
                                    <option value="America/New_York">New York (EST)</option>
                                    <option value="America/Los_Angeles">Los Angeles (PST)</option>
                                    <option value="America/Chicago">Chicago (CST)</option>
                                </optgroup>
                                <optgroup label="Australia">
                                    <option value="Australia/Sydney">Sydney</option>
                                    <option value="Australia/Perth">Perth</option>
                                </optgroup>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest font-black text-blue-400 mb-2">Domain URL</label>
                            <input type="url" x-model="db.app_url" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-blue-500 outline-none transition-colors" placeholder="https://example.com">
                        </div>
                    </div>
                </div>

                <div class="space-y-4 mb-8">
                    <div class="flex items-center gap-2 mb-2">
                         <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Database Connection</span>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase tracking-widest font-black text-slate-500 mb-2">Database Host</label>
                        <input type="text" x-model="db.host" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-blue-500 outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase tracking-widest font-black text-slate-500 mb-2">Database Name</label>
                        <input type="text" x-model="db.database" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-blue-500 outline-none transition-colors">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest font-black text-slate-500 mb-2">User</label>
                            <input type="text" x-model="db.username" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-blue-500 outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase tracking-widest font-black text-slate-500 mb-2">Password</label>
                            <input type="password" x-model="db.password" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-blue-500 outline-none transition-colors">
                        </div>
                    </div>
                </div>

                <div x-show="error" class="mb-4 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-xs font-medium" x-text="error"></div>

                <div class="flex justify-end gap-3">
                    <button @click="testDatabase()" :disabled="loading || dbSuccess"
                            class="px-8 py-3 rounded-xl font-bold transition-all flex items-center gap-2"
                            :class="dbSuccess ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-slate-800 hover:bg-slate-700 text-white'">
                        
                        <template x-if="loading">
                            <span class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Testing...</span>
                            </span>
                        </template>

                        <template x-if="!loading && !dbSuccess">
                            <span>Test Connection</span>
                        </template>

                        <template x-if="!loading && dbSuccess">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Connected</span>
                            </span>
                        </template>
                    </button>
                    
                    <button @click="saveDatabase()" x-show="dbSuccess" x-transition
                            class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold transition-all shadow-lg shadow-blue-600/20">
                        Next Phase
                    </button>
                </div>
            </div>

            <!-- STEP 3: LICENSE -->
            <div x-show="step === 3" x-transition>
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500 mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-2">License Authentication</h2>
                    <p class="text-slate-400 text-sm">Verify your legal copy of this application.</p>
                </div>

                <div class="space-y-4 mb-8">
                    <div x-show="!hasProductSecret">
                        <label class="block text-[10px] uppercase tracking-widest font-black text-slate-500 mb-2">Product Secret Key</label>
                        <input type="password" placeholder="Enter Product Secret from Dashboard" x-model="productSecret" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-blue-500 outline-none transition-colors">
                        <p class="text-[9px] text-amber-500/60 mt-2 uppercase tracking-tighter italic">Found in LicenseHub Dashboard > Product Settings</p>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase tracking-widest font-black text-slate-500 mb-2">License Key</label>
                        <input type="text" placeholder="3FLO-XXXX-XXXX-XXXX" x-model="licenseKey" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-5 text-lg font-mono tracking-widest focus:border-blue-500 outline-none transition-colors uppercase italic">
                    </div>
                </div>

                <div x-show="error" class="mb-4 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-xs font-medium" x-text="error"></div>

                <div class="flex justify-end">
                    <button @click="verifyLicense()" :disabled="loading"
                            class="px-10 py-4 bg-blue-600 hover:bg-blue-500 disabled:opacity-30 text-white rounded-xl font-bold transition-all shadow-lg shadow-blue-600/20">
                        <span x-show="!loading">Authorize System</span>
                        <span x-show="loading">Authenticating...</span>
                    </button>
                </div>
            </div>

            <!-- STEP 4: MIGRATIONS -->
            <div x-show="step === 4" x-transition>
                <div class="mb-10 text-center">
                    <h2 class="text-2xl font-bold text-white mb-2">Reconstructing Universe</h2>
                    <p class="text-slate-400 text-sm">Deploying database tables and seeding standard assets.</p>
                </div>

                <!-- Install Preference (Alpine data) -->
                <div class="space-y-6 mb-10 p-6 bg-slate-900/30 border border-slate-800 rounded-3xl" x-show="migrationProgress === 0">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-black uppercase tracking-widest text-slate-500">Installation Mode</span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <button @click="installType = 'demo'" 
                                :class="installType === 'demo' ? 'border-blue-500 bg-blue-500/10' : 'border-slate-800 bg-slate-950/30 opacity-60'"
                                class="p-5 border rounded-2xl text-left transition-all">
                            <p class="text-sm font-bold text-white mb-1">Full Demo</p>
                            <p class="text-[10px] text-slate-500 leading-relaxed uppercase italic">Includes Dummy Food, Orders, & Sample analytics.</p>
                        </button>
                        <button @click="installType = 'clean'" 
                                :class="installType === 'clean' ? 'border-blue-500 bg-blue-500/10' : 'border-slate-800 bg-slate-950/30 opacity-60'"
                                class="p-5 border rounded-2xl text-left transition-all">
                            <p class="text-sm font-bold text-white mb-1">Clean Install</p>
                            <p class="text-[10px] text-slate-500 leading-relaxed uppercase italic">Ready-to-use factory settings only. Zero dummy data.</p>
                        </button>
                    </div>

                    <!-- Admin Creation Form (Only for Clean) -->
                    <div x-show="installType === 'clean'" x-transition class="mt-8 pt-6 border-t border-slate-800 space-y-4">
                        <div class="flex items-center gap-2 mb-2">
                             <span class="text-xs font-black uppercase tracking-widest text-emerald-500">Master Account Identity</span>
                        </div>
                        <div>
                             <input type="text" placeholder="Super Admin Name" x-model="admin.name" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-blue-500 outline-none transition-colors">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <input type="email" placeholder="Email Address" x-model="admin.email" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-blue-500 outline-none transition-colors">
                            <input type="password" placeholder="Password" x-model="admin.password" class="w-full bg-slate-950/50 border border-slate-800 rounded-xl px-4 py-3 text-sm focus:border-blue-500 outline-none transition-colors">
                        </div>
                        <p class="text-[10px] text-amber-500/50 italic font-medium">This account will hold total control over the platform.</p>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="relative w-full h-1.5 bg-slate-800 rounded-full overflow-hidden mb-12" x-show="migrationProgress > 0">
                    <div class="absolute inset-0 bg-blue-500 transition-all duration-1000 ease-out" :style="`width: ${migrationProgress}%`"></div>
                </div>

                <div class="space-y-3 mb-10" x-show="migrationProgress > 0">
                    <div class="flex items-center gap-3 text-xs font-bold" :class="migrationProgress > 10 ? 'text-blue-400' : 'text-slate-600'">
                        <div class="w-1.5 h-1.5 rounded-full" :class="migrationProgress > 10 ? 'bg-blue-400' : 'bg-slate-700'"></div>
                        GENERATING MIGRATION LIST
                    </div>
                    <div class="flex items-center gap-3 text-xs font-bold" :class="migrationProgress > 50 ? 'text-blue-400' : 'text-slate-600'">
                        <div class="w-1.5 h-1.5 rounded-full" :class="migrationProgress > 50 ? 'bg-blue-400' : 'bg-slate-700'"></div>
                        EXECUTING SCHEMA DEPLOYMENT
                    </div>
                    <div class="flex items-center gap-3 text-xs font-bold" :class="migrationProgress >= 100 ? 'text-emerald-400' : 'text-slate-600'">
                        <div class="w-1.5 h-1.5 rounded-full" :class="migrationProgress >= 100 ? 'bg-emerald-400' : 'bg-slate-700'"></div>
                        INJECTING INITIAL SEED DATA
                    </div>
                </div>

                <div x-show="error" class="mb-4 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-xs font-medium" x-text="error"></div>

                <div class="flex justify-end">
                    <button @click="startMigration()" x-show="migrationProgress === 0"
                            class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold transition-all">
                        Execute Deployment
                    </button>
                    <button @click="step = 5" x-show="migrationProgress >= 100"
                            class="px-8 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold transition-all">
                        Final Step
                    </button>
                </div>
            </div>

            <!-- STEP 5: FINALIZE -->
            <div x-show="step === 5" x-transition class="text-center py-6">
                <div class="w-24 h-24 bg-emerald-500/10 rounded-[32px] flex items-center justify-center text-emerald-500 mx-auto mb-8 animate-bounce">
                    <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                </div>
                <h2 class="text-3xl font-bold text-white mb-4">Universe Ready</h2>
                <p class="text-slate-400 text-sm mb-12 max-w-xs mx-auto">Installation completed successfully. The core system is now locked and protected.</p>

                <div class="bg-indigo-500/5 border border-indigo-500/20 rounded-2xl p-6 mb-12 text-left">
                    <p class="text-[10px] font-black uppercase tracking-widest text-indigo-400 mb-3">Primary Access Key</p>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500">Identity</p>
                            <p class="text-sm font-bold text-white" x-text="installType === 'clean' ? admin.email : 'admin@dineflo.com'"></p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">Secret</p>
                            <p class="text-sm font-bold text-white italic tracking-tighter" x-text="installType === 'clean' ? admin.password : 'password'"></p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-4">
                        <div>
                            <p class="text-xs text-slate-500">Admin URL</p>
                            <p class="text-sm font-bold text-white" x-text="db.app_url + '/admin'"></p>
                        </div>
                        <button @click="copyCredentials()" class="bg-indigo-600 hover:bg-indigo-500 text-white p-2 rounded-lg transition-colors group relative" title="Copy to clipboard">
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.40.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                            </svg>
                            <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap" x-text="copied ? 'Copied!' : 'Copy'">Copy</span>
                        </button>
                    </div>
                </div>

                <button @click="finalize()" :disabled="loading"
                        class="w-full py-5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white rounded-2xl font-bold transition-all transform hover:scale-[1.02] active:scale-95 shadow-xl shadow-blue-600/20">
                    <span x-show="!loading">Enter Application</span>
                    <span x-show="loading">Finalizing...</span>
                </button>
            </div>
        </div>

        <p class="text-center mt-8 text-[10px] font-bold text-slate-600 uppercase tracking-[0.3em]">
            &copy; 2026 DINEFLO POS v6.0
        </p>
    </div>

    <script>
        function installerWizard() {
            return {
                step: 1,
                loading: false,
                error: null,
                requirements: {},
                isRequirementsMet: false,
                db: { 
                    host: '127.0.0.1', 
                    database: '', 
                    username: 'root', 
                    password: '', 
                    app_url: window.location.origin,
                    app_name: 'Smart POS',
                    app_env: 'local',
                    app_timezone: 'UTC'
                },
                dbSuccess: false,
                hasProductSecret: {{ $has_product_secret ? 'true' : 'false' }},
                productSecret: '',
                licenseKey: '',
                installType: 'demo',
                admin: { name: '', email: '', password: '' },
                migrationProgress: 0,
                csrfToken: '{{ csrf_token() }}',
                copied: false,

                init() {
                    this.fetchRequirements();
                },

                async fetchRequirements() {
                    const res = await fetch('/install/requirements', { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrfToken } });
                    const data = await res.json();
                    this.requirements = data.results;
                    this.isRequirementsMet = data.success;
                },

                nextStep() {
                    this.step++;
                    this.error = null;
                },

                async testDatabase() {
                    this.loading = true;
                    this.error = null;
                    try {
                        const res = await fetch('/install/database', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                            body: JSON.stringify(this.db)
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.dbSuccess = true;
                        } else {
                            this.error = data.message;
                        }
                    } catch (e) {
                        this.error = 'Network Error';
                    }
                    this.loading = false;
                },

                saveDatabase() {
                    this.nextStep();
                },

                async verifyLicense() {
                    this.loading = true;
                    this.error = null;
                    try {
                        const res = await fetch('/install/license', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                            body: JSON.stringify({ 
                                license_key: this.licenseKey,
                                product_secret: this.productSecret 
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.nextStep();
                        } else {
                            this.error = data.message;
                        }
                    } catch (e) {
                        this.error = 'License server unreachable.';
                    }
                    this.loading = false;
                },

                async startMigration() {
                    // Validation for Clean Install
                    if (this.installType === 'clean') {
                        if (!this.admin.name || !this.admin.email || !this.admin.password) {
                            this.error = 'All Master Account fields are required for Clean Installation.';
                            return;
                        }
                    }

                    this.migrationProgress = 20;
                    this.error = null;
                    try {
                        this.migrationProgress = 50;
                        const res = await fetch('/install/migrate', { 
                            method: 'POST', 
                            headers: { 
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken 
                            },
                            body: JSON.stringify({
                                install_type: this.installType,
                                admin: this.admin
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.migrationProgress = 100;
                        } else {
                            this.error = data.message;
                            this.migrationProgress = 0;
                        }
                    } catch (e) {
                        this.error = 'Migration error: ' + e.message;
                        this.migrationProgress = 0;
                    }
                },

                async finalize() {
                    this.loading = true;
                    this.error = null;
                    try {
                        const res = await fetch('/install/finalize', { 
                            method: 'POST', 
                            headers: { 
                                'X-CSRF-TOKEN': this.csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            } 
                        });
                        
                        const data = await res.json();
                        if (data.success) {
                            window.location.href = this.db.app_url + '/admin';
                        } else {
                            this.error = data.message;
                            this.loading = false;
                        }
                    } catch (e) {
                        this.error = 'Finalization failure: ' + e.message;
                        this.loading = false;
                    }
                },
                
                copyCredentials() {
                    const textToCopy = `Admin URL: ${this.db.app_url}/admin\nIdentity: admin@dineflo.com\nSecret: password`;
                    navigator.clipboard.writeText(textToCopy).then(() => {
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    });
                }
            }
        }
    </script>
</body>
</html>
