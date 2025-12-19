@extends('layouts.admin')

@section('title', 'Kelola Kategori - U-LINK')

@section('page-title', 'Kelola Kategori')

@section('sidebar')
    <div class="admin-nav-section">Dashboard</div>
    <a href="{{ route('dashboard.super-admin') }}" class="admin-nav-item">
        <span class="admin-nav-icon">📊</span> Overview
    </a>
    
    <div class="admin-nav-section">Manajemen</div>
    <a href="{{ route('admin.users') }}" class="admin-nav-item">
        <span class="admin-nav-icon">👥</span> Kelola Users
    </a>
    <a href="{{ route('admin.umkms') }}" class="admin-nav-item">
        <span class="admin-nav-icon">🏪</span> Kelola UMKM
    </a>
    <a href="{{ route('admin.categories') }}" class="admin-nav-item active">
        <span class="admin-nav-icon">📁</span> Kelola Kategori
    </a>
    
    <div class="admin-nav-section">Lainnya</div>
    <a href="{{ route('umkms.index') }}" class="admin-nav-item">
        <span class="admin-nav-icon">🛍️</span> Lihat UMKM
    </a>
    <a href="{{ route('products.index') }}" class="admin-nav-item">
        <span class="admin-nav-icon">📦</span> Lihat Produk
    </a>
@endsection

@section('content')
<div>

    <!-- Add Category Form -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h5 class="fw-semibold mb-3">Tambah Kategori Baru</h5>
            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama Kategori" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="description" class="form-control" placeholder="Deskripsi (opsional)" value="{{ old('description') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Categories List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Slug</th>
                            <th>Deskripsi</th>
                            <th>Jumlah Produk</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td class="fw-semibold">{{ $category->name }}</td>
                                <td><code>{{ $category->slug }}</code></td>
                                <td>{{ $category->description ?? '-' }}</td>
                                <td>{{ $category->products_count }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-secondary">Belum ada kategori</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $categories->links() }}
    </div>
</div>
@endsection
