<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'U-LINK - Platform UMKM')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-body-tertiary">
    <nav class="navbar navbar-expand bg-white border-bottom sticky-top shadow-sm" data-bs-theme="light">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="{{ url('/') }}">
                <span class="text-primary">U</span>-LINK
            </a>

            <div class="navbar-collapse">

                {{-- LEFT: Navigasi dropdown --}}
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-semibold" href="#" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Navigasi
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('umkms.index') }}">🏪 Jelajahi UMKM</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('products.index') }}">🛍️ Produk & Jasa</a>
                            </li>

                            @auth
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    {{-- entry point dashboard untuk semua role --}}
                                    <a class="dropdown-item" href="{{ route('dashboard') }}">📊 Dashboard</a>
                                </li>

                                @if(Auth::user()->isUser())
                                    <li>
                                        <a class="dropdown-item" href="{{ route('favorites.index') }}">❤️ Favorit Saya</a>
                                    </li>
                                @endif

                                @if(Auth::user()->isAdminToko())
                                    <li>
                                        <a class="dropdown-item" href="{{ route('umkm.manage') }}">🏷️ Kelola UMKM</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('products.create') }}">➕ Tambah Produk/Jasa</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('spreadsheet.analyzer') }}">📈 Analisis Spreadsheet</a>
                                    </li>
                                @endif

                                @if(Auth::user()->isSuperAdmin())
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.index') }}">🛡️ Admin Dashboard</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.users') }}">👤 Kelola Users</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.umkms') }}">🏪 Kelola UMKM</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.categories') }}">📁 Kelola Kategori</a>
                                    </li>
                                @endif
                            @endauth
                        </ul>
                    </li>
                </ul>

                {{-- RIGHT: Akun dropdown --}}
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    @auth
                        @php
                            $user = Auth::user();

                            $roleLabel = 'User';
                            $roleBadge = 'bg-primary';

                            if ($user->isAdminToko()) {
                                $roleLabel = 'Admin Toko';
                                $roleBadge = 'bg-warning text-dark';
                            } elseif ($user->isSuperAdmin()) {
                                $roleLabel = 'Super Admin';
                                $roleBadge = 'bg-danger';
                            }
                        @endphp

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-semibold" href="#" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                Akun
                                <span class="ms-1">{{ $user->name }}</span>
                                <span class="badge {{ $roleBadge }} ms-2">{{ $roleLabel }}</span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard') }}">📊 Dashboard</a>
                                </li>

                                @if($user->isUser())
                                    <li>
                                        <a class="dropdown-item" href="{{ route('favorites.index') }}">❤️ Favorit Saya</a>
                                    </li>
                                @endif

                                @if($user->isAdminToko())
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('umkm.manage') }}">🏷️ Kelola UMKM</a></li>
                                    <li><a class="dropdown-item" href="{{ route('products.create') }}">➕ Tambah Produk/Jasa</a></li>
                                    <li><a class="dropdown-item" href="{{ route('spreadsheet.analyzer') }}">📈 Analisis Spreadsheet</a></li>
                                @endif

                                @if($user->isSuperAdmin())
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.index') }}">🛡️ Admin Dashboard</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.users') }}">👤 Kelola Users</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.umkms') }}">🏪 Kelola UMKM</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.categories') }}">📁 Kelola Kategori</a></li>
                                @endif

                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-semibold" href="#" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                Akun
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('login') }}">Login</a></li>
                                <li><a class="dropdown-item" href="{{ route('register') }}">Daftar</a></li>
                            </ul>
                        </li>
                    @endauth
                </ul>
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
                            <li><a href="{{ route('dashboard') }}" class="text-decoration-none text-secondary">Dashboard</a></li>
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
