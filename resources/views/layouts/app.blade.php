<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'U-LINK - Platform UMKM')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-body-tertiary">
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top" data-bs-theme="light">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">U-LINK</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <div class="ms-auto d-flex align-items-lg-center gap-2 pt-3 pt-lg-0">
                    @auth
                        @php
                            $dashboardUrl = null;
                            if (Auth::user()->isSuperAdmin()) {
                                $dashboardUrl = route('dashboard.super-admin');
                            } elseif (Auth::user()->isAdminToko()) {
                                $dashboardUrl = route('dashboard.admin-toko');
                            } elseif (Auth::user()->isUser()) {
                                $dashboardUrl = route('dashboard.user');
                            }
                        @endphp

                        <span class="text-muted small">{{ Auth::user()->name }}</span>
                        @if ($dashboardUrl)
                            <a href="{{ $dashboardUrl }}" class="btn btn-outline-primary btn-sm">Dashboard</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="py-5 ulink-page">
        @yield('content')
    </main>

    <footer class="border-top bg-white">
        <div class="container py-4 text-center text-secondary">
            <small>&copy; 2024 U-LINK. Platform Sharing UMKM Indonesia.</small>
        </div>
    </footer>
</body>
</html>
