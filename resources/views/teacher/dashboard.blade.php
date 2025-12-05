<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Giáo Viên - SPNC Edutech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8f9fa; color: #4a5568; }
        
        /* Navbar */
        .navbar { background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .nav-link { font-weight: 600; color: #4a5568; margin: 0 10px; }
        .nav-link:hover, .nav-link.active { color: #4c6ef5; }

        /* Stats Cards */
        .stat-card {
            border: none; border-radius: 15px; overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px rgba(0,0,0,0.1); }
        .stat-icon { width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
        
        /* Action Buttons */
        .btn-quick {
            background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;
            text-align: center; color: #4a5568; text-decoration: none; display: block; transition: 0.3s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .btn-quick:hover {
            border-color: #4c6ef5; color: #4c6ef5; background: #edf2ff;
        }
        .btn-quick i { font-size: 30px; margin-bottom: 10px; display: block; }

        .table-custom th { background-color: #f8f9fa; font-weight: 700; color: #718096; }
        .table-custom td { vertical-align: middle; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold" href="#">
                <i class="fa-solid fa-chalkboard-user text-primary fs-3 me-2"></i>
                <span>GV.<span class="text-primary">Portal</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#teacherNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="teacherNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="{{ route('teacher.dashboard') }}">Tổng quan</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('teacher.exams.index') }}">Quản lý Đề thi</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('teacher.documents.index') }}">Tài liệu</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('forum.index') }}">Diễn đàn</a></li>
                    
                    <li class="nav-item dropdown ms-3">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                             @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="rounded-circle me-2" width="35" height="35" style="object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Hồ sơ cá nhân</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger">Đăng xuất</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card stat-card p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary-subtle text-primary me-3">
                            <i class="fa-regular fa-file-code"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Đề thi đã tạo</h6>
                            <h3 class="fw-bold mb-0">12</h3> </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success-subtle text-success me-3">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Học sinh tham gia</h6>
                            <h3 class="fw-bold mb-0">45</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card p-3">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning-subtle text-warning me-3">
                            <i class="fa-solid fa-folder-open"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Tài liệu chia sẻ</h6>
                            <h3 class="fw-bold mb-0">8</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between">
                        <h5 class="fw-bold">Hoạt động gần đây</h5>
                        <a href="{{ route('teacher.exams.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Xem tất cả</a>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="alert alert-light border text-center py-5">
                            <img src="https://cdni.iconscout.com/illustration/premium/thumb/no-data-found-8867280-7265556.png" width="120" style="opacity: 0.5">
                            <p class="text-muted mt-3">Chưa có dữ liệu hoạt động mới.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <h5 class="fw-bold mb-3">Thao tác nhanh</h5>
                <div class="row g-3">
                    <div class="col-6">
                        <a href="{{ route('teacher.exams.create') }}" class="btn-quick">
                            <i class="fa-solid fa-plus-circle text-primary"></i>
                            <span class="fw-bold small">Tạo đề thi</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('teacher.documents.create') }}" class="btn-quick">
                            <i class="fa-solid fa-cloud-arrow-up text-success"></i>
                            <span class="fw-bold small">Up tài liệu</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('forum.index') }}" class="btn-quick">
                            <i class="fa-regular fa-comments text-warning"></i>
                            <span class="fw-bold small">Hỗ trợ HS</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="btn-quick">
                            <i class="fa-solid fa-chart-line text-danger"></i>
                            <span class="fw-bold small">Báo cáo</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>