@extends('layouts.app')

@section('title', 'Dashboard User - U-LINK')

@section('content')
<div class="container">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Dashboard User</h1>
            <div class="text-secondary">Selamat datang, <span class="fw-semibold">{{ Auth::user()->name }}</span></div>
        </div>
        <span class="badge text-bg-primary-subtle border border-primary-subtle text-primary-emphasis px-3 py-2">Role: User</span>
    </div>

    <div class="row g-3 g-lg-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h2 class="h5 fw-semibold mb-3">Fitur untuk Anda</h2>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            Melihat produk dan jasa dari UMKM
                            <span class="badge text-bg-light border">Browse</span>
                        </div>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            Mencari UMKM berdasarkan kategori
                            <span class="badge text-bg-light border">Search</span>
                        </div>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            Menyimpan UMKM favorit
                            <span class="badge text-bg-light border">Save</span>
                        </div>
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            Memberikan review dan rating
                            <span class="badge text-bg-light border">Review</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="row g-3">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="text-secondary small">Produk UMKM</div>
                                    <div class="h5 fw-semibold mb-0">Jelajahi Produk</div>
                                </div>
                                <span class="badge text-bg-primary">Popular</span>
                            </div>
                            <p class="text-secondary mb-0 mt-2">Temukan berbagai produk dari UMKM lokal.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="text-secondary small">Jasa UMKM</div>
                                    <div class="h5 fw-semibold mb-0">Cari Layanan</div>
                                </div>
                                <span class="badge text-bg-success">New</span>
                            </div>
                            <p class="text-secondary mb-0 mt-2">Cari berbagai layanan jasa dari UMKM.</p>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="text-secondary small">Favorit Saya</div>
                                    <div class="h5 fw-semibold mb-0">Simpan UMKM</div>
                                </div>
                                <span class="badge text-bg-warning">Pinned</span>
                            </div>
                            <p class="text-secondary mb-0 mt-2">UMKM yang Anda simpan untuk nanti.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
