@extends('layouts.app')

@section('title', 'Login - U-LINK')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="h3 fw-bold text-center mb-2">Login</h1>
                    <p class="text-center text-secondary mb-4">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="link-primary text-decoration-none fw-semibold">Daftar sekarang</a>
                    </p>

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST" class="vstack gap-3">
                        @csrf

                        <div>
                            <label for="email" class="form-label">Email</label>
                            <input id="email" name="email" type="email" required
                                   class="form-control form-control-lg" placeholder="nama@email.com"
                                   value="{{ old('email') }}" autocomplete="email">
                        </div>

                        <div>
                            <label for="password" class="form-label">Password</label>
                            <input id="password" name="password" type="password" required
                                   class="form-control form-control-lg" placeholder="Password"
                                   autocomplete="current-password">
                        </div>

                        <div class="form-check">
                            <input id="remember" name="remember" type="checkbox" class="form-check-input">
                            <label for="remember" class="form-check-label">Ingat saya</label>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
