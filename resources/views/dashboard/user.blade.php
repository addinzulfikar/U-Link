@extends('layouts.app')

@section('title', 'Dashboard User - U-LINK')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1">Dashboard</h1>
        <div class="text-secondary">Selamat datang, <span class="fw-semibold">{{ Auth::user()->name }}</span></div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-secondary small mb-1">UMKM Favorit</div>
                    <div class="h3 fw-bold mb-0">{{ $favoriteCount }}</div>
                    <a href="{{ route('favorites.index') }}" class="stretched-link text-decoration-none small">Lihat semua →</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-secondary small mb-1">Jelajahi UMKM</div>
                    <div class="h3 fw-bold mb-0">🏪</div>
                    <a href="{{ route('umkms.index') }}" class="stretched-link text-decoration-none small">Jelajahi →</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-secondary small mb-1">Produk & Jasa</div>
                    <div class="h3 fw-bold mb-0">🛍️</div>
                    <a href="{{ route('products.index') }}" class="stretched-link text-decoration-none small">Lihat semua →</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Products -->
    @if($recentProducts->count() > 0)
        <div class="mb-4">
            <h4 class="fw-bold mb-3">Produk & Jasa Terbaru</h4>
            <div class="row g-3 g-lg-4">
                @foreach($recentProducts as $product)
                    <div class="col-md-6 col-lg-4 col-xl-2">
                        <div class="card product-card h-100">
                            <div class="position-relative">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" class="product-card-img" alt="{{ $product->name }}">
                                @else
                                    <div class="product-card-placeholder">
                                        {{ $product->type == 'product' ? '🛍️' : '💼' }}
                                    </div>
                                @endif
                                @if($product->type == 'service')
                                    <span class="badge badge-tokped position-absolute top-0 end-0 m-2">Jasa</span>
                                @endif
                            </div>
                            <div class="card-body">
                                <h5 class="card-title h6 mb-2">{{ Str::limit($product->name, 30) }}</h5>
                                <p class="text-secondary small mb-2">
                                    <a href="{{ route('umkms.show', $product->umkm->slug) }}" class="text-decoration-none text-secondary">
                                        {{ Str::limit($product->umkm->name, 20) }}
                                    </a>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="badge badge-price small">{{ $product->formatted_price }}</span>
                                    <a href="{{ route('products.show', [$product->umkm->slug, $product->slug]) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary">Lihat Semua Produk</a>
            </div>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h5 class="fw-semibold mb-3">Aksi Cepat</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <a href="{{ route('umkms.index') }}" class="card border text-decoration-none h-100 hover-shadow">
                        <div class="card-body">
                            <div class="h2 mb-2">🏪</div>
                            <h6 class="card-title">Jelajahi UMKM</h6>
                            <p class="card-text small text-secondary mb-0">Temukan berbagai UMKM lokal</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('products.index') }}" class="card border text-decoration-none h-100 hover-shadow">
                        <div class="card-body">
                            <div class="h2 mb-2">🛍️</div>
                            <h6 class="card-title">Produk & Jasa</h6>
                            <p class="card-text small text-secondary mb-0">Lihat semua produk dan jasa</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
