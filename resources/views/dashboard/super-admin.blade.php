@extends('layouts.admin')

@section('title', 'Dashboard Super Admin - U-LINK')

@section('page-title', 'Dashboard Super Admin')

@section('sidebar')
    @include('partials.admin.sidebar-super-admin')
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Welcome Section -->
    <div class="mb-8">
        <p class="text-sm text-gray-500">Selamat datang, <span class="font-semibold text-gray-900">{{ Auth::user()->name }}</span></p>
    </div>

    <!-- Platform Stats - Xero-style: Clean numbers -->
    <div class="mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="text-xs text-gray-500 mb-2 font-normal">Total Users</div>
                <div class="text-3xl font-semibold text-gray-900 mb-2">{{ $stats['total_users'] }}</div>
                <a href="{{ route('admin.users') }}" class="text-sm text-primary hover:text-primary-dark font-medium">Kelola →</a>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="text-xs text-gray-500 mb-2 font-normal">UMKM Aktif</div>
                <div class="text-3xl font-semibold text-gray-900 mb-2">{{ $stats['total_umkms'] }}</div>
                <a href="{{ route('admin.umkms') }}" class="text-sm text-primary hover:text-primary-dark font-medium">Kelola →</a>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="text-xs text-gray-500 mb-2 font-normal">Pending UMKM</div>
                <div class="text-3xl font-semibold text-gray-900 mb-2">{{ $stats['pending_umkms'] }}</div>
                <a href="{{ route('admin.umkms', ['status' => 'pending']) }}" class="text-sm text-primary hover:text-primary-dark font-medium">Review →</a>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="text-xs text-gray-500 mb-2 font-normal">Total Produk</div>
                <div class="text-3xl font-semibold text-gray-900">{{ $stats['total_products'] }}</div>
            </div>
        </div>
    </div>

    <!-- Pending UMKM - Xero-style: Table secondary -->
    @if($pendingUmkms->count() > 0)
        <div class="bg-white border border-gray-200 rounded-lg mb-6">
            <div class="border-b border-gray-200 px-6 py-4">
                <div class="flex justify-between items-center">
                    <h5 class="text-base font-semibold text-gray-900">UMKM Menunggu Persetujuan</h5>
                    <a href="{{ route('admin.umkms', ['status' => 'pending']) }}" class="text-sm font-medium text-primary hover:text-primary-dark">Lihat Semua</a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama UMKM</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemilik</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kota</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pendingUmkms as $umkm)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $umkm->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $umkm->owner->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $umkm->city ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $umkm->created_at->diffForHumans() }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('admin.umkms.approve', $umkm->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-700 hover:text-green-900 font-medium" onclick="return confirm('Setujui UMKM ini?')">Setuju</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.umkms.reject', $umkm->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-700 hover:text-red-900 font-medium" onclick="return confirm('Tolak UMKM ini?')">Tolak</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Quick Actions - Xero-style: Minimal -->
    <div class="bg-white border border-gray-200 rounded-lg p-6">
        <h5 class="text-base font-semibold text-gray-900 mb-4">Manajemen</h5>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.users') }}" class="block bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg p-4 transition-colors">
                <h6 class="text-sm font-semibold text-gray-900 mb-1">Kelola Users</h6>
                <p class="text-xs text-gray-500">Kelola semua pengguna platform</p>
            </a>
            <a href="{{ route('admin.umkms') }}" class="block bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg p-4 transition-colors">
                <h6 class="text-sm font-semibold text-gray-900 mb-1">Kelola UMKM</h6>
                <p class="text-xs text-gray-500">Review dan kelola UMKM</p>
            </a>
            <a href="{{ route('admin.categories') }}" class="block bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg p-4 transition-colors">
                <h6 class="text-sm font-semibold text-gray-900 mb-1">Kelola Kategori</h6>
                <p class="text-xs text-gray-500">Tambah dan edit kategori</p>
            </a>
        </div>
    </div>
</div>
@endsection
