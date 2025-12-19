<?php

namespace App\Http\Controllers;

use App\Models\Product;
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
            ->where('is_active', true)
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
        $umkm = Auth::user()->umkm;

        $stats = [
            'total_products' => 0,
            'total_services' => 0,
            'total_favorites' => 0,
            'average_rating' => 0,
        ];

        $recentProducts = collect();

        if ($umkm) {
            $stats['total_products'] = $umkm->products()->where('type', Product::TYPE_PRODUCT)->count();
            $stats['total_services'] = $umkm->products()->where('type', Product::TYPE_SERVICE)->count();
            $stats['total_favorites'] = $umkm->favorites()->count();

            $recentProducts = $umkm->products()->latest()->take(5)->get();
        }

        return view('dashboard.admin-toko', compact('umkm', 'stats', 'recentProducts'));
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
