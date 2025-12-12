<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPNC EduQuiz - Nền tảng ôn thi trắc nghiệm</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #a855f7;
            --bg-color: #f8fafc;
            --text-dark: #1e293b;
        }

        body { font-family: 'Nunito', sans-serif; background-color: var(--bg-color); color: #334155; }

        /* --- NAVBAR --- */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .navbar-brand { font-weight: 900; font-size: 1.5rem; color: var(--primary-color) !important; letter-spacing: -0.5px; }
        
        .navbar-nav .nav-link {
            font-weight: 700; color: #475569 !important; font-size: 0.95rem;
            margin: 0 12px; position: relative; transition: 0.3s;
        }
        .navbar-nav .nav-link:hover { color: var(--primary-color) !important; }
        
        .btn-login { color: var(--text-dark); font-weight: 700; border: none; background: transparent; padding: 8px 20px; text-decoration: none; }
        .btn-login:hover { color: var(--primary-color); }
        
        .btn-register {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white; font-weight: 700; border-radius: 50px; padding: 8px 25px; border: none;
            box-shadow: 0 4px 6px rgba(99, 102, 241, 0.2); transition: 0.3s; text-decoration: none;
        }
        .btn-register:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(99, 102, 241, 0.3); color: white; }

        /* --- HERO SECTION --- */
        .hero-section { padding: 120px 0 80px; background: white; position: relative; overflow: hidden; }
        .blob { position: absolute; width: 400px; height: 400px; border-radius: 50%; filter: blur(80px); opacity: 0.2; z-index: 0; }
        .blob-1 { top: -100px; left: -100px; background: var(--primary-color); }
        .blob-2 { bottom: -100px; right: -100px; background: var(--secondary-color); }
        
        /* Categories Pills */
        .category-scroll {
            display: flex; flex-wrap: wrap; gap: 15px; justify-content: center;
            margin-top: 40px; position: relative; z-index: 2;
        }
        .category-pill {
            background: white; padding: 12px 28px; border-radius: 30px; border: 1px solid #e2e8f0;
            font-weight: 700; color: #64748b; text-decoration: none; transition: 0.3s;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03); display: flex; align-items: center;
        }
        .category-pill i { margin-right: 8px; color: var(--primary-color); font-size: 1.1rem; }
        .category-pill:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); border-color: var(--primary-color); color: var(--primary-color); }

        /* --- SECTIONS --- */
        .section-title { font-weight: 800; color: var(--text-dark); margin-bottom: 40px; position: relative; display: inline-block; }
        .section-title::after {
            content: ''; display: block; width: 50px; height: 3px; background: var(--secondary-color);
            margin: 10px auto 0; border-radius: 2px;
        }

        /* Features */
        .feature-box {
            background: white; padding: 30px; border-radius: 20px; text-align: center; height: 100%;
            border: 1px solid #f1f5f9; transition: 0.3s;
        }
        .feature-box:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-color: #e0e7ff; }
        .feature-icon { font-size: 2.5rem; margin-bottom: 20px; background: -webkit-linear-gradient(45deg, var(--primary-color), var(--secondary-color)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

        /* Exam Cards */
        .exam-card {
            background: white; border-radius: 15px; overflow: hidden; border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02); transition: 0.3s; display: block; text-decoration: none; color: inherit; height: 100%;
        }
        .exam-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08); }
        .exam-thumb { height: 160px; width: 100%; object-fit: cover; }
        .exam-body { padding: 20px; }
        .exam-badge { font-size: 0.7rem; font-weight: 800; background: #e0e7ff; color: var(--primary-color); padding: 4px 8px; border-radius: 6px; }

        /* News Cards */
        .news-card { border: none; background: transparent; }
        .news-img { border-radius: 15px; height: 200px; object-fit: cover; margin-bottom: 15px; }
        .news-date { font-size: 0.8rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; }
        .news-title { font-weight: 800; color: var(--text-dark); font-size: 1.1rem; line-height: 1.4; transition: 0.2s; }
        .news-title:hover { color: var(--primary-color); }

        /* Footer */
        footer { background: #f1f5f9; padding: 60px 0 20px; margin-top: 80px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fa-solid fa-shapes me-2"></i>SPNC Quiz</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="#features">Tính năng</a></li>
                    <li class="nav-item"><a class="nav-link" href="#explore">Khám phá đề thi</a></li>
                    <li class="nav-item"><a class="nav-link" href="#news">Tin tức</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Liên hệ</a></li>
                </ul>

                <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-register shadow-sm">
                            <i class="fa-solid fa-gauge me-2"></i>Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn-login">Đăng ký</a>
                        <a href="{{ route('login') }}" class="btn btn-register">Đăng nhập</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <section class="hero-section text-center">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="container" style="position: relative; z-index: 1;">
            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-bold mb-3">
                🚀 Nền tảng ôn thi số 1 Việt Nam
            </span>
            <h1 class="display-4 fw-black mb-3 text-dark" style="font-weight: 900;">
                Ôn thi THPT Quốc gia <br>
                <span style="background: linear-gradient(to right, #6366f1, #ec4899); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Chưa bao giờ dễ đến thế</span>
            </h1>
            <p class="lead text-muted mb-5" style="max-width: 700px; margin: 0 auto;">
                Hệ thống trắc nghiệm thông minh, phân tích điểm yếu và lộ trình cá nhân hóa giúp bạn chinh phục điểm 10 môn Tin học.
            </p>
            
            <div class="d-flex justify-content-center gap-3">
                <a href="#explore" class="btn btn-register px-5 py-3 shadow-lg" style="font-size: 1.1rem;">Bắt đầu ngay</a>
                <a href="#features" class="btn btn-login border bg-white px-4 py-3 rounded-pill shadow-sm" style="font-size: 1.1rem;">Tìm hiểu thêm</a>
            </div>

            <div class="category-scroll">
                <a href="#" class="category-pill"><i class="fa-solid fa-microchip"></i>Phần cứng</a>
                <a href="#" class="category-pill"><i class="fa-solid fa-network-wired"></i>Mạng MT</a>
                <a href="#" class="category-pill"><i class="fa-solid fa-database"></i>Cơ sở dữ liệu</a>
                <a href="#" class="category-pill"><i class="fa-solid fa-code"></i>Lập trình C++</a>
                <a href="#" class="category-pill"><i class="fa-brands fa-python"></i>Python</a>
            </div>
        </div>
    </section>

    <section id="features" class="py-5">
        <div class="container">
            <div class="text-center">
                <h2 class="section-title">Tính năng nổi bật</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-box">
                        <i class="fa-solid fa-bolt feature-icon"></i>
                        <h5 class="fw-bold">Thi thử tốc độ</h5>
                        <p class="text-muted small">Chế độ thi giới hạn thời gian thực, mô phỏng áp lực phòng thi giúp bạn rèn luyện bản lĩnh.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <i class="fa-solid fa-chart-pie feature-icon"></i>
                        <h5 class="fw-bold">Phân tích điểm số</h5>
                        <p class="text-muted small">Biểu đồ trực quan chỉ rõ phần kiến thức bạn đang yếu (SQL, Python, hay Phần cứng...).</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <i class="fa-solid fa-users feature-icon"></i>
                        <h5 class="fw-bold">Hỏi đáp 24/7</h5>
                        <p class="text-muted small">Gặp câu khó? Đăng lên diễn đàn để nhận lời giải chi tiết từ thầy cô và bạn bè.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="explore" class="py-5 bg-light">
        <div class="container">
            <div class="text-center">
                <h2 class="section-title">Khám phá Đề thi</h2>
            </div>
            
            <div class="d-flex justify-content-center gap-2 mb-5 flex-wrap">
                <button class="btn btn-dark rounded-pill px-4 btn-sm fw-bold">Mới nhất</button>
                <button class="btn btn-white border rounded-pill px-4 btn-sm fw-bold">Nổi bật</button>
                <button class="btn btn-white border rounded-pill px-4 btn-sm fw-bold">Lập trình</button>
                <button class="btn btn-white border rounded-pill px-4 btn-sm fw-bold">CSDL</button>
            </div>

            <div class="row g-4">
                <div class="col-md-3 col-sm-6">
                    <a href="#" class="exam-card h-100">
                        <img src="https://img.freepik.com/free-vector/online-test-concept-illustration_114360-5473.jpg" class="exam-thumb">
                        <div class="exam-body">
                            <span class="exam-badge">THPT 2025</span>
                            <h6 class="fw-bold mt-2 mb-2 text-truncate">Đề thi thử Tốt nghiệp THPT Quốc gia</h6>
                            <div class="d-flex justify-content-between align-items-center text-muted small">
                                <span><i class="fa-regular fa-user me-1"></i> Cô Thảo</span>
                                <span><i class="fa-solid fa-play me-1"></i> 1.5k</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="#" class="exam-card h-100">
                        <img src="https://img.freepik.com/free-vector/programming-concept-illustration_114360-1351.jpg" class="exam-thumb">
                        <div class="exam-body">
                            <span class="exam-badge">Python</span>
                            <h6 class="fw-bold mt-2 mb-2 text-truncate">Kiểm tra 1 tiết: Cấu trúc rẽ nhánh</h6>
                            <div class="d-flex justify-content-between align-items-center text-muted small">
                                <span><i class="fa-regular fa-user me-1"></i> Thầy Hùng</span>
                                <span><i class="fa-solid fa-play me-1"></i> 800</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="#" class="exam-card h-100">
                        <img src="https://img.freepik.com/free-vector/database-concept-illustration_114360-642.jpg" class="exam-thumb">
                        <div class="exam-body">
                            <span class="exam-badge">SQL</span>
                            <h6 class="fw-bold mt-2 mb-2 text-truncate">Ôn tập chương 3: Hệ quản trị CSDL</h6>
                            <div class="d-flex justify-content-between align-items-center text-muted small">
                                <span><i class="fa-regular fa-user me-1"></i> Admin</span>
                                <span><i class="fa-solid fa-play me-1"></i> 2.1k</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="#" class="exam-card h-100">
                        <img src="https://img.freepik.com/free-vector/computer-troubleshooting-concept-illustration_114360-7616.jpg" class="exam-thumb">
                        <div class="exam-body">
                            <span class="exam-badge">Phần cứng</span>
                            <h6 class="fw-bold mt-2 mb-2 text-truncate">Trắc nghiệm tổng hợp Phần cứng</h6>
                            <div class="d-flex justify-content-between align-items-center text-muted small">
                                <span><i class="fa-regular fa-user me-1"></i> GV Tin</span>
                                <span><i class="fa-solid fa-play me-1"></i> 500</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            
            <div class="text-center mt-5">
                <a href="#" class="btn btn-outline-primary rounded-pill px-5 fw-bold">Xem tất cả đề thi</a>
            </div>
        </div>
    </section>

    <section id="news" class="py-5">
        <div class="container">
            <div class="text-center">
                <h2 class="section-title">Tin tức & Sự kiện</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="news-card">
                        <img src="https://img.freepik.com/free-photo/medium-shot-student-taking-notes_23-2148888836.jpg" class="news-img w-100">
                        <div class="news-date">15 Tháng 10, 2025</div>
                        <a href="#" class="news-title d-block text-decoration-none mt-2">Cấu trúc đề thi THPT Quốc gia môn Tin học năm 2025 có gì mới?</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="news-card">
                        <img src="https://img.freepik.com/free-photo/group-students-library_23-2148166345.jpg" class="news-img w-100">
                        <div class="news-date">12 Tháng 10, 2025</div>
                        <a href="#" class="news-title d-block text-decoration-none mt-2">Tổng hợp 50 câu trắc nghiệm Python hay gặp nhất trong đề thi.</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="news-card">
                        <img src="https://img.freepik.com/free-photo/focused-student-learning-library_23-2149215403.jpg" class="news-img w-100">
                        <div class="news-date">10 Tháng 10, 2025</div>
                        <a href="#" class="news-title d-block text-decoration-none mt-2">Kinh nghiệm đạt điểm 10 môn Tin học từ thủ khoa năm ngoái.</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h4 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-shapes me-2 text-primary"></i>SPNC Quiz</h4>
                    <p class="text-muted small">Nền tảng công nghệ giáo dục giúp học sinh tiếp cận kiến thức Tin học một cách chủ động, thú vị và hiệu quả.</p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="text-secondary fs-5"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#" class="text-secondary fs-5"><i class="fa-brands fa-youtube"></i></a>
                        <a href="#" class="text-secondary fs-5"><i class="fa-brands fa-tiktok"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-6 mb-4">
                    <h6 class="fw-bold mb-3">Về chúng tôi</h6>
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Giới thiệu</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Điều khoản</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Bảo mật</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-6 mb-4">
                    <h6 class="fw-bold mb-3">Hỗ trợ</h6>
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Trung tâm trợ giúp</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Báo lỗi</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Liên hệ</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6 class="fw-bold mb-3">Liên hệ</h6>
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-2"><i class="fa-solid fa-location-dot me-2"></i> Trường ĐH Sư Phạm, TP.HCM</li>
                        <li class="mb-2"><i class="fa-solid fa-envelope me-2"></i> contact@spnc-quiz.edu.vn</li>
                        <li class="mb-2"><i class="fa-solid fa-phone me-2"></i> 0909 123 456</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center small text-muted">
                &copy; 2025 SPNC Quiz. All rights reserved. Built with Laravel.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>