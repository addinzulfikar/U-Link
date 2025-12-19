@extends('layouts.app')

@section('title', 'U-LINK - Platform Sharing UMKM')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="p-4 p-lg-5 rounded-4 shadow-sm ulink-card">
                <div class="row align-items-center g-4">
                    <div class="col-lg-7">
                        <p class="text-uppercase small text-secondary mb-2">Platform UMKM Indonesia</p>
                        <h1 class="display-5 fw-bold mb-3">Bangun bisnis UMKM kamu dengan cara yang modern.</h1>
                        <p class="lead text-secondary mb-4">
                            U-LINK membantu UMKM saling berbagi, mempromosikan produk/jasa, dan terhubung dengan pelanggan.
                        </p>

                        @guest
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Mulai Sekarang</a>
                                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">Login</a>
                            </div>
                        @endguest

                        <div class="mt-4">
                            @if (Str::startsWith($db_version, 'Error:'))
                                <div class="alert alert-danger mb-0" role="alert">
                                    <strong>Koneksi Database:</strong>
                                    <span class="font-monospace">{{ Str::limit($db_version, 160) }}</span>
                                </div>
                            @else
                                <div class="alert alert-success mb-0" role="alert">
                                    <strong>Koneksi Database OK:</strong>
                                    <span class="font-monospace">{{ Str::limit($db_version, 160) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="p-4 rounded-4 bg-white border">
                            <h2 class="h5 fw-semibold mb-3">Apa yang kamu dapat?</h2>
                            <div class="d-grid gap-3">
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="rounded-3 p-2 bg-primary-subtle border border-primary-subtle">
                                        <div class="fw-bold text-primary">1</div>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">Eksposur lebih luas</div>
                                        <div class="text-secondary small">Promosikan produk/jasa dan jangkau pelanggan baru.</div>
                                    </div>
                                </div>
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="rounded-3 p-2 bg-success-subtle border border-success-subtle">
                                        <div class="fw-bold text-success">2</div>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">Kelola katalog</div>
                                        <div class="text-secondary small">Tambah, edit, dan atur produk/jasa dengan rapi.</div>
                                    </div>
                                </div>
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="rounded-3 p-2 bg-info-subtle border border-info-subtle">
                                        <div class="fw-bold text-info">3</div>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">Dipercaya pengguna</div>
                                        <div class="text-secondary small">Sistem verifikasi & moderasi menjaga kualitas.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 g-lg-4 mt-3">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h3 class="h5 card-title mb-2">Untuk User</h3>
                            <p class="card-text text-secondary">Cari produk dan jasa UMKM lokal, dukung bisnis sekitar, dan kasih review.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h3 class="h5 card-title mb-2">Untuk UMKM</h3>
                            <p class="card-text text-secondary">Bangun profil UMKM, promosi gratis, dan kelola toko online kamu.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h3 class="h5 card-title mb-2">Aman & Tertata</h3>
                            <p class="card-text text-secondary">Proses verifikasi membantu menjaga kualitas informasi produk/jasa.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
