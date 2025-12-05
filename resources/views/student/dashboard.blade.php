<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Học Sinh - SPNC Edutech</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f3f4f6; /* Xám nhạt dịu mắt */
            color: #4a5568;
        }

        /* Navbar Style */
        .navbar {
            background-color: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            padding: 12px 0;
        }
        .navbar-brand {
            font-weight: 800;
            color: #1a202c;
        }
        
        /* Card Style Custom */
        .dashboard-card {
            background: white;
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            overflow: hidden;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        }

        /* Chart Section */
        .chart-container {
            position: relative;
            padding: 20px;
        }

        /* Exam Card Details */
        .exam-meta {
            font-size: 0.9rem;
            color: #718096;
            margin-bottom: 8px;
        }
        .badge-difficulty {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-easy { background-color: #def7ec; color: #03543f; }
        .badge-medium { background-color: #feecdc; color: #9c4221; }
        .badge-hard { background-color: #fde8e8; color: #9b1c1c; }

        /* Buttons */
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 12px;
            width: 100%;
            transition: opacity 0.3s;
        }
        .btn-gradient:hover {
            opacity: 0.9;
            color: white;
        }

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }
        .welcome-pattern {
            position: absolute;
            right: 0;
            top: 0;
            opacity: 0.1;
            font-size: 150px;
            transform: translate(20%, -20%);
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="fa-solid fa-graduation-cap text-primary fs-3 me-2"></i>
                <span>SPNC<span class="text-primary">Edu</span></span>
            </a>

            <div class="ms-auto d-flex align-items-center gap-3">
                <a href="{{ route('student.documents.index') }}" class="btn btn-light rounded-pill fw-bold text-primary border shadow-sm">
                    <i class="fa-solid fa-folder-open me-1"></i> Kho tài liệu
                </a>
                <a href="{{ route('forum.index') }}" class="btn btn-light rounded-pill fw-bold text-success border shadow-sm">
                    <i class="fa-solid fa-comments me-1"></i> Hỏi đáp
                </a>

                <div class="dropdown">
                    <button class="btn btn-white d-flex align-items-center p-1 rounded-pill border shadow-sm" type="button" data-bs-toggle="dropdown">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="rounded-circle" style="width: 38px; height: 38px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center" style="width: 38px; height: 38px; font-weight: bold;">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif
                        <span class="fw-bold mx-2 d-none d-md-block">{{ Auth::user()->name }}</span>
                        <i class="fa-solid fa-chevron-down small text-muted me-2"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2">
                        <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="fa-regular fa-user me-2"></i>Hồ sơ cá nhân</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i>Đăng xuất</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        
        <div class="welcome-banner shadow">
            <i class="fa-solid fa-award welcome-pattern"></i>
            <h2 class="fw-bold">Xin chào, {{ Auth::user()->name }}! 👋</h2>
            <p class="mb-0 opacity-75">Hôm nay bạn muốn chinh phục kiến thức nào?</p>
        </div>

        <div class="row mb-5">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="dashboard-card h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4"><i class="fa-solid fa-chart-pie text-primary me-2"></i>Tiến độ học tập</h5>
                        <div class="chart-container" style="height: 250px; display: flex; justify-content: center;">
                            <canvas id="progressChart"></canvas>
                        </div>
                        <div class="text-center mt-3">
                            <span class="badge bg-light text-dark border">Đã làm: {{ $attemptedExamsCount }}</span>
                            <span class="badge bg-light text-dark border">Chưa làm: {{ $notAttemptedCount }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="dashboard-card h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4"><i class="fa-solid fa-chart-line text-success me-2"></i>Lịch sử phong độ</h5>
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="scoreChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center mb-4">
            <h3 class="fw-bold m-0"><i class="fa-solid fa-list-check text-warning me-2"></i>Đề thi mới nhất</h3>
            <span class="badge bg-primary ms-3 rounded-pill">{{ count($exams) }} đề có sẵn</span>
        </div>

        <div class="row g-4">
            @forelse($exams as $exam)
            <div class="col-md-6 col-lg-4">
                <div class="dashboard-card d-flex flex-column">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="icon-box bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fa-regular fa-file-code fs-4"></i>
                            </div>
                            @php
                                $badgeClass = match($exam->difficulty) {
                                    'easy' => 'badge-easy',
                                    'medium' => 'badge-medium',
                                    'hard' => 'badge-hard',
                                    default => 'badge-medium'
                                };
                                $diffLabel = match($exam->difficulty) {
                                    'easy' => 'Cơ bản',
                                    'medium' => 'Vận dụng',
                                    'hard' => 'Nâng cao',
                                    default => 'Trung bình'
                                };
                            @endphp
                            <span class="badge-difficulty {{ $badgeClass }}">{{ $diffLabel }}</span>
                        </div>
                        
                        <h5 class="card-title fw-bold mb-3 text-truncate" title="{{ $exam->title }}">{{ $exam->title }}</h5>
                        
                        <div class="exam-meta">
                            <i class="fa-regular fa-clock me-2"></i>Thời gian: <strong>{{ $exam->duration }} phút</strong>
                        </div>
                        <div class="exam-meta">
                            <i class="fa-solid fa-list-ol me-2"></i>Số câu hỏi: <strong>{{ $exam->total_questions }} câu</strong>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white border-0 p-4 pt-0 mt-auto">
                        <a href="{{ route('student.exams.show', $exam->id) }}" class="btn btn-gradient shadow-sm">
                            Bắt đầu làm bài <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png" alt="Empty" style="width: 200px; opacity: 0.8">
                    <h5 class="mt-3 text-muted">Chưa có đề thi nào được công bố.</h5>
                </div>
            </div>
            @endforelse
        </div>
        
        <div class="text-center mt-5 mb-5 text-muted small">
            &copy; 2025 SPNC Edutech. Chúc bạn ôn tập tốt!
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Config Chart.js (Giữ nguyên logic cũ nhưng style đẹp hơn)
        Chart.defaults.font.family = "'Nunito', sans-serif";
        Chart.defaults.color = '#718096';

        // 1. Biểu đồ tròn
        const ctxProgress = document.getElementById('progressChart').getContext('2d');
        new Chart(ctxProgress, {
            type: 'doughnut',
            data: {
                labels: ['Đã làm', 'Chưa làm'],
                datasets: [{
                    data: [{{ $attemptedExamsCount }}, {{ $notAttemptedCount }}],
                    backgroundColor: ['#48bb78', '#edf2f7'], // Màu xanh lá và xám nhạt
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%', // Làm mỏng vòng tròn
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                }
            }
        });

        // 2. Biểu đồ đường
        const ctxScore = document.getElementById('scoreChart').getContext('2d');
        new Chart(ctxScore, {
            type: 'line',
            data: {
                labels: {!! $chartLabels !!},
                datasets: [{
                    label: 'Điểm số',
                    data: {{ $chartScores }},
                    borderColor: '#667eea', // Màu tím chủ đạo
                    backgroundColor: 'rgba(102, 126, 234, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4, // Đường cong mềm
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#667eea',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10,
                        grid: { borderDash: [5, 5], color: '#e2e8f0' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    </script>
</body>
</html>