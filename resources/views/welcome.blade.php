<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPNC Edutech - Luyện thi THPT Quốc gia Tin học</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            color: #4a5568;
            background-color: #f8f9fa;
        }

        /* Navbar Custom */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 0;
        }
        .nav-link {
            font-weight: 600;
            color: #4a5568 !important;
            margin: 0 10px;
            transition: color 0.3s;
        }
        .nav-link:hover {
            color: #4c6ef5 !important;
        }
        .btn-primary-custom {
            background-color: #4c6ef5;
            border-color: #4c6ef5;
            color: white;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 700;
            transition: all 0.3s;
        }
        .btn-primary-custom:hover {
            background-color: #364fc7;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(76, 110, 245, 0.3);
        }

        /* Hero Section */
        .hero-section {
            padding: 120px 0 80px 0;
            position: relative;
            overflow: hidden;
        }
        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            line-height: 1.2;
            color: #1a202c;
            margin-bottom: 20px;
        }
        .hero-subtitle {
            font-size: 1.15rem;
            color: #718096;
            margin-bottom: 30px;
        }
        
        /* Feature Cards */
        .feature-card {
            background: white;
            border: none;
            border-radius: 20px;
            padding: 40px 30px;
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            z-index: 1;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        }
        .icon-box {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 25px;
        }
        .bg-blue-light { background-color: #e7f5ff; color: #228be6; }
        .bg-green-light { background-color: #ebfbee; color: #40c057; }
        .bg-purple-light { background-color: #f3f0ff; color: #7950f2; }

        /* Stats Section */
        .stats-section {
            background-color: #fff;
            padding: 60px 0;
            border-top: 1px solid #edf2f7;
            border-bottom: 1px solid #edf2f7;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #4c6ef5;
        }

        /* Footer */
        .footer {
            background-color: #1a202c;
            color: #cbd5e0;
            padding: 60px 0 20px;
        }
        .footer h5 { color: white; font-weight: 700; margin-bottom: 20px; }
        .footer a { color: #cbd5e0; text-decoration: none; transition: 0.3s; }
        .footer a:hover { color: #4c6ef5; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="fa-solid fa-graduation-cap text-primary fs-3 me-2"></i>
                <span class="fw-bold fs-4 text-dark">SPNC<span class="text-primary">Edu</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Tính năng</a></li>
                    <li class="nav-item"><a class="nav-link" href="#stats">Thống kê</a></li>
                    
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item ms-3">
                                <a href="{{ url('/dashboard') }}" class="btn btn-primary-custom">
                                    <i class="fa-solid fa-gauge me-2"></i>Dashboard
                                </a>
                            </li>
                        @else
                            <li class="nav-item ms-3">
                                <a href="{{ route('login') }}" class="nav-link">Đăng nhập</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="btn btn-primary-custom">Đăng ký ngay</a>
                                </li>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <span class="badge bg-primary-subtle text-primary mb-3 px-3 py-2 rounded-pill fw-bold">
                        🚀 Nền tảng ôn thi số 1
                    </span>
                    <h1 class="hero-title">Chinh phục điểm 10<br>Môn Tin học THPT</h1>
                    <p class="hero-subtitle">
                        Hệ thống luyện thi trắc nghiệm thông minh, phân tích điểm yếu và lộ trình học tập cá nhân hóa dành riêng cho học sinh THPT.
                    </p>
                    <div class="d-flex gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-primary-custom btn-lg shadow">Bắt đầu ôn tập</a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-primary-custom btn-lg shadow">Tạo tài khoản miễn phí</a>
                            <a href="#features" class="btn btn-outline-secondary btn-lg rounded-pill px-4 fw-bold">Tìm hiểu thêm</a>
                        @endauth
                    </div>
                    
                    <div class="mt-4 text-muted small">
                        <i class="fa-solid fa-check-circle text-success me-1"></i> 100% Miễn phí
                        <span class="mx-2">•</span>
                        <i class="fa-solid fa-check-circle text-success me-1"></i> Cập nhật 2024
                    </div>
                </div>
                
                <div class="col-lg-6 mt-5 mt-lg-0 text-center">
                    <img src="https://png.pngtree.com/png-vector/20250211/ourmid/pngtree-modern-educational-logo-with-open-book-and-arrows-vector-png-image_15444289.png" 
                         alt="Hero Image" class="img-fluid" style="max-height: 450px;">
                </div>
            </div>
        </div>
    </section>

    <section id="stats" class="stats-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="stat-number">500+</div>
                    <div class="text-muted fw-bold">Đề thi chọn lọc</div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="stat-number">10.000+</div>
                    <div class="text-muted fw-bold">Lượt thi thử</div>
                </div>
                <div class="col-md-4">
                    <div class="stat-number">24/7</div>
                    <div class="text-muted fw-bold">Hỗ trợ giải đáp</div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="py-5 bg-light">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h6 class="text-primary fw-bold text-uppercase ls-2">Tại sao chọn chúng tôi?</h6>
                <h2 class="fw-bold display-6">Công cụ ôn tập toàn diện</h2>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card shadow-sm">
                        <div class="icon-box bg-blue-light">
                            <i class="fa-solid fa-laptop-code"></i>
                        </div>
                        <h4>Thi thử như thật</h4>
                        <p class="text-muted">Giao diện làm bài mô phỏng 100% kỳ thi THPT Quốc gia, giúp học sinh làm quen với áp lực thời gian.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card shadow-sm">
                        <div class="icon-box bg-green-light">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <h4>Thống kê chi tiết</h4>
                        <p class="text-muted">Hệ thống tự động phân tích kết quả, vẽ biểu đồ năng lực để bạn biết mình cần cải thiện phần nào.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card shadow-sm">
                        <div class="icon-box bg-purple-light">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <h4>Cộng đồng hỏi đáp</h4>
                        <p class="text-muted">Tính năng diễn đàn tích hợp giúp bạn trao đổi bài tập khó với Giáo viên và bạn bè dễ dàng.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-primary text-white text-center">
        <div class="container py-4">
            <h2 class="fw-bold mb-3">Sẵn sàng chinh phục kỳ thi?</h2>
            <p class="lead mb-4">Đăng ký ngay hôm nay để truy cập kho đề thi và tài liệu không giới hạn.</p>
            <a href="{{ route('register') }}" class="btn btn-light btn-lg rounded-pill px-5 fw-bold text-primary shadow">
                Đăng ký Tài khoản
            </a>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h4 class="fw-bold mb-3 text-white">SPNC<span class="text-primary">Edu</span></h4>
                    <p class="small text-muted">
                        Nền tảng công nghệ giáo dục hỗ trợ học sinh ôn thi THPT Quốc gia môn Tin học hiệu quả, mọi lúc mọi nơi.
                    </p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#"><i class="fa-brands fa-facebook fs-5"></i></a>
                        <a href="#"><i class="fa-brands fa-youtube fs-5"></i></a>
                        <a href="#"><i class="fa-brands fa-github fs-5"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 mb-4 col-6">
                    <h5>Liên kết</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Trang chủ</a></li>
                        <li class="mb-2"><a href="#">Giới thiệu</a></li>
                        <li class="mb-2"><a href="#">Kho đề thi</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 mb-4 col-6">
                    <h5>Hỗ trợ</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Hướng dẫn</a></li>
                        <li class="mb-2"><a href="#">Báo lỗi</a></li>
                        <li class="mb-2"><a href="#">Liên hệ</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Liên hệ</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fa-solid fa-location-dot me-2"></i> Trường ĐH Sư Phạm</li>
                        <li class="mb-2"><i class="fa-solid fa-envelope me-2"></i> contact@spnc-edu.com</li>
                        <li class="mb-2"><i class="fa-solid fa-phone me-2"></i> 0909 123 456</li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary mt-4">
            <div class="text-center">
                &copy; 2025 SPNC Edutech. All rights reserved. Built with Laravel 11 & Bootstrap 5.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>