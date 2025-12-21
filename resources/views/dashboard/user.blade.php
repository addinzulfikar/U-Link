@extends('layouts.app')

@section('title', 'Dashboard User - U-LINK')

@section('content')
<div class="container mx-auto px-4">
    <!-- Welcome Section - Xero-style: Clean header -->
    <div class="mb-8 flex justify-between items-start gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 mb-1">Dashboard</h1>
            <div class="text-sm text-gray-500">Selamat datang, <span class="font-medium text-gray-900">{{ Auth::user()->name }}</span></div>
        </div>

        @include('partials.logout-button')
    </div>

    <!-- Financial Snapshot - Xero-style: Numbers-focused, no icons -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="text-xs text-gray-500 mb-2 font-normal">UMKM Favorit</div>
            <div class="text-3xl font-semibold text-gray-900 mb-2">{{ $favoriteCount }}</div>
            <a href="{{ route('favorites.index') }}" class="text-sm text-primary hover:text-primary-dark font-medium">Lihat semua →</a>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="text-xs text-gray-500 mb-2 font-normal">Jelajahi UMKM</div>
            <div class="text-sm text-gray-900 mb-2 font-medium">Platform UMKM</div>
            <a href="{{ route('umkms.index') }}" class="text-sm text-primary hover:text-primary-dark font-medium">Jelajahi →</a>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="text-xs text-gray-500 mb-2 font-normal">Produk & Jasa</div>
            <div class="text-sm text-gray-900 mb-2 font-medium">Semua Katalog</div>
            <a href="{{ route('products.index') }}" class="text-sm text-primary hover:text-primary-dark font-medium">Lihat semua →</a>
        </div>
    </div>

    <!-- Recent Products - Xero-style: Clean grid -->
    @if($recentProducts->count() > 0)
        <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">Produk & Jasa Terbaru</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($recentProducts as $product)
                    <div class="product-card">
                        <div class="relative">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="product-card-img" alt="{{ $product->name }}">
                            @else
                                <div class="product-card-placeholder">
                                    {{ $product->type == 'product' ? '🛍️' : '💼' }}
                                </div>
                            @endif
                            @if($product->type == 'service')
                                <span class="badge-tokped absolute top-2 right-2">Jasa</span>
                            @endif
                        </div>
                        <div class="p-3">
                            <h5 class="text-sm font-medium text-gray-900 mb-1 truncate">{{ Str::limit($product->name, 30) }}</h5>
                            <p class="text-xs text-gray-500 mb-2 truncate">
                                <a href="{{ route('umkms.show', $product->umkm->slug) }}" class="hover:text-gray-700">
                                    {{ Str::limit($product->umkm->name, 20) }}
                                </a>
                            </p>
                            <div class="flex justify-between items-center">
                                <span class="badge-price text-xs">{{ $product->formatted_price }}</span>
                                <a href="{{ route('products.show', [$product->umkm->slug, $product->slug]) }}" class="text-xs text-primary hover:text-primary-dark font-medium">Lihat</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-6">
                <a href="{{ route('products.index') }}" class="inline-block bg-white hover:bg-gray-50 text-primary border border-gray-200 font-medium px-6 py-2 rounded-lg transition-colors text-sm">Lihat Semua Produk</a>
            </div>
        </div>
    @endif

    <!-- Quick Actions - Xero-style: Minimal, no heavy icons -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h5 class="text-base font-semibold text-gray-900 mb-4">Aksi Cepat</h5>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('umkms.index') }}" class="block bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg p-4 transition-colors">
                <h6 class="text-sm font-semibold text-gray-900 mb-1">Jelajahi UMKM</h6>
                <p class="text-xs text-gray-500">Temukan berbagai UMKM lokal</p>
            </a>
            <a href="{{ route('products.index') }}" class="block bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg p-4 transition-colors">
                <h6 class="text-sm font-semibold text-gray-900 mb-1">Produk & Jasa</h6>
                <p class="text-xs text-gray-500">Lihat semua produk dan jasa</p>
            </a>
        </div>
    </div>
</div>
@endsection
