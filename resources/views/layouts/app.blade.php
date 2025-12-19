<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'U-LINK - Platform UMKM')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-body-tertiary">
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top shadow-sm" data-bs-theme="light">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="{{ url('/') }}">
                <span class="text-primary">U</span>-LINK
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('umkms.index') }}">Jelajahi UMKM</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}">Produk & Jasa</a>
                    </li>
                </ul>

                <div class="d-flex align-items-lg-center gap-2 pt-3 pt-lg-0">
                    @auth
                        @php
                            $dashboardUrl = null;
                            $dashboardLabel = 'Dashboard';
                            if (Auth::user()->isSuperAdmin()) {
                                $dashboardUrl = route('dashboard.super-admin');
                                $dashboardLabel = 'Admin Panel';
                            } elseif (Auth::user()->isAdminToko()) {
                                $dashboardUrl = route('dashboard.admin-toko');
                                $dashboardLabel = 'Kelola Toko';
                            } elseif (Auth::user()->isUser()) {
                                $dashboardUrl = route('dashboard.user');
                            }
                        @endphp

                        @if(Auth::user()->isUser())
                            <a href="{{ route('favorites.index') }}" class="btn btn-sm btn-outline-secondary">
                                <span>❤️</span> Favorit
                            </a>
                        @endif

                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if ($dashboardUrl)
                                    <li><a class="dropdown-item" href="{{ $dashboardUrl }}">{{ $dashboardLabel }}</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-primary">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <main class="py-4 ulink-page">
        @yield('content')
    </main>

    <footer class="border-top bg-white mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="fw-bold mb-3"><span class="text-primary">U</span>-LINK</h5>
                    <p class="text-secondary">Platform untuk UMKM Indonesia saling berbagi dan mempromosikan produk serta jasa mereka.</p>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-semibold mb-3">Navigasi</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('umkms.index') }}" class="text-decoration-none text-secondary">Jelajahi UMKM</a></li>
                        <li><a href="{{ route('products.index') }}" class="text-decoration-none text-secondary">Produk & Jasa</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-semibold mb-3">Akun</h6>
                    <ul class="list-unstyled">
                        @guest
                            <li><a href="{{ route('login') }}" class="text-decoration-none text-secondary">Login</a></li>
                            <li><a href="{{ route('register') }}" class="text-decoration-none text-secondary">Daftar</a></li>
                        @else
                            <li><a href="{{ $dashboardUrl ?? '#' }}" class="text-decoration-none text-secondary">Dashboard</a></li>
                        @endguest
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center text-secondary">
                <small>&copy; 2024 U-LINK. Platform Sharing UMKM Indonesia.</small>
            </div>
        </div>
    </footer>
</body>
</html>
