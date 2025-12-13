{{-- File: resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap 5 CSS  -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Sidebar Styling */
        .sidebar-custom {
            width: 240px;
            height: calc(100vh - 40px);
            background: #f0f0f0;
            border-radius: 20px;
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin: 0;
            color: #333;
        }

        .sidebar-divider {
            border: none;
            border-top: 2px solid #333;
            margin: 12px auto;
            width: 80%;
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
            color: #333;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .sidebar-item:hover {
            background: #e0e0e0;
            color: #000;
        }

        .sidebar-item.active {
            background: #4a90e2;
            color: white;
        }

        .sidebar-icon {
            flex-shrink: 0;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid #d0d0d0;
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
            color: #666;
            font-size: 13px;
            cursor: pointer;
            padding: 6px 8px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .theme-btn:hover {
            background: #e0e0e0;
            color: #000;
        }

        .theme-btn.active {
            color: #4a90e2;
            font-weight: 600;
        }

        .theme-divider {
            color: #999;
        }

        /* Main Content with Sidebar */
        .main-wrapper {
            margin-left: 280px;
            padding: 20px;
            min-height: 100vh;
        }

        /* Navbar adjustment */
        .navbar {
            margin-left: 280px;
        }

        /* Dark Mode */
        body.dark-mode {
            background: #1a1a1a;
            color: #e0e0e0;
        }

        body.dark-mode .sidebar-custom {
            background: #2a2a2a;
        }

        body.dark-mode .sidebar-title {
            color: #fff;
        }

        body.dark-mode .sidebar-divider {
            border-color: #fff;
        }

        body.dark-mode .sidebar-item {
            color: #e0e0e0;
        }

        body.dark-mode .sidebar-item:hover {
            background: #3a3a3a;
            color: #fff;
        }

        body.dark-mode .sidebar-item.active {
            background: #4a90e2;
            color: white;
        }

        body.dark-mode .sidebar-footer {
            border-color: #444;
        }

        body.dark-mode .theme-btn {
            color: #aaa;
        }

        body.dark-mode .theme-btn:hover {
            background: #3a3a3a;
            color: #fff;
        }

        body.dark-mode .navbar {
            background: #2a2a2a !important;
            border-bottom: 1px solid #444;
        }

        body.dark-mode .navbar-light .navbar-brand,
        body.dark-mode .navbar-light .nav-link {
            color: #e0e0e0 !important;
        }

        body.dark-mode .card {
            background: #2a2a2a;
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
            
            .main-wrapper,
            .navbar {
                margin-left: 220px;
            }
        }

        @media (max-width: 768px) {
            .sidebar-custom {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar-custom.show {
                transform: translateX(0);
            }
            
            .main-wrapper,
            .navbar {
                margin-left: 0;
            }

            .sidebar-toggle-btn {
                display: block !important;
            }
        }

        .sidebar-toggle-btn {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1001;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #4a90e2;
            color: white;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div id="app">
        {{-- SIDEBAR untuk user yang sudah login --}}
        @auth
        <aside class="sidebar-custom" id="sidebar">
            <div class="sidebar-header">
                <h4 class="sidebar-title">PRESENSI PKL</h4>
                <hr class="sidebar-divider">
            </div>

            <nav class="sidebar-menu">
                @if(auth()->user()->role === 'siswa')
                    <a href="{{ route('pembayaran.dashboardsiswa') }}" class="sidebar-item {{ request()->routeIs('pembayaran.dashboardsiswa') ? 'active' : '' }}">
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

                    <a href="#" class="sidebar-item">
                        <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Detail Riwayat</span>
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

        {{-- NAVBAR --}}
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        {{-- Menu dipindahkan ke sidebar --}}
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">Register</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    {{ Auth::user()->name }}
                                    <span class="badge bg-primary">{{ ucfirst(Auth::user()->role) }}</span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="bi bi-person"></i> Profile
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        {{-- MAIN CONTENT --}}
        <main class="@auth main-wrapper @else py-4 @endauth">
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