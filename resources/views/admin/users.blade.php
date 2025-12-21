@extends('layouts.admin')

@section('title', 'Kelola Users - U-LINK')

@section('page-title', 'Kelola Users')

@section('sidebar')
    @include('partials.admin.sidebar-super-admin')
@endsection

@section('content')
<div>
    <!-- Add User Button -->
    <div class="mb-4">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah User Baru
        </a>
    </div>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Tanggal Daftar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="fw-semibold">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role == 'user')
                                        <span class="badge bg-primary">User</span>
                                    @elseif($user->role == 'admin_toko')
                                        <span class="badge bg-warning text-dark">Admin Toko</span>
                                    @else
                                        <span class="badge bg-danger">Super Admin</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                                <td>
                                    @if($user->umkm)
                                        <a href="{{ route('umkms.show', $user->umkm->slug) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                            Lihat UMKM
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-primary">Edit</a>
                                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-secondary">Tidak ada user</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
