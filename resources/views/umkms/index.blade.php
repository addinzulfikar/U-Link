@extends('layouts.app')

@section('title', 'Jelajahi UMKM - U-LINK')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 fw-bold mb-3">Jelajahi UMKM</h1>
            <p class="text-secondary">Temukan berbagai UMKM lokal yang menawarkan produk dan jasa berkualitas</p>
        </div>
    </div>

    @if($umkms->count() > 0)
        <div class="row g-3 g-lg-4 mb-4">
            @foreach($umkms as $umkm)
                <div class="col-md-6 col-lg-4">
                    <div class="card umkm-card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex gap-3 mb-3">
                                <div class="umkm-logo bg-light border d-flex align-items-center justify-content-center">
                                    <span class="fs-3">🏪</span>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1">{{ $umkm->name }}</h5>
                                    @if($umkm->city)
                                        <p class="text-secondary small mb-0">📍 {{ $umkm->city }}</p>
                                    @endif
                                </div>
                            </div>

                            @if($umkm->description)
                                <p class="text-secondary small mb-3">{{ Str::limit($umkm->description, 100) }}</p>
                            @endif

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-secondary">
                                    {{ $umkm->products()->count() }} produk/jasa
                                </small>
                                <a href="{{ route('umkms.show', $umkm->slug) }}" class="btn btn-sm btn-primary">Lihat Toko</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center">
            {{ $umkms->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <div class="display-1 mb-3">🏪</div>
            <h3 class="h5 text-secondary">Belum ada UMKM terdaftar</h3>
        </div>
    @endif
</div>
@endsection
