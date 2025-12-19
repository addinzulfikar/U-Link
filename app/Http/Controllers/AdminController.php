<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Umkm;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::where('role', User::ROLE_USER)->count(),
            'total_admin_toko' => User::where('role', User::ROLE_ADMIN_TOKO)->count(),
            'total_umkms' => Umkm::count(),
            'pending_umkms' => Umkm::where('status', Umkm::STATUS_PENDING)->count(),
            'approved_umkms' => Umkm::where('status', Umkm::STATUS_APPROVED)->count(),
            'total_products' => Product::count(),
        ];

        $pendingUmkms = Umkm::with('owner')
            ->where('status', Umkm::STATUS_PENDING)
            ->latest()
            ->take(5)
            ->get();

        return view('admin.index', compact('stats', 'pendingUmkms'));
    }

    public function users()
    {
        $users = User::whereIn('role', [User::ROLE_USER, User::ROLE_ADMIN_TOKO])
            ->latest()
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function umkms(Request $request)
    {
        $query = Umkm::with('owner');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $umkms = $query->latest()->paginate(20);

        return view('admin.umkms', compact('umkms'));
    }

    public function approveUmkm($id)
    {
        $umkm = Umkm::findOrFail($id);
        $umkm->update(['status' => Umkm::STATUS_APPROVED]);

        return back()->with('success', 'UMKM berhasil disetujui.');
    }

    public function rejectUmkm($id)
    {
        $umkm = Umkm::findOrFail($id);
        $umkm->update(['status' => Umkm::STATUS_REJECTED]);

        return back()->with('success', 'UMKM ditolak.');
    }

    public function categories()
    {
        $categories = Category::withCount('products')->latest()->paginate(20);
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Category::create($validated);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
