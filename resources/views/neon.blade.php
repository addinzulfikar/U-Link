<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>U-LINK - Platform Sharing UMKM</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 to-blue-50">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-2xl font-bold text-indigo-600">U-LINK</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <span class="text-gray-700">{{ Auth::user()->name }}</span>
                        <a href="{{ route('dashboard.user') }}" class="text-indigo-600 hover:text-indigo-800">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 px-3 py-2">Login</a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <h1 class="text-5xl font-extrabold text-gray-900 mb-4">
                    Selamat Datang di <span class="text-indigo-600">U-LINK</span>
                </h1>
                <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                    Platform untuk UMKM saling berbagi dan mempromosikan produk serta jasa mereka. 
                    Bergabunglah bersama ribuan UMKM lainnya untuk mengembangkan bisnis Anda!
                </p>
                
                @guest
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition">
                        Mulai Sekarang
                    </a>
                    <a href="{{ route('login') }}" class="bg-white text-indigo-600 px-8 py-3 rounded-lg text-lg font-semibold border-2 border-indigo-600 hover:bg-indigo-50 transition">
                        Login
                    </a>
                </div>
                @endguest
            </div>

            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-center">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Untuk User</h3>
                        <p class="text-gray-600">
                            Temukan berbagai produk dan jasa dari UMKM lokal. 
                            Dukung bisnis lokal dengan berbelanja dan memberikan review.
                        </p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-center">
                        <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Untuk UMKM</h3>
                        <p class="text-gray-600">
                            Promosikan produk dan jasa Anda secara gratis. 
                            Kelola toko online Anda dan jangkau lebih banyak pelanggan.
                        </p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="text-center">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Terpercaya</h3>
                        <p class="text-gray-600">
                            Sistem verifikasi dan moderasi memastikan kualitas produk dan jasa. 
                            Aman dan terpercaya untuk semua pengguna.
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-12 bg-white p-6 rounded-lg shadow-sm">
                <p class="text-sm text-gray-600">
                    <strong>Status Koneksi Database:</strong> 
                    <span class="text-green-600">{{ Str::limit($db_version, 100) }}</span>
                </p>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-8 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; 2024 U-LINK. Platform Sharing UMKM Indonesia.</p>
        </div>
    </footer>
</body>
</html>
