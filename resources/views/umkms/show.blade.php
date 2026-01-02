@extends('layouts.app')

@section('title', $umkm->name . ' - U-LINK')

@section('content')
<div class="container">
    <!-- UMKM Header -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-auto">
                    <div class="umkm-logo bg-light border d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                        <span class="fs-1">🏪</span>
                    </div>
                </div>
                <div class="col-md">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h1 class="h3 fw-bold mb-2">{{ $umkm->name }}</h1>
                            <p class="text-secondary mb-0">
                                @if($umkm->city || $umkm->province)
                                    📍 {{ $umkm->city }}{{ $umkm->province ? ', ' . $umkm->province : '' }}
                                @endif
                            </p>
                            @if($umkm->phone)
                                <p class="text-secondary mb-0">📞 {{ $umkm->phone }}</p>
                            @endif
                        </div>
                        @auth
                            <div class="d-flex gap-2">
                                @if($umkm->owner && Auth::user()->canChatWith($umkm->owner))
                                    <a
                                        href="{{ url('/' . config('chatify.routes.prefix') . '/' . $umkm->owner->id) }}"
                                        class="btn btn-outline-primary"
                                    >💬 Chat Admin</a>
                                @endif

                                @if(Auth::user()->isUser())
                                    <form method="POST" action="{{ route('favorites.toggle', $umkm->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-{{ $isFavorited ? 'danger' : 'outline-danger' }}">
                                            {{ $isFavorited ? '❤️ Favorit' : '🤍 Tambah Favorit' }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endauth
                    </div>
                    @if($umkm->description)
                        <p class="text-secondary mt-3 mb-0">{{ $umkm->description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Products -->
    <div class="mb-4">
        <h4 class="fw-bold mb-3">Produk & Jasa</h4>

        @if($umkm->products->count() > 0)
            <div class="row g-3 g-lg-4">
                @foreach($umkm->products as $product)
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
                                @if($product->category)
                                    <span class="badge bg-light text-dark border mb-2">{{ $product->category->name }}</span>
                                @endif
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="badge badge-price">{{ $product->formatted_price }}</span>
                                    <a href="{{ route('products.show', [$umkm->slug, $product->slug]) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <div class="display-1 mb-3">📦</div>
                <p class="text-secondary">Belum ada produk atau jasa yang ditawarkan</p>
            </div>
        @endif
    </div>
</div>
@endsection
