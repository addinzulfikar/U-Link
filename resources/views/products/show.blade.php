@extends('layouts.app')

@section('title', $product->name . ' - U-LINK')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
            <li class="breadcrumb-item"><a href="{{ route('umkms.show', $product->umkm->slug) }}">{{ $product->umkm->name }}</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Product Image -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" style="height: 400px; object-fit: cover;" alt="{{ $product->name }}">
                @else
                    <div class="d-flex align-items-center justify-content-center bg-light" style="height: 400px;">
                        <div class="text-center">
                            <div style="font-size: 6rem;">{{ $product->type == 'product' ? '🛍️' : '💼' }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-lg-7">
            <div class="mb-3">
                @if($product->type == 'service')
                    <span class="badge badge-tokped mb-2">Jasa</span>
                @endif
                @if($product->category)
                    <span class="badge bg-light text-dark border mb-2">{{ $product->category->name }}</span>
                @endif
            </div>

            <h1 class="h3 fw-bold mb-3">{{ $product->name }}</h1>

            <!-- Rating -->
            @if($totalReviews > 0)
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="rating-stars">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($averageRating))
                                ⭐
                            @else
                                ☆
                            @endif
                        @endfor
                    </div>
                    <span class="text-secondary">{{ number_format($averageRating, 1) }} ({{ $totalReviews }} review{{ $totalReviews > 1 ? 's' : '' }})</span>
                </div>
            @endif

            <!-- Price -->
            <div class="mb-4">
                <h2 class="h2 fw-bold text-success mb-0">{{ $product->formatted_price }}</h2>
                @if($product->type == 'product' && $product->stock)
                    <small class="text-secondary">Stok: {{ $product->stock }}</small>
                @endif
            </div>

            <!-- UMKM Info -->
            <div class="card border-0 bg-light mb-4">
                <div class="card-body">
                    <h6 class="fw-semibold mb-2">Dijual oleh:</h6>
                    <div class="d-flex align-items-center gap-3">
                        <div class="umkm-logo bg-white border"></div>
                        <div>
                            <a href="{{ route('umkms.show', $product->umkm->slug) }}" class="text-decoration-none fw-semibold">
                                {{ $product->umkm->name }}
                            </a>
                            @if($product->umkm->city)
                                <div class="small text-secondary">📍 {{ $product->umkm->city }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <h5 class="fw-semibold mb-3">Deskripsi</h5>
                <p class="text-secondary">{!! nl2br(e($product->description ?? 'Tidak ada deskripsi.')) !!}</p>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="row mt-5">
        <div class="col-12">
            <h4 class="fw-bold mb-4">Ulasan Produk</h4>

            @auth
                @if(Auth::user()->isUser())
                    @if(!$userReview)
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body">
                                <h6 class="fw-semibold mb-3">Tulis Ulasan</h6>
                                <form method="POST" action="{{ route('reviews.store', $product->id) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Rating</label>
                                        <div class="d-flex gap-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="rating" value="{{ $i }}" id="rating{{ $i }}" required>
                                                    <label class="form-check-label" for="rating{{ $i }}">{{ $i }} ⭐</label>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Komentar (opsional)</label>
                                        <textarea name="comment" class="form-control" rows="3" maxlength="1000"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            Anda sudah memberikan ulasan untuk produk ini.
                        </div>
                    @endif
                @endif
            @else
                <div class="alert alert-secondary">
                    <a href="{{ route('login') }}">Login</a> untuk memberikan ulasan.
                </div>
            @endauth

            <!-- Reviews List -->
            @if($product->reviews->count() > 0)
                <div class="mt-4">
                    @foreach($product->reviews as $review)
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-semibold mb-1">{{ $review->user->name }}</h6>
                                        <div class="rating-stars small">
                                            @for($i = 1; $i <= 5; $i++)
                                                {{ $i <= $review->rating ? '⭐' : '☆' }}
                                            @endfor
                                        </div>
                                    </div>
                                    <small class="text-secondary">{{ $review->created_at->diffForHumans() }}</small>
                                </div>
                                @if($review->comment)
                                    <p class="mb-0 text-secondary">{{ $review->comment }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-secondary">Belum ada ulasan untuk produk ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
