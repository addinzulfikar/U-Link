<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private function generateUniqueSlug(int $umkmId, string $name, ?int $ignoreProductId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;

        $suffix = 2;
        while (Product::query()
            ->where('umkm_id', $umkmId)
            ->when($ignoreProductId !== null, fn ($q) => $q->where('id', '!=', $ignoreProductId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;

            if ($suffix > 1000) {
                $slug = $base.'-'.Str::lower(Str::random(6));
                break;
            }
        }

        return $slug;
    }

    public function index(Request $request)
    {
        $query = Product::with(['umkm', 'category'])
            ->whereHas('umkm', function ($q) {
                $q->where('status', 'approved');
            })
            ->whereRaw('is_active is true');

        // Filter by type
        if ($request->has('type') && in_array($request->type, [Product::TYPE_PRODUCT, Product::TYPE_SERVICE])) {
            $query->where('type', $request->type);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('description', 'ILIKE', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12);
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    public function show($umkmSlug, $productSlug)
    {
        $product = Product::with(['umkm', 'category', 'reviews.user'])
            ->whereHas('umkm', function ($q) use ($umkmSlug) {
                $q->where('slug', $umkmSlug);
            })
            ->where('slug', $productSlug)
            ->firstOrFail();

        $averageRating = $product->reviews()->avg('rating');
        $totalReviews = $product->reviews()->count();

        $userReview = null;
        if (Auth::check()) {
            $userReview = $product->reviews()->where('user_id', Auth::id())->first();
        }

        return view('products.show', compact('product', 'averageRating', 'totalReviews', 'userReview'));
    }

    public function create()
    {
        $umkm = Auth::user()->umkm;

        if (! $umkm) {
            return redirect()->route('umkm.create')->with('error', 'Anda harus membuat UMKM terlebih dahulu.');
        }

        if (! $umkm->isApproved()) {
            return redirect()->route('umkm.manage')->with('error', 'UMKM Anda harus disetujui terlebih dahulu.');
        }

        $categories = Category::all();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $umkm = Auth::user()->umkm;

        if (! $umkm || ! $umkm->isApproved()) {
            return redirect()->route('umkm.manage')->with('error', 'UMKM Anda harus disetujui terlebih dahulu.');
        }

        $validated = $request->validate([
            'type' => 'required|in:product,service',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'stock' => 'nullable|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['umkm_id'] = $umkm->id;
        $validated['slug'] = $this->generateUniqueSlug($umkm->id, $validated['name']);
        $validated['is_active'] = $request->has('is_active');

        try {
            Product::create($validated);
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan produk/jasa. Coba ubah nama (slug) atau periksa koneksi database.');
        }

        return redirect()->route('umkm.manage')->with('success', 'Produk/Jasa berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $umkm = Auth::user()->umkm;
        $product = Product::where('umkm_id', $umkm->id)->findOrFail($id);
        $categories = Category::all();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $umkm = Auth::user()->umkm;
        $product = Product::where('umkm_id', $umkm->id)->findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|in:product,service',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'stock' => 'nullable|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = $this->generateUniqueSlug($umkm->id, $validated['name'], (int) $product->id);
        $validated['is_active'] = $request->has('is_active');

        $product->update($validated);

        return redirect()->route('umkm.manage')->with('success', 'Produk/Jasa berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $umkm = Auth::user()->umkm;
        $product = Product::where('umkm_id', $umkm->id)->findOrFail($id);

        $product->delete();

        return redirect()->route('umkm.manage')->with('success', 'Produk/Jasa berhasil dihapus.');
    }
}
