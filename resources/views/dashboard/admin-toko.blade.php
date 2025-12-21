@extends('layouts.admin')

@section('title', 'Dashboard Admin Toko - U-LINK')

@section('page-title', 'Dashboard Admin Toko')

@section('sidebar')
    @include('partials.admin.sidebar-admin-toko')
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Welcome Section - Xero-style: Minimal, calm -->
    <div class="mb-8">
        <p class="text-sm text-gray-500">Selamat datang, <span class="font-semibold text-gray-900">{{ Auth::user()->name }}</span></p>
    </div>

    @if(!$umkm)
        <!-- No UMKM Card - Xero-style: Clean, centered, minimal -->
        <div class="bg-white border border-gray-200 rounded-lg p-12 text-center">
            <div class="text-6xl mb-4">🏪</div>
            <h3 class="text-2xl font-semibold text-gray-900 mb-3">Belum Punya UMKM?</h3>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">Daftarkan UMKM Anda sekarang dan mulai promosikan produk/jasa Anda!</p>
            <a href="{{ route('umkm.create') }}" class="inline-block bg-primary hover:bg-primary-dark text-white font-medium px-6 py-2.5 rounded-lg transition-colors">Daftar UMKM</a>
        </div>
    @else
        <!-- UMKM Status - Xero-style: Subtle, minimal badge -->
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h5 class="text-lg font-semibold text-gray-900 mb-2">{{ $umkm->name }}</h5>
                    <div class="text-sm text-gray-600">
                        Status: 
                        @if($umkm->status == 'approved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-50 text-green-800 border border-green-200">Disetujui</span>
                        @elseif($umkm->status == 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-yellow-50 text-yellow-800 border border-yellow-200">Menunggu Persetujuan</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-red-50 text-red-800 border border-red-200">Ditolak</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('umkm.manage') }}" class="bg-primary hover:bg-primary-dark text-white font-medium px-5 py-2 rounded-lg transition-colors text-sm">Kelola UMKM</a>
            </div>
        </div>

        <!-- Financial Snapshot - Xero-style: Numbers first, minimal design -->
        <div class="mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Produk -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="text-xs text-gray-500 mb-2 font-normal">Produk</div>
                    <div class="text-3xl font-semibold text-gray-900">{{ $stats['total_products'] }}</div>
                </div>
                
                <!-- Jasa -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="text-xs text-gray-500 mb-2 font-normal">Jasa</div>
                    <div class="text-3xl font-semibold text-gray-900">{{ $stats['total_services'] }}</div>
                </div>
                
                <!-- Favorit -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="text-xs text-gray-500 mb-2 font-normal">Favorit</div>
                    <div class="text-3xl font-semibold text-gray-900">{{ $stats['total_favorites'] }}</div>
                </div>
                
                <!-- Rating -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="text-xs text-gray-500 mb-2 font-normal">Rating</div>
                    <div class="text-3xl font-semibold text-gray-900">{{ number_format($stats['average_rating'], 1) }}</div>
                </div>
            </div>
        </div>

        <!-- Recent Products - Xero-style: Table is secondary, minimal -->
        @if($recentProducts->count() > 0)
            <div class="bg-white border border-gray-200 rounded-lg mb-6">
                <div class="border-b border-gray-200 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <h5 class="text-base font-semibold text-gray-900">Produk/Jasa Terbaru</h5>
                        @if($umkm->isApproved())
                            <a href="{{ route('products.create') }}" class="text-sm font-medium text-primary hover:text-primary-dark">+ Tambah</a>
                        @endif
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentProducts as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($product->type == 'product')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">Produk</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">Jasa</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->formatted_price }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($product->is_active)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-50 text-green-700">Aktif</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('products.edit', $product->id) }}" class="text-primary hover:text-primary-dark font-medium">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-200 px-6 py-4 text-center">
                    <a href="{{ route('umkm.manage') }}" class="text-sm font-medium text-primary hover:text-primary-dark">Lihat Semua</a>
                </div>
            </div>
        @endif

        <!-- Quick Actions - Xero-style: Minimal, no icons -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h5 class="text-base font-semibold text-gray-900 mb-4">Aksi Cepat</h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($umkm->isApproved())
                    <a href="{{ route('products.create') }}" class="block bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg p-4 transition-colors">
                        <h6 class="text-sm font-semibold text-gray-900 mb-1">Tambah Produk/Jasa</h6>
                        <p class="text-xs text-gray-500">Tambahkan produk atau jasa baru</p>
                    </a>
                @endif
                <a href="{{ route('umkm.edit') }}" class="block bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg p-4 transition-colors">
                    <h6 class="text-sm font-semibold text-gray-900 mb-1">Edit Profil UMKM</h6>
                    <p class="text-xs text-gray-500">Perbarui informasi UMKM</p>
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
