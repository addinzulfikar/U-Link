@extends('layouts.app')

@section('title', 'Dashboard Admin Toko - U-LINK')

@section('content')
<div class="container">
    <div class="mb-4 d-flex justify-content-between align-items-start gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1">Dashboard Admin Toko</h1>
            <div class="text-secondary">Selamat datang, <span class="fw-semibold">{{ Auth::user()->name }}</span></div>
        </div>

        @include('partials.logout-button')
    </div>

    @if(!$umkm)
        <!-- No UMKM Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5 text-center">
                <div class="display-1 mb-3">🏪</div>
                <h3 class="fw-bold mb-3">Belum Punya UMKM?</h3>
                <p class="text-secondary mb-4">Daftarkan UMKM Anda sekarang dan mulai promosikan produk/jasa Anda!</p>
                <a href="{{ route('umkm.create') }}" class="btn btn-primary btn-lg">Daftar UMKM</a>
            </div>
        </div>
    @else
        <!-- UMKM Status -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-2">{{ $umkm->name }}</h5>
                        <div>
                            Status: 
                            @if($umkm->status == 'approved')
                                <span class="badge bg-success">✓ Disetujui</span>
                            @elseif($umkm->status == 'pending')
                                <span class="badge bg-warning">⏳ Menunggu Persetujuan</span>
                            @else
                                <span class="badge bg-danger">✗ Ditolak</span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('umkm.manage') }}" class="btn btn-primary">Kelola UMKM</a>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card stat-card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-secondary small mb-1">Produk</div>
                        <div class="h3 fw-bold mb-0">{{ $stats['total_products'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-secondary small mb-1">Jasa</div>
                        <div class="h3 fw-bold mb-0">{{ $stats['total_services'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-secondary small mb-1">Favorit</div>
                        <div class="h3 fw-bold mb-0">{{ $stats['total_favorites'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-secondary small mb-1">Rating</div>
                        <div class="h3 fw-bold mb-0">{{ number_format($stats['average_rating'], 1) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Products -->
        @if($recentProducts->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Produk/Jasa Terbaru</h5>
                        @if($umkm->isApproved())
                            <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary">+ Tambah</a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Tipe</th>
                                    <th>Harga</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentProducts as $product)
                                    <tr>
                                        <td class="fw-semibold">{{ $product->name }}</td>
                                        <td>
                                            @if($product->type == 'product')
                                                <span class="badge bg-primary">Produk</span>
                                            @else
                                                <span class="badge bg-info">Jasa</span>
                                            @endif
                                        </td>
                                        <td>{{ $product->formatted_price }}</td>
                                        <td>
                                            @if($product->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('umkm.manage') }}" class="btn btn-outline-primary">Lihat Semua</a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3">Aksi Cepat</h5>
                <div class="row g-3">
                    @if($umkm->isApproved())
                        <div class="col-md-6">
                            <a href="{{ route('products.create') }}" class="card border text-decoration-none h-100 hover-shadow">
                                <div class="card-body">
                                    <div class="h2 mb-2">➕</div>
                                    <h6 class="card-title">Tambah Produk/Jasa</h6>
                                    <p class="card-text small text-secondary mb-0">Tambahkan produk atau jasa baru</p>
                                </div>
                            </a>
                        </div>
                    @endif
                    <div class="col-md-6">
                        <a href="{{ route('umkm.edit') }}" class="card border text-decoration-none h-100 hover-shadow">
                            <div class="card-body">
                                <div class="h2 mb-2">✏️</div>
                                <h6 class="card-title">Edit Profil UMKM</h6>
                                <p class="card-text small text-secondary mb-0">Perbarui informasi UMKM</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
