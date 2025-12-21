<div class="admin-nav-section">Dashboard</div>
<a href="{{ route('dashboard.admin-toko') }}" class="admin-nav-item {{ request()->routeIs('dashboard.admin-toko') ? 'active' : '' }}">
    <span class="admin-nav-icon">📊</span> Overview
</a>

@php
    $umkmOpen = request()->routeIs('umkm.*') || request()->routeIs('products.create') || request()->routeIs('spreadsheet.analyzer');
@endphp
<div class="admin-nav-section">UMKM Saya</div>
<button
    class="admin-nav-item admin-nav-toggle"
    type="button"
    data-bs-toggle="collapse"
    data-bs-target="#nav-umkm"
    aria-expanded="{{ $umkmOpen ? 'true' : 'false' }}"
    aria-controls="nav-umkm"
>
    <span class="admin-nav-icon">🏪</span> UMKM Saya
    <span class="admin-nav-caret">▾</span>
</button>
<div class="collapse {{ $umkmOpen ? 'show' : '' }}" id="nav-umkm">
    <div class="admin-nav-sub">
        <a href="{{ route('umkm.manage') }}" class="admin-nav-item {{ request()->routeIs('umkm.manage') ? 'active' : '' }}">
            <span class="admin-nav-icon">🛠️</span> Kelola UMKM
        </a>
        @if(isset($umkm) && $umkm && $umkm->isApproved())
            <a href="{{ route('products.create') }}" class="admin-nav-item {{ request()->routeIs('products.create') ? 'active' : '' }}">
                <span class="admin-nav-icon">➕</span> Tambah Produk/Jasa
            </a>
        @endif
        <a href="{{ route('spreadsheet.analyzer') }}" class="admin-nav-item {{ request()->routeIs('spreadsheet.analyzer') ? 'active' : '' }}">
            <span class="admin-nav-icon">📈</span> Analisis Spreadsheet
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
            <span class="admin-nav-icon">🛍️</span> Jelajahi UMKM
        </a>
        <a href="{{ route('products.index') }}" class="admin-nav-item {{ request()->routeIs('products.index') ? 'active' : '' }}">
            <span class="admin-nav-icon">📦</span> Produk & Jasa
        </a>
    </div>
</div>
