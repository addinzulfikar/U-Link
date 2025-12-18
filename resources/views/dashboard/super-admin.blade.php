@extends('layouts.app')

@section('title', 'Dashboard Super Admin - U-LINK')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Dashboard Super Admin</h1>
        
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-xl font-semibold mb-4">Selamat datang, {{ Auth::user()->name }}!</h2>
                <p class="text-gray-600 mb-4">Anda login sebagai <span class="font-semibold text-red-600">Super Admin</span></p>
                
                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-3">Fitur untuk Anda:</h3>
                    <ul class="list-disc list-inside space-y-2 text-gray-700">
                        <li>Mengelola semua pengguna (User, Admin Toko)</li>
                        <li>Moderasi konten produk dan jasa</li>
                        <li>Melihat statistik platform secara keseluruhan</li>
                        <li>Mengelola kategori produk/jasa</li>
                        <li>Mengelola pengaturan sistem</li>
                        <li>Verifikasi dan approve UMKM baru</li>
                    </ul>
                </div>

                <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-blue-900">Total User</h4>
                        <p class="text-2xl font-bold text-blue-600 mt-2">0</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-green-900">Total UMKM</h4>
                        <p class="text-2xl font-bold text-green-600 mt-2">0</p>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-yellow-900">Total Produk</h4>
                        <p class="text-2xl font-bold text-yellow-600 mt-2">0</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-purple-900">UMKM Pending</h4>
                        <p class="text-2xl font-bold text-purple-600 mt-2">0</p>
                    </div>
                </div>

                <div class="mt-8 space-x-4">
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700">
                        Kelola User
                    </a>
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700">
                        Kelola UMKM
                    </a>
                    <a href="#" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-white hover:bg-yellow-700">
                        Moderasi Konten
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
