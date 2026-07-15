<!-- Load Font & Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --nav-bg: #0f172a;
        --nav-text: #f8fafc;
        --nav-text-muted: #94a3b8;
        --nav-border: #1e293b;
        --brand-primary: #3b82f6;
        --accent-color: #60a5fa;
        --dropdown-bg: #1e293b;
    }

    .user-navbar {
        background: var(--nav-bg);
        height: 75px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1.5rem;
        position: sticky;
        top: 0;
        z-index: 1000;
        font-family: 'Plus Jakarta Sans', sans-serif;
        border-bottom: 1px solid var(--nav-border);
    }

    /* Container untuk Burger + Brand agar Sejajar (Solusi image_9c604c.png) */
    .nav-brand-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* Tombol Burger */
    .menu-toggle {
        display: none; /* Sembunyi di desktop */
        background: #1e293b;
        border: none;
        color: white;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.3s;
    }

    /* Brand Section */
    .nav-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .brand-box {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, var(--brand-primary), #2563eb);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
    }

    .brand-text {
    font-size: 1.2rem;
    font-weight: 800;
    /* Efek Gradasi Teks */
    background: linear-gradient(135deg, #ffffff 30%, var(--accent-color) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: inline-block;
    }

    /* Navigation Menu */
    .nav-menu {
        display: flex;
        gap: 8px;
    }

    .nav-item {
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 10px;
        color: var(--nav-text-muted);
        font-size: 0.85rem;
        font-weight: 600;
        transition: 0.3s;
        cursor: pointer;
        border: none;
        background: transparent;
        white-space: nowrap;
    }

    .nav-item:hover, .nav-item.active {
        color: var(--accent-color);
        background: rgba(59, 130, 246, 0.1);
    }

    /* --- DROPDOWN STYLING --- */
    .nav-dropdown { position: relative; }

    .dropdown-content {
        position: absolute;
        top: calc(100% + 15px);
        left: 0;
        background: var(--dropdown-bg);
        border: 1px solid var(--nav-border);
        min-width: 210px;
        border-radius: 12px;
        padding: 8px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        opacity: 0;
        visibility: hidden;
        transition: 0.3s ease;
        z-index: 1002;
    }

    .dropdown-link {
        text-decoration: none !important;
        display: flex !important;
        align-items: center;
        gap: 10px;
        padding: 12px 16px !important;
        border-radius: 8px;
        color: #ffffff !important;
        font-size: 0.85rem;
        font-weight: 500;
        transition: 0.2s;
    }

    .dropdown-link:hover {
        background: rgba(255, 255, 255, 0.08);
        color: var(--accent-color) !important;
    }

    .nav-dropdown:hover .dropdown-content {
        opacity: 1;
        visibility: visible;
        transform: translateY(-5px);
    }

    .chevron {
        font-size: 0.7rem;
        transition: 0.3s;
    }

    /* --- TOOLS AREA (Profil & Logout) --- */
    .nav-tools {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .profile-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-right: 15px;
        border-right: 1px solid var(--nav-border);
    }

    .user-info {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .user-name {
        color: #ffffff;
        font-size: 0.85rem;
        font-weight: 700;
        margin-bottom: 2px;
    }

    .user-role {
        color: var(--brand-primary);
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        background: #3b82f6;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-weight: 800;
        font-size: 1.1rem;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
    }

    .btn-logout-custom {
        width: 40px;
        height: 40px;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.1);
        border-radius: 10px;
        color: #f87171;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.3s ease;
        font-size: 1.1rem;
    }

    .btn-logout-custom:hover {
        background: #ef4444;
        color: #ffffff;
        transform: translateY(-2px);
    }

    /* --- MOBILE RESPONSIVE --- */
    @media (max-width: 1024px) {
        .menu-toggle {
            display: flex; /* Munculkan burger di mobile */
        }

        .nav-menu {
            position: fixed;
            top: 75px;
            left: -100%;
            width: 100%;
            height: calc(100vh - 75px);
            background: var(--nav-bg);
            flex-direction: column;
            padding: 1.5rem;
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            gap: 10px;
        }

        .nav-menu.active { left: 0; }

        .nav-item {
            width: 100%;
            background: #1e293b;
            padding: 15px;
        }

        .dropdown-content {
            position: static;
            display: none;
            opacity: 1;
            visibility: visible;
            transform: none;
            background: rgba(0, 0, 0, 0.2);
            box-shadow: none;
            width: 100%;
            margin-top: 5px;
            border: none;
            border-left: 3px solid var(--brand-primary);
            padding-left: 10px;
        }

        .dropdown-content.show { display: block !important; }

        .user-info { display: none; }
        .profile-wrapper { border-right: none; padding-right: 0; }
    }
</style>

@php
    $user = session('user');
    $userName = $user['nama_karyawan'] ?? 'User';
    $initial = strtoupper(substr($userName, 0, 1));
@endphp

<nav class="user-navbar">
    <!-- Bagian Kiri: Burger & Logo Sejajar -->
    <div class="nav-brand-wrapper">
        <button class="menu-toggle" id="mobile-toggle">
            <i class="fa-solid fa-bars"></i>
        </button>
        <a href="{{ route('user.dashboard') }}" class="nav-brand">
            <div class="brand-box"><i class="fa-solid fa-house"></i></div>
            <span class="brand-text d-none d-sm-block">HRIS Plus</span>
        </a>
    </div>

    <!-- Bagian Tengah: Navigation Menu -->
    <div class="nav-menu" id="nav-menu">
        <a href="{{ route('user.dashboard') }}" class="nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
            <span><i class="fa-solid fa-chart-line me-1"></i> Dashboard</span>
        </a>

        <!-- Dropdown Kehadiran -->
        <div class="nav-dropdown">
            <button class="nav-item mobile-dropdown-toggle {{ request()->is('user/absensi*') || request()->is('user/cuti*') ? 'active' : '' }}">
                <span><i class="fa-solid fa-calendar-check me-1"></i> Kehadiran</span>
                <i class="fa-solid fa-chevron-down chevron"></i>
            </button>
            <div class="dropdown-content">
                <a href="{{ route('user.absensi.index') }}" class="dropdown-link">
                    <i class="fa-solid fa-fingerprint"></i> Presensi Saya
                </a>
                <a href="{{ route('user.cuti.index') }}" class="dropdown-link">
                    <i class="fa-solid fa-calendar-day"></i> Pengajuan Cuti
                </a>
            </div>
        </div>

       <div class="nav-dropdown">
    <button class="nav-item mobile-dropdown-toggle {{ request()->is('user/payslip*') ? 'active' : '' }}">
        <span><i class="fa-solid fa-wallet me-1"></i> Keuangan</span>
        <i class="fa-solid fa-chevron-down chevron"></i>
    </button>
    <div class="dropdown-content">
        <a href="{{ route('user.payslip.index') }}" class="dropdown-link">
            <i class="fa-solid fa-receipt"></i> Slip Gaji (Payslip)
        </a>
        <a href="{{ route('user.klaim.index') }}" class="dropdown-link">
            <i class="fa-solid fa-hand-holding-dollar"></i> Klaim Reimbursement
        </a>

    </div>
</div>
    </div>

    <!-- Bagian Kanan: Profil & Logout -->
    <div class="nav-tools">
        <div class="profile-wrapper">
            <div class="user-info">
                <span class="user-name">{{ $userName }}</span>
                <span class="user-role">Karyawan</span>
            </div>
            <div class="user-avatar">
                {{ $initial }}
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="btn-logout-custom" title="Keluar">
                <i class="fa-solid fa-power-off"></i>
            </button>
        </form>
    </div>
</nav>

<script>
    // Logic Toggle Menu Mobile
    const mobileToggle = document.getElementById('mobile-toggle');
    const navMenu = document.getElementById('nav-menu');

    mobileToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        mobileToggle.innerHTML = navMenu.classList.contains('active')
            ? '<i class="fa-solid fa-xmark"></i>'
            : '<i class="fa-solid fa-bars"></i>';
    });

    // Logic Dropdown Mobile (Accordion)
    document.querySelectorAll('.mobile-dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('click', function() {
            if (window.innerWidth <= 1024) {
                const content = this.nextElementSibling;
                const chevron = this.querySelector('.chevron');
                content.classList.toggle('show');
                if(chevron) {
                    chevron.style.transform = content.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
                }
            }
        });
    });
</script>
