<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - U-LINK')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        /* Xero-style Admin Layout */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
            background: #F9FAFB;
        }
        
        .admin-sidebar {
            width: 260px;
            background: #FFFFFF;
            border-right: 1px solid #E5E7EB;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }
        
        .admin-sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .admin-sidebar::-webkit-scrollbar-track {
            background: #F3F4F6;
        }
        
        .admin-sidebar::-webkit-scrollbar-thumb {
            background: #D1D5DB;
            border-radius: 3px;
        }
        
        .admin-sidebar-header {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid #E5E7EB;
        }
        
        .admin-sidebar-brand {
            font-size: 1.5rem;
            font-weight: 600;
            color: #111827;
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
            color: #6B7280;
        }
        
        .admin-nav-item {
            display: block;
            padding: 0.75rem 1.25rem;
            color: #6B7280;
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 2px solid transparent;
            font-size: 0.875rem;
        }
        
        .admin-nav-toggle {
            width: 100%;
            background: transparent;
            border: 0;
            text-align: left;
            cursor: pointer;
        }
        
        .admin-nav-toggle.admin-nav-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .admin-nav-item:hover {
            background: #F9FAFB;
            color: #111827;
            border-left-color: #E5E7EB;
        }
        
        .admin-nav-item.active {
            background: #F3F4F6;
            color: #1F73B7;
            border-left-color: #1F73B7;
            font-weight: 500;
        }
        
        .admin-nav-icon {
            display: inline-block;
            width: 1.5rem;
            margin-right: 0.75rem;
            text-align: center;
            font-size: 1rem;
        }
        
        .admin-nav-caret {
            margin-left: auto;
            opacity: 0.6;
            transition: transform 0.2s ease;
        }
        
        .admin-nav-toggle[aria-expanded="true"] .admin-nav-caret {
            transform: rotate(180deg);
        }
        
        .admin-nav-sub {
            padding: 0.25rem 0;
        }
        
        .admin-nav-sub .admin-nav-item {
            padding-left: 2.75rem;
            font-size: 0.875rem;
            opacity: 0.9;
        }
        
        .admin-main {
            flex: 1;
            margin-left: 260px;
            background: #F9FAFB;
        }
        
        .admin-header {
            background: #FFFFFF;
            border-bottom: 1px solid #E5E7EB;
            padding: 1.25rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .admin-content {
            padding: 2rem;
        }
        
        .admin-user-info {
            padding: 1rem 1.25rem;
            border-top: 1px solid #E5E7EB;
            margin-top: auto;
        }
        
        .admin-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #1F73B7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
            color: white;
        }
        
        .admin-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
            border-radius: 0.25rem;
            font-weight: 500;
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
        <!-- Sidebar - Xero-style: Minimal, clean -->
        <aside class="admin-sidebar">
            <div class="admin-sidebar-header">
                <a href="{{ url('/') }}" class="admin-sidebar-brand">
                    <span style="color: #1F73B7;">U</span>-LINK
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
                        <div class="fw-semibold text-truncate" style="font-size: 0.875rem; color: #111827;">{{ Auth::user()->name }}</div>
                        <div style="font-size: 0.75rem; color: #6B7280;">
                            @if(Auth::user()->isSuperAdmin())
                                <span class="admin-badge" style="background: #FEE2E2; color: #991B1B;">Super Admin</span>
                            @elseif(Auth::user()->isAdminToko())
                                <span class="admin-badge" style="background: #FEF3C7; color: #92400E;">Admin Toko</span>
                            @endif
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-sm w-100" style="border: 1px solid #E5E7EB; color: #6B7280; background: transparent;">
                        <span class="admin-nav-icon">🚪</span> Logout
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h4 mb-0" style="font-weight: 600; color: #111827;">@yield('page-title', 'Dashboard')</h1>
                    <div>
                        <a href="{{ url('/') }}" class="btn btn-sm" style="border: 1px solid #E5E7EB; color: #6B7280; background: white;">
                            <span>🏠</span> Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </header>
            
            @if(session('success'))
                <div class="admin-content">
                    <div class="alert alert-dismissible fade show" role="alert" style="background: #D1FAE5; border: 1px solid #059669; color: #065F46; border-radius: 8px;">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif
            
            @if(session('error'))
                <div class="admin-content">
                    <div class="alert alert-dismissible fade show" role="alert" style="background: #FEE2E2; border: 1px solid #DC2626; color: #991B1B; border-radius: 8px;">
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

    @livewireScripts

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const activeLinks = document.querySelectorAll('.admin-nav-sub .admin-nav-item.active');
            activeLinks.forEach(function (link) {
                const collapseEl = link.closest('.collapse');
                if (!collapseEl) return;

                if (window.bootstrap && window.bootstrap.Collapse) {
                    const instance = window.bootstrap.Collapse.getOrCreateInstance(collapseEl, { toggle: false });
                    instance.show();
                } else {
                    collapseEl.classList.add('show');
                }

                const id = collapseEl.getAttribute('id');
                if (!id) return;

                const toggle = document.querySelector('[data-bs-target="#' + id + '"]');
                if (toggle) toggle.setAttribute('aria-expanded', 'true');
            });
        });
    </script>
</body>
</html>
