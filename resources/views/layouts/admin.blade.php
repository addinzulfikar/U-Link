<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - U-LINK')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        .admin-sidebar {
            width: 260px;
            background: #2c3e50;
            color: #ecf0f1;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }
        
        .admin-sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .admin-sidebar::-webkit-scrollbar-track {
            background: #34495e;
        }
        
        .admin-sidebar::-webkit-scrollbar-thumb {
            background: #7f8c8d;
            border-radius: 3px;
        }
        
        .admin-sidebar-header {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(236, 240, 241, 0.1);
        }
        
        .admin-sidebar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .admin-sidebar-nav {
            padding: 1rem 0;
        }
        
        .admin-nav-section {
            padding: 0.75rem 1.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #95a5a6;
        }
        
        .admin-nav-item {
            display: block;
            padding: 0.75rem 1.25rem;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        
        .admin-nav-item:hover {
            background: rgba(52, 73, 94, 0.5);
            color: #fff;
            border-left-color: #3498db;
        }
        
        .admin-nav-item.active {
            background: rgba(52, 152, 219, 0.2);
            color: #fff;
            border-left-color: #3498db;
        }
        
        .admin-nav-icon {
            display: inline-block;
            width: 1.5rem;
            margin-right: 0.75rem;
            text-align: center;
        }
        
        .admin-main {
            flex: 1;
            margin-left: 260px;
            background: #ecf0f1;
        }
        
        .admin-header {
            background: #fff;
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .admin-content {
            padding: 1.5rem;
        }
        
        .admin-user-info {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(236, 240, 241, 0.1);
            margin-top: auto;
        }
        
        .admin-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #3498db;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
        }
        
        .admin-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
            border-radius: 0.25rem;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .admin-sidebar.show {
                transform: translateX(0);
            }
            
            .admin-main {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-sidebar-header">
                <a href="{{ url('/') }}" class="admin-sidebar-brand">
                    <span style="color: #3498db;">U</span>-LINK
                </a>
            </div>
            
            <nav class="admin-sidebar-nav">
                @yield('sidebar')
            </nav>
            
            <div class="admin-user-info">
                <div class="d-flex align-items-center gap-3">
                    <div class="admin-user-avatar">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="flex-fill" style="min-width: 0;">
                        <div class="fw-semibold text-truncate" style="font-size: 0.9rem;">{{ Auth::user()->name }}</div>
                        <div style="font-size: 0.75rem; color: #95a5a6;">
                            @if(Auth::user()->isSuperAdmin())
                                <span class="admin-badge" style="background: #e74c3c;">Super Admin</span>
                            @elseif(Auth::user()->isAdminToko())
                                <span class="admin-badge" style="background: #f39c12;">Admin Toko</span>
                            @endif
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light w-100">
                        <span class="admin-nav-icon">🚪</span> Logout
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h4 mb-0 fw-bold">@yield('page-title', 'Dashboard')</h1>
                    <div>
                        <a href="{{ url('/') }}" class="btn btn-sm btn-outline-secondary">
                            <span>🏠</span> Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </header>
            
            @if(session('success'))
                <div class="admin-content">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif
            
            @if(session('error'))
                <div class="admin-content">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif
            
            <div class="admin-content">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
