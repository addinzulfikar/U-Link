@extends('layouts.app')

@section('title', 'Dashboard Super Admin - U-LINK')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1">Dashboard Super Admin</h1>
        <div class="text-secondary">Selamat datang, <span class="fw-semibold">{{ Auth::user()->name }}</span></div>
    </div>

    <!-- Platform Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-secondary small mb-1">Total Users</div>
                    <div class="h3 fw-bold mb-0">{{ $stats['total_users'] }}</div>
                    <a href="{{ route('admin.users') }}" class="stretched-link text-decoration-none small">Kelola →</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-secondary small mb-1">UMKM Aktif</div>
                    <div class="h3 fw-bold mb-0">{{ $stats['total_umkms'] }}</div>
                    <a href="{{ route('admin.umkms') }}" class="stretched-link text-decoration-none small">Kelola →</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-secondary small mb-1">Pending UMKM</div>
                    <div class="h3 fw-bold mb-0">{{ $stats['pending_umkms'] }}</div>
                    <a href="{{ route('admin.umkms', ['status' => 'pending']) }}" class="stretched-link text-decoration-none small">Review →</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-secondary small mb-1">Total Produk</div>
                    <div class="h3 fw-bold mb-0">{{ $stats['total_products'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending UMKM -->
    @if($pendingUmkms->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">UMKM Menunggu Persetujuan</h5>
                    <a href="{{ route('admin.umkms', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama UMKM</th>
                                <th>Pemilik</th>
                                <th>Kota</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingUmkms as $umkm)
                                <tr>
                                    <td class="fw-semibold">{{ $umkm->name }}</td>
                                    <td>{{ $umkm->owner->name }}</td>
                                    <td>{{ $umkm->city ?? '-' }}</td>
                                    <td>{{ $umkm->created_at->diffForHumans() }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <form method="POST" action="{{ route('admin.umkms.approve', $umkm->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success" onclick="return confirm('Setujui UMKM ini?')">✓ Setuju</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.umkms.reject', $umkm->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Tolak UMKM ini?')">✗ Tolak</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h5 class="fw-semibold mb-3">Manajemen</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <a href="{{ route('admin.users') }}" class="card border text-decoration-none h-100 hover-shadow">
                        <div class="card-body">
                            <div class="h2 mb-2">👥</div>
                            <h6 class="card-title">Kelola Users</h6>
                            <p class="card-text small text-secondary mb-0">Kelola semua pengguna platform</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('admin.umkms') }}" class="card border text-decoration-none h-100 hover-shadow">
                        <div class="card-body">
                            <div class="h2 mb-2">🏪</div>
                            <h6 class="card-title">Kelola UMKM</h6>
                            <p class="card-text small text-secondary mb-0">Review dan kelola UMKM</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('admin.categories') }}" class="card border text-decoration-none h-100 hover-shadow">
                        <div class="card-body">
                            <div class="h2 mb-2">📁</div>
                            <h6 class="card-title">Kelola Kategori</h6>
                            <p class="card-text small text-secondary mb-0">Tambah dan edit kategori</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
