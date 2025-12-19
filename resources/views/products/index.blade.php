@extends('layouts.app')

@section('title', 'Produk & Jasa UMKM - U-LINK')

@section('content')
<div class="container">
    <!-- Header & Search -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 fw-bold mb-3">Produk & Jasa UMKM</h1>
            <form method="GET" action="{{ route('products.index') }}" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control search-bar" placeholder="Cari produk atau jasa..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="sort" class="form-select">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama A-Z</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Filter Pills -->
    <div class="mb-4">
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('products.index') }}" class="filter-pill text-decoration-none {{ !request('type') ? 'active' : '' }}">
                Semua
            </a>
            <a href="{{ route('products.index', ['type' => 'product'] + request()->except('type')) }}" class="filter-pill text-decoration-none {{ request('type') == 'product' ? 'active' : '' }}">
                🛍️ Produk
            </a>
            <a href="{{ route('products.index', ['type' => 'service'] + request()->except('type')) }}" class="filter-pill text-decoration-none {{ request('type') == 'service' ? 'active' : '' }}">
                💼 Jasa
            </a>
        </div>
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="row g-3 g-lg-4 mb-4">
            @foreach($products as $product)
                <div class="col-md-6 col-lg-4 col-xl-3">
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
                            <h5 class="card-title h6 mb-2">{{ $product->name }}</h5>
                            <p class="text-secondary small mb-2">
                                <a href="{{ route('umkms.show', $product->umkm->slug) }}" class="text-decoration-none text-secondary">
                                    {{ $product->umkm->name }}
                                </a>
                            </p>
                            @if($product->category)
                                <span class="badge bg-light text-dark border mb-2">{{ $product->category->name }}</span>
                            @endif
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="badge badge-price">{{ $product->formatted_price }}</span>
                                <a href="{{ route('products.show', [$product->umkm->slug, $product->slug]) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <div class="display-1 mb-3">🔍</div>
            <h3 class="h5 text-secondary">Tidak ada produk ditemukan</h3>
            <p class="text-secondary">Coba ubah filter atau kata kunci pencarian Anda</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">Lihat Semua Produk</a>
        </div>
    @endif
</div>
@endsection
