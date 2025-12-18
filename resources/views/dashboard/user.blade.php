@extends('layouts.app')

@section('title', 'Dashboard User - U-LINK')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Dashboard User</h1>
        
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-xl font-semibold mb-4">Selamat datang, {{ Auth::user()->name }}!</h2>
                <p class="text-gray-600 mb-4">Anda login sebagai <span class="font-semibold text-indigo-600">User</span></p>
                
                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-3">Fitur untuk Anda:</h3>
                    <ul class="list-disc list-inside space-y-2 text-gray-700">
                        <li>Melihat produk dan jasa dari UMKM</li>
                        <li>Mencari UMKM berdasarkan kategori</li>
                        <li>Menyimpan UMKM favorit</li>
                        <li>Memberikan review dan rating</li>
                    </ul>
                </div>

                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-indigo-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-indigo-900">Produk UMKM</h4>
                        <p class="text-sm text-indigo-700 mt-2">Temukan berbagai produk dari UMKM lokal</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-green-900">Jasa UMKM</h4>
                        <p class="text-sm text-green-700 mt-2">Cari berbagai layanan jasa dari UMKM</p>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-yellow-900">Favorit Saya</h4>
                        <p class="text-sm text-yellow-700 mt-2">UMKM yang Anda simpan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
