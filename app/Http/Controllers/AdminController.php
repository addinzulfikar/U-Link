<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $users = User::latest()
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

    public function updateCategory(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::findOrFail($id);
        
        // Regenerate slug if name changed
        if ($category->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        $category->update($validated);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    // User Management
    public function createUser()
    {
        return view('admin.users-create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,admin_toko,super_admin',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        User::create($validated);

        return redirect()->route('admin.users')->with('success', 'User berhasil ditambahkan.');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users-edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'role' => 'required|in:user,admin_toko,super_admin',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users')->with('success', 'User berhasil diperbarui.');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }

    // UMKM Management
    public function deleteUmkm($id)
    {
        $umkm = Umkm::findOrFail($id);
        $umkm->delete();

        return back()->with('success', 'UMKM berhasil dihapus.');
    }

    // Product Management
    public function products()
    {
        $products = Product::with(['umkm', 'category'])->latest()->paginate(20);
        return view('admin.products', compact('products'));
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }
}
