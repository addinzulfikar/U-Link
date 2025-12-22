<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'U-LINK - Platform UMKM')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background: #F9FAFB; font-family: 'Inter', sans-serif;">
    <!-- Xero-style: Clean, minimal navbar -->
    <nav class="navbar navbar-expand bg-white sticky-top" style="border-bottom: 1px solid #E5E7EB;" data-bs-theme="light">
        <div class="container">
            <a class="navbar-brand" style="font-weight: 600; font-size: 1.5rem; color: #111827;" href="{{ url('/') }}">
                <span style="color: #1F73B7;">U</span>-LINK
            </a>

            <div class="navbar-collapse">

                {{-- LEFT: Navigasi dropdown --}}
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" style="font-weight: 500; color: #6B7280; font-size: 0.875rem;" href="#" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Navigasi
                        </a>
                        <ul class="dropdown-menu" style="border: 1px solid #E5E7EB; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                            <li>
                                <a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('umkms.index') }}">Jelajahi UMKM</a>
                            </li>
                            <li>
                                <a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('products.index') }}">Produk & Jasa</a>
                            </li>

                            @auth
                                <li><hr class="dropdown-divider" style="border-color: #E5E7EB;"></li>
                                <li>
                                    <a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('dashboard') }}">Dashboard</a>
                                </li>

                                @if(Auth::user()->isUser())
                                    <li>
                                        <a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('favorites.index') }}">Favorit Saya</a>
                                    </li>
                                @endif

                                @if(Auth::user()->isAdminToko())
                                    <li>
                                        <a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('umkm.manage') }}">Kelola UMKM</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('products.create') }}">Tambah Produk/Jasa</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('spreadsheet.analyzer') }}">Input Data</a>
                                    </li>
                                @endif

                                @if(Auth::user()->isSuperAdmin())
                                    <li><hr class="dropdown-divider" style="border-color: #E5E7EB;"></li>
                                    <li>
                                        <a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('admin.index') }}">Admin Dashboard</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('admin.users') }}">Kelola Users</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('admin.umkms') }}">Kelola UMKM</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('admin.categories') }}">Kelola Kategori</a>
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
                            $roleBadgeStyle = 'background: #EFF6FF; color: #1E40AF;';

                            if ($user->isAdminToko()) {
                                $roleLabel = 'Admin Toko';
                                $roleBadgeStyle = 'background: #FEF3C7; color: #92400E;';
                            } elseif ($user->isSuperAdmin()) {
                                $roleLabel = 'Super Admin';
                                $roleBadgeStyle = 'background: #FEE2E2; color: #991B1B;';
                            }
                        @endphp

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" style="font-weight: 500; color: #6B7280; font-size: 0.875rem;" href="#" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                Akun
                                <span class="ms-1">{{ $user->name }}</span>
                                <span class="badge ms-2" style="{{ $roleBadgeStyle }} font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $roleLabel }}</span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end" style="border: 1px solid #E5E7EB; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                                <li>
                                    <a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('dashboard') }}">Dashboard</a>
                                </li>

                                @if($user->isUser())
                                    <li>
                                        <a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('favorites.index') }}">Favorit Saya</a>
                                    </li>
                                @endif

                                @if($user->isAdminToko())
                                    <li><hr class="dropdown-divider" style="border-color: #E5E7EB;"></li>
                                    <li><a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('umkm.manage') }}">Kelola UMKM</a></li>
                                    <li><a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('products.create') }}">Tambah Produk/Jasa</a></li>
                                    <li><a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('spreadsheet.analyzer') }}">Input Data</a></li>
                                @endif

                                @if($user->isSuperAdmin())
                                    <li><hr class="dropdown-divider" style="border-color: #E5E7EB;"></li>
                                    <li><a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('admin.index') }}">Admin Dashboard</a></li>
                                    <li><a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('admin.users') }}">Kelola Users</a></li>
                                    <li><a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('admin.umkms') }}">Kelola UMKM</a></li>
                                    <li><a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('admin.categories') }}">Kelola Kategori</a></li>
                                @endif

                                <li><hr class="dropdown-divider" style="border-color: #E5E7EB;"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item" style="font-size: 0.875rem; color: #DC2626;">
                                            Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" style="font-weight: 500; color: #6B7280; font-size: 0.875rem;" href="#" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                Akun
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" style="border: 1px solid #E5E7EB; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                                <li><a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('login') }}">Login</a></li>
                                <li><a class="dropdown-item" style="font-size: 0.875rem; color: #6B7280;" href="{{ route('register') }}">Daftar</a></li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-dismissible fade show" role="alert" style="background: #D1FAE5; border: 1px solid #059669; color: #065F46; border-radius: 8px;">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-dismissible fade show" role="alert" style="background: #FEE2E2; border: 1px solid #DC2626; color: #991B1B; border-radius: 8px;">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <main class="py-4 ulink-page">
        @yield('content')
    </main>

    <!-- Xero-style: Clean footer -->
    <footer class="bg-white mt-5" style="border-top: 1px solid #E5E7EB;">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3" style="font-weight: 600; color: #111827;"><span style="color: #1F73B7;">U</span>-LINK</h5>
                    <p style="color: #6B7280; font-size: 0.875rem;">Platform untuk UMKM Indonesia saling berbagi dan mempromosikan produk serta jasa mereka.</p>
                </div>
                <div class="col-md-3">
                    <h6 class="mb-3" style="font-weight: 600; color: #111827; font-size: 0.875rem;">Navigasi</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('umkms.index') }}" style="text-decoration: none; color: #6B7280; font-size: 0.875rem;">Jelajahi UMKM</a></li>
                        <li><a href="{{ route('products.index') }}" style="text-decoration: none; color: #6B7280; font-size: 0.875rem;">Produk & Jasa</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="mb-3" style="font-weight: 600; color: #111827; font-size: 0.875rem;">Akun</h6>
                    <ul class="list-unstyled">
                        @guest
                            <li><a href="{{ route('login') }}" style="text-decoration: none; color: #6B7280; font-size: 0.875rem;">Login</a></li>
                            <li><a href="{{ route('register') }}" style="text-decoration: none; color: #6B7280; font-size: 0.875rem;">Daftar</a></li>
                        @else
                            <li><a href="{{ route('dashboard') }}" style="text-decoration: none; color: #6B7280; font-size: 0.875rem;">Dashboard</a></li>
                        @endguest
                    </ul>
                </div>
            </div>
            <hr style="border-color: #E5E7EB; margin: 1.5rem 0;">
            <div class="text-center" style="color: #6B7280; font-size: 0.75rem;">
                &copy; 2024 U-LINK. Platform Sharing UMKM Indonesia.
            </div>
        </div>
    </footer>
</body>
</html>
