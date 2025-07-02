<!-- filepath: resources/views/layouts/moderator.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Moderator Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Sidebar Styles - IMPROVED */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #212529 0%, #343a40 100%);
            color: white;
            padding-top: 20px;
            box-shadow: 3px 0 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .sidebar-brand {
            padding: 15px;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-brand h4 {
            font-weight: 700;
            color: #ff8a00;
            margin-bottom: 0;
            letter-spacing: 1px;
        }

        .sidebar-brand p {
            color: rgba(255,255,255,0.7);
            font-size: 0.85rem;
            margin-bottom: 0;
        }

        .sidebar-link {
            color: rgba(231, 107, 6, 0.8);
            border: none;
            border-radius: 6px;
            padding: 12px 20px;
            margin: 4px 10px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .sidebar-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }

        .sidebar-link.active {
            color: white;
            background: rgba(255, 152, 0, 0.2);
            border-left: 4px solid #ff9800;
        }

        .sidebar-link i {
            width: 24px;
            text-align: center;
            margin-right: 8px;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 15px;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 0.85rem;
        }

        .content {
            padding: 20px;
        }

        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        /* Make sidebar work better on mobile */
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
                position: relative;
            }

            .sidebar-link {
                border-radius: 0;
                margin: 0;
            }

            .sidebar-footer {
                display: none;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="d-flex flex-column h-100">
                    <div class="sidebar-brand text-center">
                        <h4>DISQUSERIA</h4>
                        <p>Moderator Panel</p>
                    </div>

                    <div class="list-group px-2">
                        <a href="{{ route('moderator.dashboard') }}" class="list-group-item sidebar-link {{ Request::is('moderator') || Request::is('moderator/dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a href="{{ route('moderator.threads.index') }}" class="list-group-item sidebar-link {{ Request::is('moderator/threads*') ? 'active' : '' }}">
                            <i class="fas fa-comments"></i> Kelola Thread
                        </a>
                        <a href="{{ route('moderator.comments.index') }}" class="list-group-item sidebar-link {{ Request::is('moderator/comments*') ? 'active' : '' }}">
                            <i class="fas fa-comment"></i> Kelola Komentar
                        </a>
                        <a href="{{ route('moderator.reports.index') }}" class="list-group-item sidebar-link {{ Request::is('moderator/reports*') ? 'active' : '' }}">
                            <i class="fas fa-flag"></i> Laporan
                        </a>
                    </div>

                    <div class="sidebar-footer mt-auto">
                        <a href="{{ url('/') }}" class="list-group-item sidebar-link">
                            <i class="fas fa-home"></i> Kembali ke Situs
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-0">
                <!-- Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light sticky-top">
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
                                        <li><a class="dropdown-item" href="#">Thread baru perlu dimoderasi</a></li>
                                        <li><a class="dropdown-item" href="#">2 laporan baru</a></li>
                                        <li><a class="dropdown-item" href="#">Komentar yang dilaporkan</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-user-circle me-2"></i>{{ Auth::user()->name }}
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('moderator.profile') }}"><i class="fas fa-user me-2"></i>Profil</a></li>
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
    <script>
    // Add a small script to handle navbar toggler that also affects sidebar on mobile
    document.addEventListener('DOMContentLoaded', function() {
        const navbarToggler = document.querySelector('.navbar-toggler');
        const sidebar = document.querySelector('.sidebar');

        if (navbarToggler && sidebar) {
            navbarToggler.addEventListener('click', function() {
                sidebar.classList.toggle('show-sidebar');
            });
        }
    });
    </script>
    @yield('scripts')
</body>
</html>
