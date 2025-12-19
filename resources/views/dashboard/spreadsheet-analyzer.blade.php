@extends('layouts.admin')

@section('title', 'Analisis Spreadsheet - U-LINK')

@section('page-title', 'Analisis Spreadsheet')

@section('sidebar')
    <div class="admin-nav-section">Dashboard</div>
    <a href="{{ route('dashboard.admin-toko') }}" class="admin-nav-item">
        <span class="admin-nav-icon">📊</span> Overview
    </a>
    
    <div class="admin-nav-section">UMKM Saya</div>
    <a href="{{ route('umkm.manage') }}" class="admin-nav-item">
        <span class="admin-nav-icon">🏪</span> Kelola UMKM
    </a>
    @if(isset($umkm) && $umkm && $umkm->isApproved())
        <a href="{{ route('products.create') }}" class="admin-nav-item">
            <span class="admin-nav-icon">➕</span> Tambah Produk/Jasa
        </a>
    @endif
    <a href="{{ route('spreadsheet.analyzer') }}" class="admin-nav-item active">
        <span class="admin-nav-icon">📈</span> Analisis Spreadsheet
    </a>
    
    <div class="admin-nav-section">Lainnya</div>
    <a href="{{ route('umkms.index') }}" class="admin-nav-item">
        <span class="admin-nav-icon">🛍️</span> Jelajahi UMKM
    </a>
    <a href="{{ route('products.index') }}" class="admin-nav-item">
        <span class="admin-nav-icon">📦</span> Produk & Jasa
    </a>
@endsection

@section('content')
    @livewire('spreadsheet-analyzer')
@endsection
