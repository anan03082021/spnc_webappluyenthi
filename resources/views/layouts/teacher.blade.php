<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Giáo Viên - SPNC EduQuiz')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            /* Màu chủ đạo cho Giáo viên: Indigo (Chuyên nghiệp, Quản lý) */
            --teacher-primary: #4f46e5; 
            --teacher-bg: #f8fafc;
            --text-dark: #1e293b;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: var(--teacher-bg);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* --- NAVBAR --- */
        .teacher-nav {
            background: white;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            padding: 0.8rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .brand-text {
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--teacher-primary);
            letter-spacing: -0.5px;
        }

        .nav-link {
            font-weight: 600;
            color: #64748b !important;
            margin: 0 5px;
            padding: 8px 16px !important;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .nav-link:hover {
            color: var(--teacher-primary) !important;
            background-color: #eef2ff;
        }

        .nav-link.active {
            color: var(--teacher-primary) !important;
            background-color: #eef2ff;
            font-weight: 700;
        }

        .nav-icon {
            margin-right: 8px;
            width: 20px;
            text-align: center;
        }

        /* User Profile Dropdown */
        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e0e7ff;
        }

        /* --- CONTENT WRAPPER --- */
        .main-content {
            flex: 1; /* Đẩy footer xuống đáy */
            padding-top: 30px;
            padding-bottom: 50px;
        }

        /* Footer */
        footer {
            background: white;
            padding: 25px 0;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            font-size: 0.9rem;
            color: #64748b;
        }
        
        /* CSS riêng được push từ các trang con */
        @stack('styles')
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg teacher-nav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center me-4" href="{{ route('teacher.dashboard') }}">
                <i class="fa-solid fa-chalkboard-user me-2 text-indigo-600" style="color: #4f46e5;"></i>
                <span class="brand-text">SPNC Teacher</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#teacherMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="teacherMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}" 
                           href="{{ route('teacher.dashboard') }}">
                            <i class="fa-solid fa-chart-line nav-icon"></i> Tổng quan
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('teacher.exams.*') ? 'active' : '' }}" 
                           href="{{ route('teacher.exams.index') }}">
                            <i class="fa-solid fa-file-signature nav-icon"></i> Đề thi
                        </a>
                    </li>

                    <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('teacher.classes.*') ? 'active' : '' }}" 
       href="{{ route('teacher.classes.index') }}"> <i class="fa-solid fa-users-rectangle nav-icon"></i> Lớp học
    </a>
</li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('teacher.documents.*') ? 'active' : '' }}" 
                           href="{{ route('teacher.documents.index') }}">
                            <i class="fa-solid fa-folder-open nav-icon"></i> Tài liệu
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('forum.*') ? 'active' : '' }}" 
                           href="{{ route('forum.index') }}">
                            <i class="fa-solid fa-comments nav-icon"></i> Diễn đàn
                        </a>
                    </li>
                </ul>

                <div class="dropdown ms-lg-3">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        <div class="text-end me-2 d-none d-lg-block">
                            <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ Auth::user()->name }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Giáo viên</div>
                        </div>
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="user-avatar">
                        @else
                            <div class="user-avatar bg-indigo text-white d-flex align-items-center justify-content-center" 
                                 style="background-color: #4f46e5; font-weight: bold;">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 mt-2">
                        <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="fa-solid fa-user-gear me-2"></i> Hồ sơ</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item py-2 text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-2"></i> Đăng xuất</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                    <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <footer>
        <div class="container">
            <p class="mb-1 fw-bold">SPNC Education System</p>
            <p class="mb-0">© 2025 All rights reserved. Designed for Teachers.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    
    @stack('scripts')
</body>
</html>