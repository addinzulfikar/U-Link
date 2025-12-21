@extends('layouts.admin')

@section('title', 'Admin Dashboard - U-LINK')

@section('page-title', 'Admin Dashboard')

@section('sidebar')
    @include('partials.admin.sidebar-super-admin')
@endsection

@section('content')
<div>
    <div class="mb-4">
        <p class="text-secondary mb-0">Selamat datang, <span class="fw-semibold">{{ Auth::user()->name }}</span></p>
    </div>

    <!-- Platform Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-secondary small mb-1">Total Users</div>
                    <div class="h3 fw-bold mb-0">{{ $stats['total_users'] }}</div>
                    <a href="{{ route('admin.users') }}" class="text-decoration-none small">Kelola →</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-secondary small mb-1">Admin Toko</div>
                    <div class="h3 fw-bold mb-0">{{ $stats['total_admin_toko'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-secondary small mb-1">Total UMKM</div>
                    <div class="h3 fw-bold mb-0">{{ $stats['total_umkms'] }}</div>
                    <a href="{{ route('admin.umkms') }}" class="text-decoration-none small">Kelola →</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
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
                    <h5 class="fw-bold mb-0">UMKM Menunggu Persetujuan ({{ $stats['pending_umkms'] }})</h5>
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

    <!-- Status Overview -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="text-secondary mb-3">Status UMKM</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small">Pending</span>
                        <span class="fw-bold">{{ $stats['pending_umkms'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="small">Disetujui</span>
                        <span class="fw-bold text-success">{{ $stats['approved_umkms'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="text-secondary mb-3">Aksi Cepat</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-primary">
                            <span class="admin-nav-icon">👥</span> Kelola Users
                        </a>
                        <a href="{{ route('admin.umkms') }}" class="btn btn-outline-primary">
                            <span class="admin-nav-icon">🏪</span> Kelola UMKM
                        </a>
                        <a href="{{ route('admin.categories') }}" class="btn btn-outline-primary">
                            <span class="admin-nav-icon">📁</span> Kelola Kategori
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
