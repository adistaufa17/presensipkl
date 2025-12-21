{{-- File: resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Presensi PKL') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap 5 CSS  -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>

        :root {
        --primary-color: #213448;
        --text-muted: #717171;
        --border-color: #b3b9c4ff;
        --radius: 16px;
    }

        /* Reset & Base */
        body {
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        /* Sidebar Styling */
        .sidebar-custom {
            width: 240px;
            height: calc(100vh - 40px);
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 20px;
            top: 20px;
            z-index: 1000;
        }

        .sidebar-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .sidebar-title {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin: 0;
            color: #212529;
        }

        .sidebar-divider {
            border: none;
            border-top: 1px solid #213448;
            margin: 16px 0;
        }

        .sidebar-menu {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #495057;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.2s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .sidebar-item:hover {
            background: #f8f9fa;
            color: #212529;
        }

        .sidebar-item.active {
            background: #e7f1ff;
            color: #0d6efd;
        }

        .sidebar-icon {
            flex-shrink: 0;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid #e0e0e0;
        }

        .theme-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 12px;
            padding: 8px;
        }

        .theme-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            background: transparent;
            border: none;
            color: #6c757d;
            font-size: 13px;
            cursor: pointer;
            padding: 6px 8px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .theme-btn:hover {
            background: #f8f9fa;
            color: var(--primary-color);
        }

        .theme-btn.active {
            color: #0d6efd;
            font-weight: 600;
        }

        .theme-divider {
            color: #dee2e6;
        }

        /* Main Content with Sidebar */
        .main-wrapper {
            margin-left: 280px;
            padding: 20px;
            min-height: 100vh;
        }

        /* Dashboard Header Card */
        .dashboard-header {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 20px 32px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dashboard-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            color: #212529;
        }

        .dashboard-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .dashboard-user-email {
            text-align: right;
        }

        .dashboard-user-email strong {
            display: block;
            color: #212529;
            font-size: 14px;
            font-weight: 600;
        }

        .dashboard-user-email small {
            color: #6c757d;
            font-size: 12px;
        }

        .dashboard-user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: 2px solid #e0e0e0;
        }

        /* Styling Dropdown User */
        .user-dropdown-menu {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            padding: 8px;
            margin-top: 10px !important;
        }

        .user-dropdown-item {
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #495057;
            transition: all 0.2s;
        }

        .user-dropdown-item:hover {
            background-color: #f8f9fa;
            color: var(--primary-color);
        }

        .user-dropdown-item.text-danger:hover {
            background-color: #fff5f5;
        }

        /* Dark Mode */
        body.dark-mode {
            background: #1a1a1a;
            color: #e0e0e0;
        }

        body.dark-mode .sidebar-custom {
            background: #2a2a2a;
            border-color: #404040;
        }

        body.dark-mode .sidebar-title {
            color: #e0e0e0;
        }

        body.dark-mode .sidebar-divider {
            border-color: #404040;
        }

        body.dark-mode .sidebar-item {
            color: #adb5bd;
        }

        body.dark-mode .sidebar-item:hover {
            background: #3a3a3a;
            color: #e0e0e0;
        }

        body.dark-mode .sidebar-item.active {
            background: #1a3a5a;
            color: #4a9eff;
        }

        body.dark-mode .sidebar-footer {
            border-color: #404040;
        }

        body.dark-mode .theme-btn {
            color: #adb5bd;
        }

        body.dark-mode .theme-btn:hover {
            background: #3a3a3a;
            color: #e0e0e0;
        }

        body.dark-mode .theme-btn.active {
            color: #4a9eff;
        }

        body.dark-mode .dashboard-header {
            background: #2a2a2a;
            border-color: #404040;
        }

        body.dark-mode .dashboard-title,
        body.dark-mode .dashboard-user-email strong {
            color: #e0e0e0;
        }

        body.dark-mode .dashboard-user-email small {
            color: #adb5bd;
        }

        body.dark-mode .dashboard-user-avatar {
            border-color: #404040;
        }
        body.dark-mode .user-dropdown-menu {
            background-color: #2a2a2a;
            border-color: #404040;
        }

        body.dark-mode .user-dropdown-item {
            color: #adb5bd;
        }

        body.dark-mode .user-dropdown-item:hover {
            background-color: #3a3a3a;
            color: #e0e0e0;
        }

        /* Mobile Responsive */
        @media (max-width: 992px) {
            .sidebar-custom {
                width: 200px;
                left: 10px;
                top: 10px;
                height: calc(100vh - 20px);
            }
            
            .main-wrapper {
                margin-left: 220px;
            }
        }

        @media (max-width: 768px) {
            .sidebar-custom {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                left: 0;
                top: 0;
                height: 100vh;
                border-radius: 0;
            }
            
            .sidebar-custom.show {
                transform: translateX(0);
            }
            
            .main-wrapper {
                margin-left: 0;
            }

            .sidebar-toggle-btn {
                display: flex !important;
            }

            .dashboard-header {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }

            .dashboard-user {
                flex-direction: column;
            }
        }

        .sidebar-toggle-btn {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1001;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #0d6efd;
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div id="app">
        {{-- SIDEBAR untuk user yang sudah login --}}
        @auth
        <aside class="sidebar-custom" id="sidebar">
            <div class="sidebar-header">
                <h4 class="sidebar-title">GriyaSoft</h4>
                <hr class="sidebar-divider">
            </div>

            <nav class="sidebar-menu">
                @if(auth()->user()->role === 'siswa')
                    <a href="{{ route('siswa.dashboard') }}" class="sidebar-item {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
                        <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('pembayaran.siswa') }}" class="sidebar-item {{ request()->routeIs('pembayaran.siswa') ? 'active' : '' }}">
                        <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 11l3 3L22 4"></path>
                            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path>
                        </svg>
                        <span>Pembayaran</span>
                    </a>

                    <a href="{{ route('presensi.riwayat') }}" class="sidebar-item {{ request()->routeIs('presensi.riwayat') ? 'active' : '' }}">
                        <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 3h18v18H3z"></path>
                            <path d="M3 9h18"></path>
                            <path d="M9 3v18"></path>
                        </svg>
                        <span>Riwayat Presensi</span>
                    </a>

                @elseif(auth()->user()->role === 'pembimbing')
                    <a href="{{ route('pembimbing.dashboard') }}" class="sidebar-item {{ request()->routeIs('pembimbing.dashboard') ? 'active' : '' }}">
                        <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('tagihan.index') }}" class="sidebar-item {{ request()->routeIs('tagihan.*') ? 'active' : '' }}">
                        <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                        <span>Tagihan</span>
                    </a>

                    <a href="{{ route('pembayaran.semua') }}" class="sidebar-item {{ request()->routeIs('pembayaran.semua') ? 'active' : '' }}">
                        <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 11l3 3L22 4"></path>
                            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path>
                        </svg>
                        <span>Pembayaran</span>
                    </a>
                @endif
            </nav>

            <div class="sidebar-footer">
                <a href="{{ route('profile.edit') }}" class="sidebar-item">
                    <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M12 1v6m0 6v6m5.66-13.66l-4.24 4.24m0 4.24l4.24 4.24M23 12h-6m-6 0H1m18.66 5.66l-4.24-4.24m0-4.24l4.24-4.24"></path>
                    </svg>
                    <span>Settings</span>
                </a>

                <div class="theme-toggle">
                    <button class="theme-btn active" id="lightBtn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="5"></circle>
                            <line x1="12" y1="1" x2="12" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="23"></line>
                            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                            <line x1="1" y1="12" x2="3" y2="12"></line>
                            <line x1="21" y1="12" x2="23" y2="12"></line>
                            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                        </svg>
                        Light
                    </button>
                    <span class="theme-divider">|</span>
                    <button class="theme-btn" id="darkBtn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"></path>
                        </svg>
                        Dark
                    </button>
                </div>
            </div>
        </aside>

        {{-- Toggle button for mobile --}}
        <button class="sidebar-toggle-btn" id="sidebarToggle">
            <i class="bi bi-list" style="font-size: 24px;"></i>
        </button>
        @endauth

        {{-- MAIN CONTENT --}}
        <main class="@auth main-wrapper @else py-4 @endauth">
            @auth
           {{-- Dashboard Header Card --}}
<div class="dashboard-header">
    <h1 class="dashboard-title">
        @yield('page_title', 'Dashboard')
    </h1>
    
    <div class="dropdown dashboard-user">
        <a href="#" class="d-flex align-items-center text-decoration-none" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="dashboard-user-email me-2 d-none d-sm-block">
                <strong>{{ auth()->user()->name }}</strong>
                <small>{{ auth()->user()->email }}</small>
            </div>
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0d6efd&color=fff" 
                 alt="Avatar" 
                 class="dashboard-user-avatar">
        </a>
        
        <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu" aria-labelledby="userDropdown">
            <li>
                <a class="dropdown-item user-dropdown-item" href="{{ route('profile.edit') }}">
                    <i class="bi bi-person"></i> Profil Saya
                </a>
            </li>
            <li><hr class="dropdown-divider mx-2"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item user-dropdown-item text-danger w-100 border-0 bg-transparent">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>
            @endauth

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Theme Toggle Script
        document.addEventListener('DOMContentLoaded', function() {
            const lightBtn = document.getElementById('lightBtn');
            const darkBtn = document.getElementById('darkBtn');
            const body = document.body;
            
            // Check saved theme
            const savedTheme = localStorage.getItem('theme') || 'light';
            if (savedTheme === 'dark') {
                body.classList.add('dark-mode');
                if (darkBtn) {
                    darkBtn.classList.add('active');
                    lightBtn.classList.remove('active');
                }
            }
            
            if (lightBtn) {
                lightBtn.addEventListener('click', function() {
                    body.classList.remove('dark-mode');
                    localStorage.setItem('theme', 'light');
                    lightBtn.classList.add('active');
                    darkBtn.classList.remove('active');
                });
            }
            
            if (darkBtn) {
                darkBtn.addEventListener('click', function() {
                    body.classList.add('dark-mode');
                    localStorage.setItem('theme', 'dark');
                    darkBtn.classList.add('active');
                    lightBtn.classList.remove('active');
                });
            }

            // Mobile sidebar toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });

                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function(event) {
                    if (window.innerWidth <= 768) {
                        if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                            sidebar.classList.remove('show');
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>