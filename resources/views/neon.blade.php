@extends('layouts.app')

@section('title', 'U-LINK - Platform Sharing UMKM')

@section('content')
<div class="mx-auto w-full max-w-screen-xl px-6 lg:px-10">
    <div class="rounded-2xl border border-border bg-background-paper shadow-xero-sm">
        <div class="grid grid-cols-1 gap-12 p-8 sm:p-10 lg:grid-cols-12 lg:items-center">
                <div class="lg:col-span-7">
                    <p class="text-caption font-medium uppercase tracking-wide text-text-tertiary">Platform UMKM Indonesia</p>
                    <h1 class="mt-3 max-w-2xl text-h1 font-semibold tracking-tight text-text-primary sm:text-display">Bangun bisnis UMKM kamu dengan cara yang modern.</h1>
                    <p class="mt-4 max-w-2xl text-body-lg leading-relaxed text-text-secondary">
                        U-LINK memudahkan UMKM mempromosikan produk/jasa dan terhubung dengan pelanggan.
                    </p>

                    @guest
                        <div class="mt-7 flex flex-wrap items-center gap-3">
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-full bg-primary px-7 py-3.5 text-body font-semibold text-white shadow-xero-sm !no-underline hover:!no-underline hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/30">
                                Mulai Sekarang
                            </a>
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-full border border-border bg-background-paper px-7 py-3.5 text-body font-semibold text-text-primary !no-underline hover:!no-underline hover:bg-background-subtle focus:outline-none focus:ring-2 focus:ring-primary/20">
                                Login
                            </a>
                        </div>
                    @endguest
                </div>

                <div class="lg:col-span-5">
                    <div class="rounded-2xl border border-border bg-background-subtle p-6">
                        <h2 class="text-h3 text-text-primary">Sekilas U-LINK</h2>
                        <p class="mt-2 text-body-sm text-text-secondary">Temukan UMKM lokal, tampilkan katalog, dan percepat interaksi pelanggan.</p>
                        <div class="mt-4 space-y-4">
                            <div class="flex gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-border bg-background-subtle">
                                    <span class="text-body font-semibold text-text-tertiary">1</span>
                                </div>
                                <div>
                                    <div class="text-body font-semibold text-text-primary">Jelajah</div>
                                    <div class="mt-1 text-body-sm text-text-secondary">UMKM dan produk/jasa di satu tempat.</div>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-border bg-background-subtle">
                                    <span class="text-body font-semibold text-text-tertiary">2</span>
                                </div>
                                <div>
                                    <div class="text-body font-semibold text-text-primary">Kelola</div>
                                    <div class="mt-1 text-body-sm text-text-secondary">Profil UMKM dan katalog yang rapi.</div>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-border bg-background-subtle">
                                    <span class="text-body font-semibold text-text-tertiary">3</span>
                                </div>
                                <div>
                                    <div class="text-body font-semibold text-text-primary">Terhubung</div>
                                    <div class="mt-1 text-body-sm text-text-secondary">Lebih cepat dengan calon pelanggan.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
@endsection
