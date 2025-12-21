<div class="admin-nav-section">Dashboard</div>
@php
    $isAdminIndex = request()->routeIs('admin.index');
    $isSuperAdminDashboard = request()->routeIs('dashboard.super-admin');
    $dashboardHref = request()->routeIs('admin.*') ? route('admin.index') : route('dashboard.super-admin');
@endphp
<a href="{{ $dashboardHref }}" class="admin-nav-item {{ ($isAdminIndex || $isSuperAdminDashboard) ? 'active' : '' }}">
    <span class="admin-nav-icon">📊</span> Overview
</a>

@php
    $managementOpen = request()->routeIs('admin.users') || request()->routeIs('admin.umkms') || request()->routeIs('admin.categories');
@endphp
<div class="admin-nav-section">Manajemen</div>
<button
    class="admin-nav-item admin-nav-toggle"
    type="button"
    data-bs-toggle="collapse"
    data-bs-target="#nav-manajemen"
    aria-expanded="{{ $managementOpen ? 'true' : 'false' }}"
    aria-controls="nav-manajemen"
>
    <span class="admin-nav-icon">🧰</span> Manajemen
    <span class="admin-nav-caret">▾</span>
</button>
<div class="collapse {{ $managementOpen ? 'show' : '' }}" id="nav-manajemen">
    <div class="admin-nav-sub">
        <a href="{{ route('admin.users') }}" class="admin-nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
            <span class="admin-nav-icon">👥</span> Kelola Users
        </a>
        <a href="{{ route('admin.umkms') }}" class="admin-nav-item {{ request()->routeIs('admin.umkms') ? 'active' : '' }}">
            <span class="admin-nav-icon">🏪</span> Kelola UMKM
        </a>
        <a href="{{ route('admin.categories') }}" class="admin-nav-item {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
            <span class="admin-nav-icon">📁</span> Kelola Kategori
        </a>
    </div>
</div>

@php
    $othersOpen = request()->routeIs('umkms.*') || request()->routeIs('products.index') || request()->routeIs('products.show');
@endphp
<div class="admin-nav-section">Lainnya</div>
<button
    class="admin-nav-item admin-nav-toggle"
    type="button"
    data-bs-toggle="collapse"
    data-bs-target="#nav-lainnya"
    aria-expanded="{{ $othersOpen ? 'true' : 'false' }}"
    aria-controls="nav-lainnya"
>
    <span class="admin-nav-icon">🧭</span> Navigasi
    <span class="admin-nav-caret">▾</span>
</button>
<div class="collapse {{ $othersOpen ? 'show' : '' }}" id="nav-lainnya">
    <div class="admin-nav-sub">
        <a href="{{ route('umkms.index') }}" class="admin-nav-item {{ request()->routeIs('umkms.index') ? 'active' : '' }}">
            <span class="admin-nav-icon">🛍️</span> Lihat UMKM
        </a>
        <a href="{{ route('products.index') }}" class="admin-nav-item {{ request()->routeIs('products.index') ? 'active' : '' }}">
            <span class="admin-nav-icon">📦</span> Lihat Produk
        </a>
    </div>
</div>
