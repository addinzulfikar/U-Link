@extends('layouts.app')

@section('title', 'Favorit Saya - U-LINK')

@section('content')
<div class="container">
    <h1 class="h3 fw-bold mb-4">❤️ Favorit Saya</h1>

    @if($favorites->count() > 0)
        <div class="row g-3 g-lg-4 mb-4">
            @foreach($favorites as $favorite)
                <div class="col-md-6 col-lg-4">
                    <div class="card umkm-card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex gap-3 mb-3">
                                <div class="umkm-logo bg-light border d-flex align-items-center justify-content-center">
                                    <span class="fs-3">🏪</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1">{{ $favorite->umkm->name }}</h5>
                                    @if($favorite->umkm->city)
                                        <p class="text-secondary small mb-0">📍 {{ $favorite->umkm->city }}</p>
                                    @endif
                                </div>
                            </div>

                            @if($favorite->umkm->description)
                                <p class="text-secondary small mb-3">{{ Str::limit($favorite->umkm->description, 100) }}</p>
                            @endif

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('umkms.show', $favorite->umkm->slug) }}" class="btn btn-sm btn-primary">Lihat Toko</a>
                                <form method="POST" action="{{ route('favorites.toggle', $favorite->umkm->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center">
            {{ $favorites->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <div class="display-1 mb-3">💔</div>
            <h3 class="h5 text-secondary mb-3">Belum ada UMKM favorit</h3>
            <p class="text-secondary">Jelajahi UMKM dan tambahkan ke favorit Anda</p>
            <a href="{{ route('umkms.index') }}" class="btn btn-primary mt-3">Jelajahi UMKM</a>
        </div>
    @endif
</div>
@endsection
