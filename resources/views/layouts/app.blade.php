<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'U-LINK - Platform UMKM')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="ulink-page min-h-screen flex flex-col font-sans text-text-primary @yield('bodyClass')">
    <!-- Xero-style: Clean, minimal navbar -->
    @unless (View::hasSection('chromeless'))
        <nav class="navbar navbar-expand sticky-top bg-background-paper border-b border-border shadow-xero-sm ulink-nav" data-bs-theme="light">
            <div class="mx-auto w-full max-w-screen-xl px-6 lg:px-10">
                <div class="flex w-full items-center py-3">
                    {{-- LEFT: Logo --}}
                    <div class="flex basis-1/3 items-center justify-start">
                        <a class="navbar-brand text-body font-semibold tracking-tight text-text-primary !no-underline" href="{{ url('/') }}">
                            <span class="text-primary">U</span>-LINK
                        </a>
                    </div>

                    {{-- CENTER: Primary navigation --}}
                    <div class="flex basis-1/3 items-center justify-center">
                        <a href="{{ route('umkms.index') }}" class="whitespace-nowrap rounded-xero px-4 py-2 text-body-sm font-semibold text-text-primary !no-underline hover:bg-background-subtle focus:outline-none focus:ring-2 focus:ring-primary/20">
                            Jelajahi UMKM
                        </a>
                    </div>

                    {{-- RIGHT: Akun dropdown --}}
                    <div class="flex basis-1/3 items-center justify-end">
                        <span class="mx-2 hidden h-6 w-px bg-border sm:block" aria-hidden="true"></span>
                        <ul class="navbar-nav mb-0 flex flex-row items-center gap-2">
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
                                <a class="nav-link dropdown-toggle text-body-sm font-normal text-text-secondary hover:text-text-primary" href="#" role="button"
                                   data-bs-toggle="dropdown" aria-expanded="false">
                                    Akun
                                    <span class="ms-1">{{ $user->name }}</span>
                                    <span class="badge ms-2" style="{{ $roleBadgeStyle }} font-size: 0.75rem; padding: 0.25rem 0.5rem;">{{ $roleLabel }}</span>
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end rounded-xero border border-border shadow-xero-sm ulink-dropdown">
                                    <li>
                                        <a class="dropdown-item text-body-sm font-normal text-text-secondary" href="{{ route('dashboard') }}">Dashboard</a>
                                    </li>

                                    @if($user->isUser())
                                        <li>
                                            <a class="dropdown-item text-body-sm font-normal text-text-secondary" href="{{ route('favorites.index') }}">Favorit Saya</a>
                                        </li>
                                    @endif

                                    @if($user->isAdminToko())
                                        <li><hr class="dropdown-divider" style="border-color: #E5E7EB;"></li>
                                        <li><a class="dropdown-item text-body-sm font-normal text-text-secondary" href="{{ route('umkm.manage') }}">Kelola UMKM</a></li>
                                        <li><a class="dropdown-item text-body-sm font-normal text-text-secondary" href="{{ route('products.create') }}">Tambah Produk/Jasa</a></li>
                                        <li><a class="dropdown-item text-body-sm font-normal text-text-secondary" href="{{ route('spreadsheet.analyzer') }}">Upload Data Keuangan</a></li>
                                    @endif

                                    @if($user->isSuperAdmin())
                                        <li><hr class="dropdown-divider" style="border-color: #E5E7EB;"></li>
                                        <li><a class="dropdown-item text-body-sm font-normal text-text-secondary" href="{{ route('admin.index') }}">Admin Dashboard</a></li>
                                        <li><a class="dropdown-item text-body-sm font-normal text-text-secondary" href="{{ route('admin.users') }}">Kelola Users</a></li>
                                        <li><a class="dropdown-item text-body-sm font-normal text-text-secondary" href="{{ route('admin.umkms') }}">Kelola UMKM</a></li>
                                        <li><a class="dropdown-item text-body-sm font-normal text-text-secondary" href="{{ route('admin.categories') }}">Kelola Kategori</a></li>
                                    @endif

                                    <li><hr class="dropdown-divider" style="border-color: #E5E7EB;"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-body-sm" style="color: #DC2626;">
                                                Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-body-sm font-normal text-text-secondary hover:text-text-primary" href="#" role="button"
                                   data-bs-toggle="dropdown" aria-expanded="false">
                                    Akun
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end rounded-xero border border-border shadow-xero-sm ulink-dropdown">
                                    <li><a class="dropdown-item text-body-sm font-normal text-text-secondary" href="{{ route('login') }}">Login</a></li>
                                    <li><a class="dropdown-item text-body-sm font-normal text-text-secondary" href="{{ route('register') }}">Daftar</a></li>
                                </ul>
                            </li>
                        @endauth
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    @endunless

    @unless (View::hasSection('chromeless'))
        @if(session('success'))
            <div class="mx-auto w-full max-w-screen-xl px-6 lg:px-10 mt-4">
                <div class="alert alert-dismissible fade show" role="alert" style="background: #D1FAE5; border: 1px solid #059669; color: #065F46; border-radius: 8px;">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mx-auto w-full max-w-screen-xl px-6 lg:px-10 mt-4">
                <div class="alert alert-dismissible fade show" role="alert" style="background: #FEE2E2; border: 1px solid #DC2626; color: #991B1B; border-radius: 8px;">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
    @endunless

    <main class="{{ View::hasSection('chromeless') ? 'flex-1' : 'flex-1 py-10 lg:py-12' }}">
        @yield('content')
    </main>

    @unless (View::hasSection('chromeless'))
        <footer class="mt-auto bg-background-subtle border-t border-border">
            <div class="mx-auto w-full max-w-screen-xl px-6 py-10 lg:px-10 lg:py-12">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-12 md:items-start md:gap-x-10">
                    <div class="md:col-span-4 flex flex-col items-start text-left">
                        <div class="text-body font-semibold tracking-tight text-text-primary"><span class="text-primary">U</span>-LINK</div>
                        <div class="mt-1 text-body-sm text-text-secondary">Platform sharing UMKM Indonesia.</div>
                    </div>

                    <div class="md:col-span-4 flex flex-col items-center text-center">
                        <div class="text-body font-semibold leading-tight text-text-primary">Bantuan &amp; Panduan</div>
                        <ul class="mt-4 m-0 flex list-none flex-col items-center gap-3 p-0">
                            <li>
                                <a href="#" class="inline-block text-body-sm font-medium leading-snug !text-primary !no-underline hover:!text-primary-dark">Syarat &amp; Ketentuan</a>
                            </li>
                            <li>
                                <a href="#" class="inline-block text-body-sm font-medium leading-snug !text-primary !no-underline hover:!text-primary-dark">Kebijakan Privasi</a>
                            </li>
                        </ul>
                    </div>

                    <div class="md:col-span-4 md:flex md:flex-col md:items-end md:text-right">
                        <div class="text-body-sm font-semibold text-text-primary">CS : <a class="!text-primary !no-underline hover:!text-primary-dark" href="mailto:cs@u-link.local">cs@u-link.local</a></div>
                    </div>
                </div>

                <div class="mt-10 text-center text-caption text-text-secondary">
                    &copy; 2025 U-LINK. Platform Sharing UMKM Indonesia.
                </div>
            </div>
        </footer>
    @endunless
</body>
</html>
