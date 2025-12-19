@extends('layouts.app')

@section('title', 'Dashboard Super Admin - U-LINK')

@section('content')
<div class="container">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Dashboard Super Admin</h1>
            <div class="text-secondary">Selamat datang, <span class="fw-semibold">{{ Auth::user()->name }}</span></div>
        </div>
        <span class="badge text-bg-danger-subtle border border-danger-subtle text-danger-emphasis px-3 py-2">Role: Super Admin</span>
    </div>

    <div class="row g-3 g-lg-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h5 fw-semibold mb-3">Fitur untuk Anda</h2>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0">Mengelola semua pengguna (User, Admin Toko)</div>
                        <div class="list-group-item px-0">Moderasi konten produk dan jasa</div>
                        <div class="list-group-item px-0">Melihat statistik platform secara keseluruhan</div>
                        <div class="list-group-item px-0">Mengelola kategori produk/jasa</div>
                        <div class="list-group-item px-0">Mengelola pengaturan sistem</div>
                        <div class="list-group-item px-0">Verifikasi dan approve UMKM baru</div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap mt-4">
                        <a href="#" class="btn btn-primary">Kelola User</a>
                        <a href="#" class="btn btn-success">Kelola UMKM</a>
                        <a href="#" class="btn btn-warning">Moderasi Konten</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="row g-3">
                <div class="col-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-3 p-lg-4">
                            <div class="text-secondary small">Total User</div>
                            <div class="display-6 fw-bold mb-0">0</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-3 p-lg-4">
                            <div class="text-secondary small">Total UMKM</div>
                            <div class="display-6 fw-bold mb-0">0</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-3 p-lg-4">
                            <div class="text-secondary small">Total Produk</div>
                            <div class="display-6 fw-bold mb-0">0</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-3 p-lg-4">
                            <div class="text-secondary small">UMKM Pending</div>
                            <div class="display-6 fw-bold mb-0">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
