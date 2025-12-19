@extends('layouts.app')

@section('title', 'Kelola UMKM - U-LINK')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0">Kelola UMKM</h1>
        <a href="{{ route('dashboard.super-admin') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <!-- Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-auto">
                    <label class="form-label mb-0 small">Status:</label>
                </div>
                <div class="col-auto">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- UMKMs Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama UMKM</th>
                            <th>Pemilik</th>
                            <th>Kota</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($umkms as $umkm)
                            <tr>
                                <td class="fw-semibold">{{ $umkm->name }}</td>
                                <td>{{ $umkm->owner->name }}</td>
                                <td>{{ $umkm->city ?? '-' }}</td>
                                <td>
                                    @if($umkm->status == 'approved')
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif($umkm->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>{{ $umkm->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('umkms.show', $umkm->slug) }}" class="btn btn-outline-secondary" target="_blank">Lihat</a>
                                        @if($umkm->status == 'pending')
                                            <form method="POST" action="{{ route('admin.umkms.approve', $umkm->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success">Setuju</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.umkms.reject', $umkm->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-danger">Tolak</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-secondary">Tidak ada UMKM</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $umkms->links() }}
    </div>
</div>
@endsection
