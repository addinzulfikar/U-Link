<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use App\Models\Review;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show dashboard entry point:
     * - user -> render dashboard.user
     * - admin_toko -> redirect dashboard.admin-toko
     * - super_admin -> redirect dashboard.super-admin
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('dashboard.super-admin');
        }

        if ($user->isAdminToko()) {
            return redirect()->route('dashboard.admin-toko');
        }

        // Default: role user
        $recentProducts = Product::with(['umkm', 'category'])
            ->whereHas('umkm', function ($q) {
                $q->where('status', Umkm::STATUS_APPROVED);
            })
            ->whereRaw('is_active is true')
            ->latest()
            ->take(6)
            ->get();

        $favoriteCount = $user->favorites()->count();

        return view('dashboard.user', compact('recentProducts', 'favoriteCount'));
    }

    /**
     * Show admin toko dashboard
     */
    public function adminToko()
    {
        $user = Auth::user();
        $umkm = $user->umkm;

        // Fallback for setups where admin_toko is linked via users.umkm_id
        if (! $umkm) {
            $umkm = $user->assignedUmkm;
        }

        $stats = [
            'total_products' => 0,
            'total_services' => 0,
            'total_favorites' => 0,
            'average_rating' => 0,
        ];

        $recentProducts = collect();
        $dashboardCharts = [
            'pie' => [
                'labels' => ['Produk', 'Jasa'],
                'values' => [0, 0],
            ],
            'bar' => [
                'labels' => [],
                'values' => [],
            ],
        ];

        if ($umkm) {
            $stats['total_products'] = $umkm->products()->where('type', Product::TYPE_PRODUCT)->count();
            $stats['total_services'] = $umkm->products()->where('type', Product::TYPE_SERVICE)->count();
            $stats['total_favorites'] = Favorite::where('umkm_id', $umkm->id)->count();

            $avgRating = Review::query()
                ->whereHas('product', function ($q) use ($umkm) {
                    $q->where('umkm_id', $umkm->id);
                })
                ->avg('rating');

            $stats['average_rating'] = $avgRating ? (float) $avgRating : 0.0;

            $recentProducts = $umkm->products()->latest()->take(5)->get();

            $dashboardCharts['pie']['values'] = [
                (int) $stats['total_products'],
                (int) $stats['total_services'],
            ];

            $topStocks = $umkm->products()
                ->where('type', Product::TYPE_PRODUCT)
                ->select(['name', 'stock'])
                ->orderByDesc('stock')
                ->take(7)
                ->get();

            $dashboardCharts['bar']['labels'] = $topStocks->pluck('name')->values()->all();
            $dashboardCharts['bar']['values'] = $topStocks->pluck('stock')->map(fn ($v) => (int) ($v ?? 0))->values()->all();
        }

        return view('dashboard.admin-toko', compact('umkm', 'stats', 'recentProducts', 'dashboardCharts'));
    }

    /**
     * Show super admin dashboard
     */
    public function superAdmin()
    {
        $stats = [
            'total_users' => User::where('role', User::ROLE_USER)->count(),
            'total_umkms' => Umkm::where('status', Umkm::STATUS_APPROVED)->count(),
            'pending_umkms' => Umkm::where('status', Umkm::STATUS_PENDING)->count(),
            'total_products' => Product::count(),
        ];

        $pendingUmkms = Umkm::with('owner')
            ->where('status', Umkm::STATUS_PENDING)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.super-admin', compact('stats', 'pendingUmkms'));
    }

    /**
     * Show spreadsheet analyzer page for admin toko
     */
    public function spreadsheetAnalyzer()
    {
        $umkm = Auth::user()->umkm;

        return view('dashboard.spreadsheet-analyzer', compact('umkm'));
    }
}
