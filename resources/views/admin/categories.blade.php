@extends('layouts.admin')

@section('title', 'Kelola Kategori - U-LINK')

@section('page-title', 'Kelola Kategori')

@section('sidebar')
    @include('partials.admin.sidebar-super-admin')
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
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $category->id }}">
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</button>
                                        </form>
                                    </div>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Kategori</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form method="POST" action="{{ route('admin.categories.update', $category->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Kategori</label>
                                                            <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Deskripsi</label>
                                                            <input type="text" name="description" class="form-control" value="{{ $category->description }}">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
