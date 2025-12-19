@extends('layouts.app')

@section('title', 'Kelola UMKM - U-LINK')

@section('content')
<div class="container">
    <!-- UMKM Status Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-auto">
                    <div class="umkm-logo bg-light border d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                        <span class="fs-2">🏪</span>
                    </div>
                </div>
                <div class="col-md">
                    <h3 class="fw-bold mb-2">{{ $umkm->name }}</h3>
                    <p class="text-secondary mb-2">
                        Status: 
                        @if($umkm->status == 'approved')
                            <span class="badge bg-success">✓ Disetujui</span>
                        @elseif($umkm->status == 'pending')
                            <span class="badge bg-warning">⏳ Menunggu Persetujuan</span>
                        @else
                            <span class="badge bg-danger">✗ Ditolak</span>
                        @endif
                    </p>
                    @if($umkm->city)
                        <p class="text-secondary mb-0">📍 {{ $umkm->city }}</p>
                    @endif
                </div>
                <div class="col-md-auto">
                    <a href="{{ route('umkm.edit') }}" class="btn btn-outline-primary">Edit Profil</a>
                    <a href="{{ route('umkms.show', $umkm->slug) }}" class="btn btn-outline-secondary" target="_blank">Lihat Halaman</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-secondary small mb-1">Total Produk</div>
                    <div class="h3 fw-bold mb-0">{{ $stats['total_products'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-secondary small mb-1">Total Jasa</div>
                    <div class="h3 fw-bold mb-0">{{ $stats['total_services'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-secondary small mb-1">Favorit</div>
                    <div class="h3 fw-bold mb-0">{{ $stats['total_favorites'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Produk & Jasa</h5>
                @if($umkm->isApproved())
                    <a href="{{ route('products.create') }}" class="btn btn-primary">+ Tambah Produk/Jasa</a>
                @endif
            </div>
        </div>
        <div class="card-body p-4">
            @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Tipe</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $product->name }}</div>
                                        @if($product->description)
                                            <small class="text-secondary">{{ Str::limit($product->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->type == 'product')
                                            <span class="badge bg-primary">Produk</span>
                                        @else
                                            <span class="badge bg-info">Jasa</span>
                                        @endif
                                    </td>
                                    <td>{{ $product->category->name ?? '-' }}</td>
                                    <td>{{ $product->formatted_price }}</td>
                                    <td>
                                        @if($product->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('products.show', [$umkm->slug, $product->slug]) }}" class="btn btn-outline-secondary" target="_blank">Lihat</a>
                                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-outline-primary">Edit</a>
                                            <form method="POST" action="{{ route('products.destroy', $product->id) }}" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="display-1 mb-3">📦</div>
                    <h5 class="text-secondary mb-3">Belum ada produk atau jasa</h5>
                    @if($umkm->isApproved())
                        <a href="{{ route('products.create') }}" class="btn btn-primary">Tambah Produk/Jasa Pertama</a>
                    @else
                        <p class="text-secondary">Tunggu UMKM Anda disetujui untuk menambah produk/jasa</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
