@extends('layouts.app')

@section('title', 'Daftar - U-LINK')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7 col-xl-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="h3 fw-bold text-center mb-2">Buat Akun</h1>
                    <p class="text-center text-secondary mb-4">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="link-primary text-decoration-none fw-semibold">Login di sini</a>
                    </p>

                    <form action="{{ route('register') }}" method="POST" class="row g-3">
                        @csrf

                        <div class="col-12">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input id="name" name="name" type="text" required
                                   class="form-control form-control-lg @error('name') is-invalid @enderror"
                                   placeholder="Nama Lengkap" value="{{ old('name') }}" autocomplete="name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" name="email" type="email" required
                                   class="form-control form-control-lg @error('email') is-invalid @enderror"
                                   placeholder="nama@email.com" value="{{ old('email') }}" autocomplete="email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" name="password" type="password" required
                                   class="form-control form-control-lg @error('password') is-invalid @enderror"
                                   placeholder="Minimal 8 karakter" autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                   class="form-control form-control-lg" placeholder="Ketik ulang password" autocomplete="new-password">
                        </div>

                        <div class="col-12">
                            <label for="role" class="form-label">Tipe Akun</label>
                            <select id="role" name="role" required class="form-select form-select-lg @error('role') is-invalid @enderror">
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User (Pembeli)</option>
                                <option value="admin_toko" {{ old('role') == 'admin_toko' ? 'selected' : '' }}>Admin Toko (UMKM)</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-lg w-100">Daftar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
