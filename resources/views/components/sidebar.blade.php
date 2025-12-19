{{-- File: resources/views/components/sidebar.blade.php --}}
<aside class="sidebar-custom">
    <div class="sidebar-header">
        <h4 class="sidebar-title">PRESENSI PKL</h4>
        <hr class="sidebar-divider">
    </div>

    <nav class="sidebar-menu">
        <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('pembayaran.index') }}" class="sidebar-item {{ request()->routeIs('pembayaran.*') ? 'active' : '' }}">
            <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 11l3 3L22 4"></path>
                <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path>
            </svg>
            <span>Pembayaran</span>
        </a>

        <a href="{{ route('riwayat') }}" class="sidebar-item {{ request()->routeIs('riwayat') ? 'active' : '' }}">
            <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <span>Detail Riwayat</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="{{ route('settings') }}" class="sidebar-item">
            <svg class="sidebar-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M12 1v6m0 6v6m5.66-13.66l-4.24 4.24m0 4.24l4.24 4.24M23 12h-6m-6 0H1m18.66 5.66l-4.24-4.24m0-4.24l4.24-4.24"></path>
            </svg>
            <span>Settings</span>
        </a>

        <div class="theme-toggle">
            <button class="theme-btn" id="lightBtn">
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

<style>
/* Sidebar Styling */
.sidebar-custom {
    width: 240px;
    height: 100vh;
    background: #f0f0f0;
    border-radius: 20px;
    padding: 24px 16px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    position: fixed;
    left: 20px;
    top: 20px;
    bottom: 20px;
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

/* Dark Mode */
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

/* Responsive */
@media (max-width: 768px) {
    .sidebar-custom {
        width: 200px;
        left: 10px;
        top: 10px;
        bottom: 10px;
        padding: 16px 12px;
    }
    
    .sidebar-title {
        font-size: 14px;
    }
    
    .sidebar-item {
        font-size: 13px;
        padding: 10px 12px;
    }
}
</style>

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
        darkBtn.classList.add('active');
    } else {
        lightBtn.classList.add('active');
    }
    
    lightBtn.addEventListener('click', function() {
        body.classList.remove('dark-mode');
        localStorage.setItem('theme', 'light');
        lightBtn.classList.add('active');
        darkBtn.classList.remove('active');
    });
    
    darkBtn.addEventListener('click', function() {
        body.classList.add('dark-mode');
        localStorage.setItem('theme', 'dark');
        darkBtn.classList.add('active');
        lightBtn.classList.remove('active');
    });
});
</script>