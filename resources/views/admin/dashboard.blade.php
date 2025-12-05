<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - SPNC Edutech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f1f5f9; overflow-x: hidden; }
        
        /* Sidebar Styles */
        .sidebar {
            width: 260px; height: 100vh; position: fixed; top: 0; left: 0;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: #94a3b8; padding: 20px; z-index: 1000; transition: 0.3s;
        }
        .sidebar-brand {
            color: white; font-size: 1.5rem; font-weight: 800; display: flex; align-items: center;
            margin-bottom: 40px; text-decoration: none; padding-left: 10px;
        }
        .nav-link {
            color: #94a3b8; padding: 12px 15px; border-radius: 10px; margin-bottom: 5px;
            display: flex; align-items: center; font-weight: 600; transition: 0.2s;
        }
        .nav-link:hover, .nav-link.active {
            background-color: rgba(255,255,255,0.1); color: white;
        }
        .nav-link i { width: 25px; font-size: 1.1rem; }
        
        /* Content Area */
        .main-content { margin-left: 260px; padding: 30px; }
        
        /* Cards */
        .stat-card {
            border: none; border-radius: 16px; color: white; padding: 25px;
            position: relative; overflow: hidden; height: 100%;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        }
        .bg-gradient-1 { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
        .bg-gradient-2 { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
        .bg-gradient-3 { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        
        .stat-value { font-size: 2.5rem; font-weight: 800; line-height: 1; margin-bottom: 5px; }
        .stat-label { font-size: 0.9rem; opacity: 0.8; text-transform: uppercase; letter-spacing: 1px; }
        .stat-icon { position: absolute; right: 20px; bottom: 20px; font-size: 4rem; opacity: 0.2; transform: rotate(-15deg); }

        /* User Menu */
        .user-dropdown img { width: 40px; height: 40px; border-radius: 50%; border: 2px solid white; }
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="#" class="sidebar-brand">
            <i class="fa-solid fa-shield-halved text-primary me-2"></i> SPNC Admin
        </a>
        
        <nav class="nav flex-column">
            <a href="{{ route('admin.dashboard') }}" class="nav-link active">
                <i class="fa-solid fa-gauge-high"></i> Dashboard
            </a>
            <p class="text-uppercase small fw-bold mt-4 mb-2 ps-3" style="opacity: 0.5; font-size: 0.75rem;">Quản lý</p>
            <a href="{{ route('admin.users.index') }}" class="nav-link">
                <i class="fa-solid fa-users"></i> Người dùng
            </a>
            <a href="{{ route('admin.exams.index') }}" class="nav-link">
                <i class="fa-solid fa-file-circle-check"></i> Ngân hàng đề
            </a>
            
            <p class="text-uppercase small fw-bold mt-4 mb-2 ps-3" style="opacity: 0.5; font-size: 0.75rem;">Hệ thống</p>
            <a href="#" class="nav-link"><i class="fa-solid fa-gear"></i> Cài đặt</a>
            
            <form method="POST" action="{{ route('logout') }}" class="mt-5">
                @csrf
                <button class="nav-link w-100 text-start text-danger">
                    <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                </button>
            </form>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h3 class="fw-bold text-dark m-0">Tổng quan hệ thống</h3>
                <p class="text-muted m-0">Chào mừng trở lại, {{ Auth::user()->name }}!</p>
            </div>
            <div class="d-flex align-items-center bg-white px-3 py-2 rounded-pill shadow-sm">
                <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.Auth::user()->name }}" class="rounded-circle me-2" width="35" height="35">
                <span class="fw-bold text-dark small">{{ Auth::user()->name }}</span>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card bg-gradient-1">
                    <div class="stat-label">Tổng thành viên</div>
                    <div class="stat-value">{{ $totalUsers }}</div>
                    <div class="small opacity-75"><i class="fa-solid fa-arrow-up"></i> +5 hôm nay</div>
                    <i class="fa-solid fa-users stat-icon"></i>
                    <a href="{{ route('admin.users.index') }}" class="stretched-link"></a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-gradient-2">
                    <div class="stat-label">Đề thi trong kho</div>
                    <div class="stat-value">{{ $totalExams }}</div>
                    <div class="small opacity-75">Đang hoạt động</div>
                    <i class="fa-solid fa-layer-group stat-icon"></i>
                    <a href="{{ route('admin.exams.index') }}" class="stretched-link"></a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-gradient-3">
                    <div class="stat-label">Chờ phê duyệt</div>
                    <div class="stat-value">{{ $pendingExams }}</div>
                    <div class="small opacity-75">Cần xử lý ngay</div>
                    <i class="fa-solid fa-hourglass-half stat-icon"></i>
                    <a href="{{ route('admin.exams.index') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold">Biểu đồ truy cập (Demo)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="adminChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold">Lối tắt quản lý</h5>
                    </div>
                    <div class="list-group list-group-flush p-3">
                        <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action border-0 rounded-3 mb-2 p-3 bg-light">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded p-2 me-3"><i class="fa-solid fa-user-shield"></i></div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Phân quyền User</h6>
                                    <small class="text-muted">Set quyền Giáo viên/Admin</small>
                                </div>
                            </div>
                        </a>
                        <a href="{{ route('admin.exams.index') }}" class="list-group-item list-group-item-action border-0 rounded-3 mb-2 p-3 bg-light">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning text-dark rounded p-2 me-3"><i class="fa-solid fa-check-double"></i></div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Duyệt đề thi</h6>
                                    <small class="text-muted">Kiểm tra nội dung mới</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('adminChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
                datasets: [{
                    label: 'Lượt truy cập',
                    data: [12, 19, 3, 5, 2, 3, 10],
                    borderColor: '#4f46e5',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(79, 70, 229, 0.1)'
                }]
            },
            options: { plugins: { legend: {display: false} }, scales: { y: {beginAtZero: true, grid: {display:false}}, x: {grid: {display:false}} } }
        });
    </script>
</body>
</html>