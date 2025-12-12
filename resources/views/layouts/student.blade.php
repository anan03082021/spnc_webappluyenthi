<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SPNC EduQuiz')</title> <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #a855f7;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --bg-color: #f8fafc;
            --card-radius: 20px;
        }
        body { font-family: 'Nunito', sans-serif; background-color: var(--bg-color); color: #334155; display: flex; flex-direction: column; min-height: 100vh; }
        
        /* Navbar */
        .navbar { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 12px 0; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); }
        .navbar-brand { font-weight: 900; font-size: 1.5rem; color: var(--primary-color) !important; letter-spacing: -0.5px; }
        .nav-link { font-weight: 700; color: var(--text-gray) !important; margin: 0 8px; transition: 0.3s; font-size: 0.95rem; }
        .nav-link:hover, .nav-link.active { color: var(--primary-color) !important; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #e0e7ff; }

        /* Footer */
        footer { background: white; padding: 40px 0; margin-top: auto; border-top: 1px solid #e2e8f0; text-align: center; }
        
        /* CSS Riêng cho từng trang con */
        @stack('styles')
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('student.dashboard') }}">
                <i class="fa-solid fa-shapes me-2"></i> SPNC<span style="color: #1e293b;">Quiz</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="userNav">
                <ul class="navbar-nav mx-auto">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">Trang chủ</a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('student.exams.explore') ? 'active' : '' }}" href="{{ route('student.exams.explore') }}">Khám phá đề thi</a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('student.exams.history') ? 'active' : '' }}" href="{{ route('student.exams.history') }}">Thư viện của tôi</a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('student.progress') ? 'active' : '' }}" href="{{ route('student.progress') }}">Tiến độ học tập</a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('student.documents.index') ? 'active' : '' }}" href="{{ route('student.documents.index') }}">Tài liệu</a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('forum.*') ? 'active' : '' }}" href="{{ route('forum.index') }}">Diễn đàn</a>
    </li>
</ul>

                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="user-avatar me-2">
                        @else
                            <div class="user-avatar bg-primary text-white d-flex align-items-center justify-content-center me-2" style="font-weight: bold;">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif
                        <span class="fw-bold text-dark d-none d-lg-block">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 mt-2">
                        <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="fa-solid fa-user-gear me-2 text-muted"></i> Hồ sơ cá nhân</a></li>
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

    <div class="container py-4">
        @yield('content')
    </div>

    <footer>
        <div class="container">
            <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-shapes"></i> SPNC Quiz</h5>
            <p class="text-muted small mb-4">Hệ thống ôn luyện trắc nghiệm thông minh dành cho học sinh THPT.</p>
            <p class="small text-muted mb-0">&copy; 2025 SPNC Edutech. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>