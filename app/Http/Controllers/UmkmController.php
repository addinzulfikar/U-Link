<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UmkmController extends Controller
{
    public function index()
    {
        $umkms = Umkm::with('owner')
            ->where('status', Umkm::STATUS_APPROVED)
            ->latest()
            ->paginate(12);

        return view('umkms.index', compact('umkms'));
    }

    public function show($slug)
    {
        $umkm = Umkm::with(['owner', 'products' => function($query) {
            $query->where('is_active', true);
        }])->where('slug', $slug)->firstOrFail();

        $isFavorited = false;
        if (Auth::check()) {
            $isFavorited = Auth::user()->favorites()->where('umkm_id', $umkm->id)->exists();
        }

        return view('umkms.show', compact('umkm', 'isFavorited'));
    }

    public function create()
    {
        // Check if user already has a UMKM
        if (Auth::user()->umkm) {
            return redirect()->route('umkm.manage')->with('error', 'Anda sudah memiliki UMKM.');
        }

        return view('umkms.create');
    }

    public function store(Request $request)
    {
        // Check if user already has a UMKM
        if (Auth::user()->umkm) {
            return redirect()->route('umkm.manage')->with('error', 'Anda sudah memiliki UMKM.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
        ]);

        $validated['owner_user_id'] = Auth::id();
        $validated['slug'] = Str::slug($validated['name']);
        $validated['status'] = Umkm::STATUS_PENDING;

        $umkm = Umkm::create($validated);

        return redirect()->route('umkm.manage')->with('success', 'UMKM berhasil dibuat dan menunggu persetujuan.');
    }

    public function manage()
    {
        $umkm = Auth::user()->umkm;

        if (!$umkm) {
            return redirect()->route('umkm.create');
        }

        $products = $umkm->products()->latest()->paginate(10);
        $stats = [
            'total_products' => $umkm->products()->where('type', Product::TYPE_PRODUCT)->count(),
            'total_services' => $umkm->products()->where('type', Product::TYPE_SERVICE)->count(),
            'total_favorites' => $umkm->favorites()->count(),
        ];

        return view('umkms.manage', compact('umkm', 'products', 'stats'));
    }

    public function edit()
    {
        $umkm = Auth::user()->umkm;

        if (!$umkm) {
            return redirect()->route('umkm.create');
        }

        return view('umkms.edit', compact('umkm'));
    }

    public function update(Request $request)
    {
        $umkm = Auth::user()->umkm;

        if (!$umkm) {
            return redirect()->route('umkm.create');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $umkm->update($validated);

        return redirect()->route('umkm.manage')->with('success', 'UMKM berhasil diperbarui.');
    }
}
