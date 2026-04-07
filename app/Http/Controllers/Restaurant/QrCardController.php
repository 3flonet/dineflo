<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Table as TableModel;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCardController extends Controller
{
    /**
     * QR Card Designer – full interactive page.
     */
    public function designer(Restaurant $restaurant, Request $request)
    {
        $this->authorizeRestaurant($restaurant);

        // Ambill 1 meja sebagai preview, kalau belum ada meja satupun, buat instance sementara (dummy)
        $table = $restaurant->tables()->first() ?? new TableModel(['name' => 'Meja 01', 'area' => 'Indoor']);
        
        // URL QR
        $qrUrl = $table->id ? $table->url : url("/restaurant/{$restaurant->slug}/tables/1");

        // Generate QR as inline SVG
        $qrSvg = QrCode::size(300)->errorCorrection('H')->generate($qrUrl);
        $qrSvg = preg_replace('/<\?xml.*?\?>/i', '', trim($qrSvg));

        // Logo URL
        $logoUrl = $restaurant->logo ? Storage::url($restaurant->logo) : null;

        return view('restaurant.qr-designer', [
            'table'       => $table,
            'qrUrl'       => $qrUrl,
            'restaurant'  => $restaurant,
            'qrSvg'       => $qrSvg,
            'logoUrl'     => $logoUrl,
            'socialLinks' => is_array($restaurant->social_links) ? $restaurant->social_links : [],
        ]);
    }

    /**
     * Save the QR card design setup to the restaurant.
     */
    public function saveDesign(Restaurant $restaurant, Request $request)
    {
        $this->authorizeRestaurant($restaurant);

        $validated = $request->validate([
            'design' => 'required|array',
        ]);

        $restaurant->update([
            'qr_card_design' => $validated['design'],
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Single table QR print page (from designer).
     */
    public function single(Restaurant $restaurant, TableModel $table, Request $request)
    {
        abort_if($table->restaurant_id !== $restaurant->id, 403);
        $this->authorizeRestaurant($restaurant);

        $template = in_array($request->query('template'), ['minimal', 'bistro', 'dark'])
            ? $request->query('template')
            : 'minimal';

        return view('restaurant.qr-single-print', [
            'record'     => $table,
            'restaurant' => $restaurant,
            'template'   => $template,
        ]);
    }

    /**
     * Bulk print all active tables.
     */
    public function bulk(Restaurant $restaurant, Request $request)
    {
        $this->authorizeRestaurant($restaurant);

        $tables = TableModel::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->orderBy('area')
            ->orderBy('name')
            ->get();

        return view('restaurant.qr-bulk-print', [
            'restaurant' => $restaurant,
            'tables'     => $tables,
        ]);
    }

    protected function authorizeRestaurant(Restaurant $restaurant): void
    {
        $user = auth()->user();
        abort_unless($user, 401);

        if ($user->hasRole(['super_admin', 'restaurant_owner', 'restaurant_staff'])) {
            return;
        }

        abort_if($user->restaurant_id !== $restaurant->id, 403);
    }
}
