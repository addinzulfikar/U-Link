@extends('layouts.app')

@section('title', 'Daftar - U-LINK')

@section('chromeless')
@endsection

@section('bodyClass', 'bg-text-primary text-background-paper')

@section('content')
<div class="flex min-h-screen items-center justify-center px-6 py-14">
    <div class="w-full max-w-md">
        <div class="mb-6 text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 !no-underline hover:!no-underline">
                <span class="text-body font-semibold tracking-tight text-background-paper">
                    <span class="text-primary">U</span>-LINK
                </span>
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-body-sm text-border" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl border border-white/10 bg-white/5 shadow-xero">
            <div class="p-6 sm:p-7">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h1 class="text-h3 text-background-paper">Buat akun</h1>
                        <p class="mt-2 text-body-sm text-border">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="font-semibold !text-background-paper !no-underline hover:!no-underline hover:opacity-90">Login</a>
                        </p>
                    </div>
                </div>

                <form action="{{ route('register') }}" method="POST" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="mb-2 block text-caption font-semibold uppercase tracking-wide text-border">Nama lengkap</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            required
                            class="w-full rounded-full border border-white/10 bg-text-primary/40 px-4 py-3 text-body text-background-paper placeholder:text-border focus:border-primary/50 focus:outline-none focus:ring-2 focus:ring-primary/25"
                            placeholder="Nama Lengkap"
                            value="{{ old('name') }}"
                            autocomplete="name"
                        >
                        @error('name')
                            <p class="mt-2 text-body-sm text-border">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-caption font-semibold uppercase tracking-wide text-border">Email</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            required
                            class="w-full rounded-full border border-white/10 bg-text-primary/40 px-4 py-3 text-body text-background-paper placeholder:text-border focus:border-primary/50 focus:outline-none focus:ring-2 focus:ring-primary/25"
                            placeholder="nama@email.com"
                            value="{{ old('email') }}"
                            autocomplete="email"
                        >
                        @error('email')
                            <p class="mt-2 text-body-sm text-border">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label for="password" class="mb-2 block text-caption font-semibold uppercase tracking-wide text-border">Password</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                class="w-full rounded-full border border-white/10 bg-text-primary/40 px-4 py-3 text-body text-background-paper placeholder:text-border focus:border-primary/50 focus:outline-none focus:ring-2 focus:ring-primary/25"
                                placeholder="Minimal 8 karakter"
                                autocomplete="new-password"
                            >
                            @error('password')
                                <p class="mt-2 text-body-sm text-border">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-2 block text-caption font-semibold uppercase tracking-wide text-border">Konfirmasi</label>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                class="w-full rounded-full border border-white/10 bg-text-primary/40 px-4 py-3 text-body text-background-paper placeholder:text-border focus:border-primary/50 focus:outline-none focus:ring-2 focus:ring-primary/25"
                                placeholder="Ketik ulang"
                                autocomplete="new-password"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="role" class="mb-2 block text-caption font-semibold uppercase tracking-wide text-border">Tipe akun</label>
                        <select
                            id="role"
                            name="role"
                            required
                            class="w-full rounded-full border border-white/10 bg-text-primary/40 px-4 py-3 text-body text-background-paper focus:border-primary/50 focus:outline-none focus:ring-2 focus:ring-primary/25"
                        >
                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User (Pembeli)</option>
                            <option value="admin_toko" {{ old('role') == 'admin_toko' ? 'selected' : '' }}>Admin Toko (UMKM)</option>
                        </select>
                        @error('role')
                            <p class="mt-2 text-body-sm text-border">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full rounded-full bg-primary px-4 py-3 text-body font-semibold text-white shadow-xero-sm hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/30">
                        Daftar
                    </button>
                </form>
            </div>
        </div>

        <p class="mt-6 text-center text-caption text-border">
            Pastikan email aktif untuk menerima informasi akun.
        </p>
    </div>
</div>
@endsection
