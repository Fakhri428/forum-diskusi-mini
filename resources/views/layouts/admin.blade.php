<!-- filepath: resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Nunito', sans-serif;
        }

        /* Sidebar - Peningkatan */
        .sidebar {
            min-height: 100vh;
            background-color: #2c3e50; /* Warna background lebih gelap */
            color: white;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
        }

        /* Logo header dengan latar belakang lebih kontras */
        .sidebar-header {
            padding: 20px 15px;
            text-align: center;
            background-color: #1a2639;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h4 {
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .sidebar-header p {
            opacity: 0.8;
            margin-bottom: 0;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        /* Styling untuk item sidebar */
        .sidebar-menu {
            padding: 0 10px;
        }

        .sidebar-link {
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 8px;
            transition: all 0.3s;
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        .sidebar-link:hover {
            color: white;
            background-color: rgba(52, 152, 219, 0.8); /* Warna biru transparan saat hover */
            transform: translateX(5px);
        }

        .sidebar-link.active {
            color: white;
            background-color: #3498db; /* Warna biru cerah untuk item aktif */
            border-left: 4px solid #e74c3c; /* Aksen merah di sebelah kiri */
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Icon sidebar yang lebih besar dan jelas */
        .sidebar-link i {
            margin-right: 12px;
            width: 24px;
            height: 24px;
            text-align: center;
            color: #ffffff;
            font-size: 18px;
            line-height: 24px;
        }

        /* Divider untuk memisahkan grup menu */
        .sidebar-divider {
            height: 1px;
            background-color: rgba(255, 255, 255, 0.1);
            margin: 15px 0;
        }

        /* Footer sidebar */
        .sidebar-footer {
            margin-top: auto;
            padding: 15px 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Bagian yang tidak berubah dari CSS asli */
        .content {
            padding: 25px;
            background-color: #f0f2f5;
        }

        .navbar {
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 15px 20px;
        }

        .navbar-brand {
            font-weight: 700;
            color: #2c3e50;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            margin-bottom: 25px;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e0e6ed;
            font-weight: 600;
            padding: 15px 20px;
            border-top-left-radius: 10px !important;
            border-top-right-radius: 10px !important;
        }

        /* Kartu statistik dengan warna berbeda */
        .stat-card {
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        /* Warna untuk kartu statistik */
        .stat-card.primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .stat-card.success {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
        }

        .stat-card.warning {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
        }

        .stat-card.danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .stat-card .stat-icon {
            font-size: 48px;
            opacity: 0.5;
        }

        .stat-card .stat-value {
            font-size: 24px;
            font-weight: 700;
        }

        .stat-card .stat-label {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
        }

        /* Tombol */
        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }

        /* Badge/Label */
        .badge {
            padding: 7px 10px;
            font-weight: 500;
        }

        .badge-success {
            background-color: #2ecc71;
        }

        .badge-warning {
            background-color: #f39c12;
            color: #fff;
        }

        .badge-danger {
            background-color: #e74c3c;
        }

        /* Tabel */
        .table {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .table thead th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
            color: #2c3e50;
        }

        /* Footer */
        footer {
            background-color: white;
            padding: 15px 25px;
            border-top: 1px solid #e0e6ed;
            margin-top: 30px;
        }

        /* Utilitas */
        .text-primary { color: #3498db !important; }
        .text-success { color: #2ecc71 !important; }
        .text-warning { color: #f39c12 !important; }
        .text-danger { color: #e74c3c !important; }
        .text-muted { color: #95a5a6 !important; }

        /* Breadcrumb */
        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 20px;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            font-size: 20px;
            line-height: 0;
            vertical-align: middle;
            color: #95a5a6;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar - Struktur yang Diperbarui -->
            <div class="col-md-2 col-lg-2 px-0 sidebar">
                <div class="d-flex flex-column h-100">
                    <!-- Logo dan Judul -->
                    <div class="sidebar-header">
                        <h4>DISQUSERIA</h4>
                        <p>Admin Panel</p>
                    </div>

                    <!-- Menu Utama -->
                    <div class="sidebar-menu">
                        <h6 class="text-uppercase px-3 mb-3 text-white-50 small">Menu Utama</h6>

                        <a href="{{ route('admin.dashboard') }}" class="list-group-item sidebar-link {{ Request::is('admin') || Request::is('admin/dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>

                        <div class="sidebar-divider"></div>
                        <h6 class="text-uppercase px-3 mb-3 text-white-50 small">Pengelolaan Konten</h6>

                        <a href="{{ route('admin.users.index') }}" class="list-group-item sidebar-link {{ Request::is('admin/users*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i> Kelola Pengguna
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="list-group-item sidebar-link {{ Request::is('admin/categories*') ? 'active' : '' }}">
                            <i class="fas fa-folder"></i> Kelola Kategori
                        </a>
                        <a href="{{ route('admin.threads.index') }}" class="list-group-item sidebar-link {{ Request::is('admin/threads*') ? 'active' : '' }}">
                            <i class="fas fa-comments"></i> Kelola Thread
                        </a>
                        <a href="{{ route('admin.comments.index') }}" class="list-group-item sidebar-link {{ Request::is('admin/comments*') ? 'active' : '' }}">
                            <i class="fas fa-comment"></i> Kelola Komentar
                        </a>

                        <div class="sidebar-divider"></div>
                        <h6 class="text-uppercase px-3 mb-3 text-white-50 small">Pengaturan</h6>

                        <a href="{{ route('admin.profile') }}" class="list-group-item sidebar-link {{ Request::is('admin/profile*') ? 'active' : '' }}">
                            <i class="fas fa-user-cog"></i> Profil Admin
                        </a>
                    </div>

                    <!-- Sidebar Footer -->
                    <div class="sidebar-footer mt-auto">
                        <a href="{{ url('/') }}" class="list-group-item sidebar-link">
                            <i class="fas fa-home"></i> Kembali ke Situs
                        </a>

                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();"
                           class="list-group-item sidebar-link">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                        <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 col-lg-10 px-0">
                <!-- Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light">
                    <div class="container-fluid">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarContent">
                            <ul class="navbar-nav ms-auto">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-bell me-2"></i>
                                        <span class="badge bg-danger">3</span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#">Notifikasi 1</a></li>
                                        <li><a class="dropdown-item" href="#">Notifikasi 2</a></li>
                                        <li><a class="dropdown-item" href="#">Notifikasi 3</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-user-circle me-2"></i>{{ Auth::user()->name }}
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="fas fa-user me-2"></i>Profil</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Pengaturan</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('logout') }}"
                                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                               <i class="fas fa-sign-out-alt me-2"></i>{{ __('Logout') }}
                                            </a>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                                @csrf
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <!-- Page Content -->
                <div class="content">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
