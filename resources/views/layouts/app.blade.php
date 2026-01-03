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

    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-color: #213448;
            --text-muted: #717171;
            --border-color: #b3b9c4ff;
            --radius: 16px;
        }

        /* ========== Global Styles ========== */
        body {
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        body.dark-mode {
            background: #1a1a1a;
            color: #e0e0e0;
        }

        /* ========== Select2 Customization ========== */
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: #213448 !important;
            color: #ffffff !important;
            padding: 2px 10px !important;
            border: none !important;
            border-radius: 4px !important;
            font-size: 13px !important;
            margin-top: 6px !important;
        }

        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
            color: #ffffff !important;
            margin-right: 5px !important;
        }

        .select2-container--open {
            z-index: 9999 !important;
        }

        /* ========== Sidebar Styles ========== */
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

        .sidebar-footer {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid #e0e0e0;
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

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }

        .sidebar-overlay.show {
            display: block;
        }

        /* ========== Main Content Styles ========== */
        .main-wrapper {
            margin-left: 280px;
            padding: 20px;
            min-height: 100vh;
        }

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
            transition: opacity 0.2s ease;
        }

        .dashboard-user:hover {
            opacity: 0.8;
        }

        .dashboard-user-email strong {
            display: block;
            font-size: 14px;
            font-weight: 600;
        }

        .dashboard-user-email small {
            font-size: 12px;
        }

        .dashboard-user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: 2px solid #e0e0e0;
        }

        /* ========== Responsive Styles ========== */
        @media (max-width: 991.98px) {
            .sidebar-custom {
                transform: translateX(-110%);
                transition: transform 0.3s ease-in-out;
                left: 0;
                top: 0;
                height: 100vh;
                width: 260px;
                border-radius: 0;
                z-index: 1050;
            }

            .sidebar-custom.show {
                transform: translateX(0);
                box-shadow: 10px 0 30px rgba(0, 0, 0, 0.1);
            }

            .main-wrapper {
                margin-left: 0 !important;
                padding: 15px;
            }

            .sidebar-toggle-btn {
                display: flex;
            }

            .dashboard-header {
                padding: 15px 20px;
            }
        }

        @media (max-width: 576px) {
            .dashboard-title {
                font-size: 18px;
            }

            .dashboard-user-email {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="app-wrapper">
        @auth
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        <aside class="sidebar-custom" id="sidebar">
            <div class="sidebar-header">
                <h4 class="sidebar-title">GriyaSoft</h4>
                <hr class="sidebar-divider">
            </div>

            <nav class="sidebar-menu">
                @if(auth()->user()->role === 'siswa')
                    <a href="{{ route('siswa.dashboard') }}" class="sidebar-item {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('siswa.riwayat-presensi') }}" class="sidebar-item {{ request()->routeIs('siswa.riwayat-presensi') ? 'active' : '' }}">
                        <i class="bi bi-clock-history"></i>
                        <span>Riwayat Presensi</span>
                    </a>
                    <a href="{{ route('siswa.tagihan.index') }}" class="sidebar-item {{ request()->routeIs('siswa.tagihan.index') ? 'active' : '' }}">
                        <i class="bi bi-wallet2"></i>
                        <span>Tagihan Saya</span>
                    </a>
                @endif

                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.siswa.index') }}" class="sidebar-item {{ request()->routeIs('admin.siswa.index') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i>
                        <span>Manajemen Siswa</span>
                    </a>
                    <a href="{{ route('admin.sekolah.index') }}" class="sidebar-item {{ request()->routeIs('admin.sekolah.index') ? 'active' : '' }}">
                        <i class="bi bi-building"></i>
                        <span>Manajemen Sekolah</span>
                    </a>
                    <a href="{{ route('admin.pembayaran.index') }}" class="sidebar-item {{ request()->routeIs('admin.pembayaran.index') ? 'active' : '' }}">
                        <i class="bi bi-wallet2"></i>
                        <span>Manajemen Pembayaran</span>
                    </a>
                    <a href="{{ route('admin.presensi') }}" class="sidebar-item {{ request()->routeIs('admin.presensi') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check-fill"></i>
                        <span>Manajemen Absensi</span>
                    </a>
                    <a href="{{ route('admin.setting.jam-kerja') }}" class="sidebar-item {{ request()->routeIs('admin.setting.jam-kerja') ? 'active' : '' }}">
                        <i class="bi bi-clock-fill"></i>
                        <span>Pengaturan Jam Kerja</span>
                    </a>
                @endif
            </nav>

            <div class="sidebar-footer">
                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
                    <button type="button" onclick="confirmLogout()" class="sidebar-item border-0 bg-transparent w-100 text-danger text-start">
                        <i class="bi bi-box-arrow-left"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <button class="sidebar-toggle-btn" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        @endauth

        <main class="@auth main-wrapper @else py-4 @endauth">
            @auth
            <div class="dashboard-header">
                <h1 class="dashboard-title">@yield('page_title', 'Dashboard')</h1>
                
                @php
                    $profileRoute = (auth()->user()->role === 'admin') 
                                    ? route('admin.profile') 
                                    : route('siswa.profile');
                @endphp

                <a href="{{ $profileRoute }}" class="text-decoration-none text-dark">
                    <div class="dashboard-user">
                        <div class="text-end me-3 d-none d-sm-block dashboard-user-email">
                            <strong>{{ auth()->user()->nama_lengkap }}</strong>
                            <small class="text-muted d-block">{{ auth()->user()->email }}</small>
                        </div>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nama_lengkap) }}&background=0D6EFD&color=fff" 
                            class="rounded-circle border border-2 border-primary border-opacity-10" 
                            width="40" 
                            height="40" 
                            alt="Avatar">
                    </div>
                </a>
            </div>
            @endauth

            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script>
        $(document).ready(function() {
            $('select[name="siswa_id[]"]').select2({
                placeholder: "-- Cari Nama Siswa --",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalTambahTagihan')
            });
        });

        function confirmLogout() {
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Anda akan keluar dari sesi aplikasi ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Logout!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const body = document.body;
            const savedTheme = localStorage.getItem('theme') || 'light';
            
            if (savedTheme === 'dark') {
                body.classList.add('dark-mode');
            }

            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            function toggleSidebar() {
                sidebar?.classList.toggle('show');
                overlay?.classList.toggle('show');
            }

            sidebarToggle?.addEventListener('click', toggleSidebar);
            overlay?.addEventListener('click', toggleSidebar);
        });
    </script>
</body>
</html>