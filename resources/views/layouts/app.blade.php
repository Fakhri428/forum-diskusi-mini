<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Add this in your app.blade.php -->
<meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FAKEOYER') }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Fonts & Styles -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        :root {
            --primary-color: #4e54c8;
            --primary-gradient: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            --secondary-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --sidebar-bg: #2c3e50;
            --sidebar-active: #34495e;
            --sidebar-hover: #3d5a80;
            --main-bg: #f0f2f5;
            --card-bg: #ffffff;
            --text-light: #f8f9fa;
            --text-dark: #333;
        }

        body {
            overflow-x: hidden;
            background-color: var(--main-bg);
            font-family: 'Nunito', sans-serif;
        }

        /* Navbar Styling */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1040;
            background: var(--primary-gradient) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand, .nav-link, .navbar .navbar-nav .nav-link {
            color: var(--text-light) !important;
            font-weight: 600;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
        }

        /* Sidebar Styling */
        #sidebar {
            position: fixed;
            top: 56px;
            left: 0;
            width: 250px;
            height: 100vh;
            background-color: var(--sidebar-bg);
            padding-top: 1rem;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            z-index: 1030;
        }

        #sidebar.collapsed {
            margin-left: -250px;
        }

        .sidebar-link {
            color: var(--text-light) !important;
            border: none;
            border-radius: 0;
            padding: 12px 20px;
            margin-bottom: 5px;
            font-weight: 600;
            background-color: transparent;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover {
            background-color: var(--sidebar-hover) !important;
            color: white !important;
            transform: translateX(5px);
        }

        .sidebar-link.active {
            background-color: var(--sidebar-active) !important;
            border-left: 4px solid #38ef7d;
        }

        .sidebar-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Content Area */
        #main-content {
            margin-left: 250px;
            margin-top: 56px;
            padding: 1.5rem;
            transition: margin-left 0.3s ease;
            min-height: calc(100vh - 56px);
        }

        #main-content.collapsed {
            margin-left: 0;
        }

        /* Toggle Button */
        .toggle-btn {
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1050;
            border: none;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 50%;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .toggle-btn:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        /* Dropdown Menu */
        .dropdown-menu {
            background-color: #fff;
            border: none;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 0.5rem 0;
        }

        .dropdown-item {
            padding: 0.5rem 1.5rem;
            transition: all 0.2s;
        }

        .dropdown-item:hover {
            background-color: #f0f2f5;
            color: var(--primary-color);
        }

        /* Responsive */
        @media (max-width: 767.98px) {
            #sidebar {
                margin-left: -250px;
            }

            #sidebar.active {
                margin-left: 0;
            }

            #main-content {
                margin-left: 0 !important;
            }

            .toggle-btn {
                left: auto;
                right: 10px;
            }
        }

        /* Cards and other elements */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 1.5rem;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
        }

        .btn-success {
            background: var(--secondary-gradient);
            border: none;
        }
    </style>

    @yield('styles')
</head>

<body>
    <div id="app">
        {{-- Navbar --}}
        <nav class="navbar navbar-expand-md navbar-dark shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="fas fa-comments me-2"></i>Disquseria
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('threads.index') }}">
                                <i class="fas fa-comments me-1"></i>Thread
                            </a>
                        </li>
                        @auth
                            <li class="nav-item">
                                <span class="nav-link">
                                    <i class="fas fa-user-tag me-1"></i>{{ auth()->user()->role }}
                                </span>
                            </li>
                        @endauth
                    </ul>

                    <!-- Right Side -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">
                                        <i class="fas fa-sign-in-alt me-1"></i>{{ __('Login') }}
                                    </a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">
                                        <i class="fas fa-user-plus me-1"></i>{{ __('Register') }}
                                    </a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-user-circle me-1"></i>{{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('profile') }}">
                                        <i class="fas fa-id-card me-2"></i>{{ __('Profile') }}
                                    </a>

                                    @auth
                                        @if(auth()->user()->role === 'admin')
                                            <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                                <i class="fas fa-user-shield me-2"></i>Admin Dashboard
                                            </a>
                                        @elseif(auth()->user()->role === 'moderator')
                                            <a class="dropdown-item" href="{{ route('moderator.dashboard') }}">
                                                <i class="fas fa-user-cog me-2"></i>Moderator Dashboard
                                            </a>
                                        @endif
                                    @endauth

                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>{{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        {{-- Toggle Button --}}
        <button class="btn toggle-btn" id="toggleSidebar">☰</button>

        {{-- Sidebar --}}
        <div id="sidebar">
            <div class="list-group list-group-flush">
                <a href="{{ url('/home') }}" class="list-group-item sidebar-link {{ Request::is('home') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>Home
                </a>
                <a href="{{ url('/threads') }}" class="list-group-item sidebar-link {{ Request::is('threads*') ? 'active' : '' }}">
                    <i class="fas fa-comments"></i>Diskusi
                </a>
                <a href="{{ url('/statistik') }}" class="list-group-item sidebar-link {{ Request::is('statistik') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>Statistik
                </a>
                <a href="{{ url('/diskusi-saya') }}" class="list-group-item sidebar-link {{ Request::is('diskusi-saya') ? 'active' : '' }}">
                    <i class="fas fa-comment-dots"></i>Diskusi Saya
                </a>
                <a href="{{ url('/komentar-saya') }}" class="list-group-item sidebar-link {{ Request::is('komentar-saya') ? 'active' : '' }}">
                    <i class="fas fa-reply"></i>Komentar Saya
                </a>
                <a href="{{ url('/notifikasi') }}" class="list-group-item sidebar-link {{ Request::is('notifikasi') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i>Notifikasi
                </a>
            </div>
        </div>

        {{-- Konten Utama --}}
        <div id="main-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    {{-- Toggle Script --}}
    <script>
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
        });

        // Add active class based on current page
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const sidebarLinks = document.querySelectorAll('.sidebar-link');

            sidebarLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (currentPath === href || currentPath.startsWith(href)) {
                    link.classList.add('active');
                }
            });
        });
    </script>

    @stack('scripts')

    {{-- Tambahkan di resources/views/layouts/app.blade.php atau sejenisnya, sebelum closing body --}}
@if(app()->environment('local'))
<script>
// Debug helper
setTimeout(function() {
    console.log('=== PAGE DEBUG ===');
    console.log('jQuery loaded:', typeof $ !== 'undefined');
    console.log('Bootstrap loaded:', typeof bootstrap !== 'undefined');
    console.log('Reply buttons:', document.querySelectorAll('.reply-btn').length);
    console.log('Reply forms:', document.querySelectorAll('.reply-form').length);

    // Test button clickability
    document.querySelectorAll('.reply-btn').forEach((btn, i) => {
        const rect = btn.getBoundingClientRect();
        const elementAtCenter = document.elementFromPoint(
            rect.left + rect.width / 2,
            rect.top + rect.height / 2
        );

        if (elementAtCenter !== btn) {
            console.warn(`Reply button ${i} might be blocked by:`, elementAtCenter);
        }
    });
}, 1000);

// Test function untuk tombol reply
window.testReply = function(commentId) {
    console.log('Testing reply for comment:', commentId);
    const button = document.querySelector(`[data-comment-id="${commentId}"]`);
    const form = document.getElementById(`reply-form-${commentId}`);

    console.log('Button found:', !!button);
    console.log('Form found:', !!form);

    if (button && form) {
        form.classList.remove('d-none');
        console.log('Form shown successfully');
    }
};
</script>
@endif
</body>

</html>
