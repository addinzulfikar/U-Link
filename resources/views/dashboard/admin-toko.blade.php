@extends('layouts.app')

@section('title', 'Dashboard Admin Toko - U-LINK')

@section('content')
<div class="container">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Dashboard Admin Toko (UMKM)</h1>
            <div class="text-secondary">Selamat datang, <span class="fw-semibold">{{ Auth::user()->name }}</span></div>
        </div>
        <span class="badge text-bg-success-subtle border border-success-subtle text-success-emphasis px-3 py-2">Role: Admin Toko</span>
    </div>

    <div class="row g-3 g-lg-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h5 fw-semibold mb-3">Fitur untuk Anda</h2>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0">Mengelola profil toko UMKM</div>
                        <div class="list-group-item px-0">Menambah dan mengedit produk/jasa</div>
                        <div class="list-group-item px-0">Melihat statistik toko</div>
                        <div class="list-group-item px-0">Merespon review dari pelanggan</div>
                        <div class="list-group-item px-0">Promosikan dagangan Anda</div>
                    </div>

                    <div class="mt-4">
                        <a href="#" class="btn btn-success btn-lg">+ Tambah Produk/Jasa</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="row g-3">
                <div class="col-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-3 p-lg-4">
                            <div class="text-secondary small">Produk Saya</div>
                            <div class="display-6 fw-bold mb-0">0</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-3 p-lg-4">
                            <div class="text-secondary small">Jasa Saya</div>
                            <div class="display-6 fw-bold mb-0">0</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-3 p-lg-4">
                            <div class="text-secondary small">Pengunjung</div>
                            <div class="display-6 fw-bold mb-0">0</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-3 p-lg-4">
                            <div class="text-secondary small">Rating</div>
                            <div class="display-6 fw-bold mb-0">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
