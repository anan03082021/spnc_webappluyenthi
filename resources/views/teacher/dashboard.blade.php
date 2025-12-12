@extends('layouts.teacher')

@section('title', 'Tổng quan giảng dạy')

@push('styles')
    <style>
        /* --- ENTERPRISE DASHBOARD STYLE --- */
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; color: #334155; }

        /* 1. Header Area */
        .page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 32px; }
        .welcome-text h1 { font-size: 1.8rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.5px; }
        .welcome-text p { font-size: 0.95rem; color: #64748b; margin-top: 6px; }

        .btn-action-primary {
            background-color: #4f46e5; color: white; padding: 12px 24px; border-radius: 10px;
            font-weight: 600; font-size: 0.95rem; text-decoration: none; border: none;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); transition: all 0.2s;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-action-primary:hover { background-color: #4338ca; transform: translateY(-2px); color: white; }

        /* 2. Overview Stats (SỬA: Grid 3 cột thay vì 4) */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 32px; }
        
        .stat-card-pro {
            background: white; border-radius: 16px; padding: 24px;
            border: 1px solid #e2e8f0; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            display: flex; flex-direction: column; justify-content: space-between; height: 140px;
            transition: 0.2s; position: relative; overflow: hidden;
        }
        .stat-card-pro:hover { border-color: #cbd5e1; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); }
        
        /* Decorative Icon Background */
        .stat-card-pro::after {
            content: ''; position: absolute; right: -10px; bottom: -10px; font-size: 5rem;
            opacity: 0.05; font-family: "Font Awesome 6 Free"; font-weight: 900; pointer-events: none;
        }
        .card-exams::after { content: '\f15c'; }
        .card-students::after { content: '\f0c0'; }
        .card-attempts::after { content: '\f044'; }

        .stat-header { display: flex; align-items: center; gap: 12px; margin-bottom: 15px; }
        .icon-box-sm { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
        
        .bg-indigo-light { background: #e0e7ff; color: #4f46e5; }
        .bg-emerald-light { background: #d1fae5; color: #059669; }
        .bg-blue-light { background: #e0f2fe; color: #0284c7; }

        .stat-title { font-size: 0.9rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-number { font-size: 2.2rem; font-weight: 800; color: #0f172a; line-height: 1; }

        /* 3. Dashboard Layout */
        .dashboard-layout { display: grid; grid-template-columns: 2.5fr 1fr; gap: 32px; }

        .section-card {
            background: white; border-radius: 16px; border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05); margin-bottom: 32px; overflow: hidden;
        }
        .card-head {
            padding: 20px 24px; border-bottom: 1px solid #f1f5f9;
            display: flex; justify-content: space-between; align-items: center;
        }
        .card-head h5 { font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0; }
        .btn-link-sm { font-size: 0.85rem; font-weight: 600; color: #4f46e5; text-decoration: none; }
        .btn-link-sm:hover { text-decoration: underline; }

        .table-responsive { width: 100%; overflow-x: auto; }
        .table-clean { width: 100%; border-collapse: collapse; }
        .table-clean th {
            text-align: left; padding: 16px 24px; font-size: 0.75rem; font-weight: 700;
            text-transform: uppercase; color: #64748b; background: #f8fafc; border-bottom: 1px solid #e2e8f0;
        }
        .table-clean td { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-size: 0.95rem; color: #334155; }
        .table-clean tr:last-child td { border-bottom: none; }
        .table-clean tr:hover td { background-color: #fcfcfd; }

        .user-cell { display: flex; align-items: center; gap: 12px; }
        .avatar-circle { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1px solid #e2e8f0; }
        .text-main { font-weight: 600; color: #0f172a; display: block; }
        .text-sub { font-size: 0.8rem; color: #64748b; }

        .score-pill { display: inline-block; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 0.85rem; min-width: 45px; text-align: center; }
        .pill-success { background: #dcfce7; color: #166534; }
        .pill-danger { background: #fee2e2; color: #991b1b; }

        .quick-action-list { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; padding: 24px; }
        .action-card {
            background: #f8fafc; border: 1px solid transparent; border-radius: 12px; padding: 20px 15px;
            text-align: center; text-decoration: none; transition: 0.2s;
        }
        .action-card:hover { background: white; border-color: #4f46e5; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1); transform: translateY(-3px); }
        .action-card i { font-size: 1.8rem; margin-bottom: 10px; display: block; }
        .action-text { font-size: 0.85rem; font-weight: 700; color: #475569; display: block; }

        .top-student-list { padding: 0 24px 24px; }
        .student-row { display: flex; align-items: center; padding: 12px 0; border-bottom: 1px dashed #e2e8f0; }
        .student-row:last-child { border-bottom: none; }
        .rank-badge {
            width: 24px; height: 24px; background: #f1f5f9; color: #64748b; font-weight: 700; font-size: 0.75rem;
            border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-right: 12px;
        }
        .rank-1 { background: #fef3c7; color: #d97706; }

        @media (max-width: 992px) {
            .stats-grid { grid-template-columns: 1fr; } /* Sửa thành 1 cột trên mobile cho đẹp */
            .dashboard-layout { grid-template-columns: 1fr; }
        }
        @media (max-width: 576px) {
            .stats-grid { grid-template-columns: 1fr; }
            .page-header { flex-direction: column; align-items: flex-start; gap: 15px; }
        }
    </style>
@endpush

@section('content')

    <div class="page-header" data-aos="fade-down">
        <div class="welcome-text">
            <h1>Tổng quan lớp học</h1>
            <p>Xin chào, Thầy/Cô <strong>{{ Auth::user()->name }}</strong>! Dưới đây là báo cáo hoạt động mới nhất.</p>
        </div>
        <div>
            <a href="{{ route('teacher.exams.create_quick') }}" class="btn-action-primary">
                <i class="fa-solid fa-bolt"></i> Tạo đề nhanh
            </a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card-pro card-exams" data-aos="fade-up" data-aos-delay="0">
            <div class="stat-header">
                <div class="icon-box-sm bg-indigo-light"><i class="fa-regular fa-file-code"></i></div>
                <span class="stat-title">Tổng đề thi</span>
            </div>
            <div class="stat-number">{{ $totalExams ?? 0 }}</div>
        </div>
        
        <div class="stat-card-pro card-students" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-header">
                <div class="icon-box-sm bg-emerald-light"><i class="fa-solid fa-users"></i></div>
                <span class="stat-title">Học sinh</span>
            </div>
            <div class="stat-number">{{ $totalStudents ?? 0 }}</div>
        </div>

        <div class="stat-card-pro card-attempts" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-header">
                <div class="icon-box-sm bg-blue-light"><i class="fa-solid fa-pen-to-square"></i></div>
                <span class="stat-title">Lượt làm bài</span>
            </div>
            <div class="stat-number">{{ $totalAttempts ?? 0 }}</div>
        </div>
    </div>

    <div class="dashboard-layout">
        
        <div class="layout-left">
            
            <div class="section-card" data-aos="fade-up">
                <div class="card-head">
                    <h5><i class="fa-solid fa-chart-simple me-2 text-primary"></i>Phổ điểm học sinh</h5>
                    <select class="form-select form-select-sm w-auto border-0 bg-light fw-bold text-secondary">
                        <option>Tất cả đề thi</option>
                    </select>
                </div>
                <div class="p-4">
                    <div style="height: 300px; width: 100%;">
                        <canvas id="scoreChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="section-card" data-aos="fade-up" data-aos-delay="100">
                <div class="card-head">
                    <h5><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Bài nộp gần đây</h5>
                    <a href="#" class="btn-link-sm">Xem tất cả</a>
                </div>
                <div class="table-responsive">
                    <table class="table-clean">
                        <thead>
                            <tr>
                                <th width="40%">Học sinh</th>
                                <th width="30%">Đề thi</th>
                                <th width="20%">Thời gian</th>
                                <th width="10%" class="text-end">Điểm</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentResults ?? [] as $result)
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <img src="https://ui-avatars.com/api/?name={{ $result->user->name }}&background=random" class="avatar-circle">
                                        <div>
                                            <span class="text-main">{{ $result->user->name }}</span>
                                            <span class="text-sub">ID: #{{ $result->user->id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-main" style="font-size: 0.9rem;">{{ $result->exam->title }}</span>
                                </td>
                                <td class="text-muted" style="font-size: 0.85rem;">
                                    {{ $result->created_at->format('H:i - d/m/Y') }}
                                </td>
                                <td class="text-end">
                                    <span class="score-pill {{ $result->score >= 5 ? 'pill-success' : 'pill-danger' }}">
                                        {{ $result->score }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    Chưa có dữ liệu bài làm mới.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="layout-right">
            
            <div class="section-card" data-aos="fade-left">
                <div class="card-head">
                    <h5>Thao tác nhanh</h5>
                </div>
                <div class="quick-action-list">
                    <a href="{{ route('teacher.exams.create') }}" class="action-card">
                        <i class="fa-regular fa-file-lines text-primary"></i>
                        <span class="action-text">Soạn thủ công</span>
                    </a>
                    <a href="{{ route('teacher.exams.create_quick') }}" class="action-card">
                        <i class="fa-solid fa-bolt text-warning"></i>
                        <span class="action-text">Nhập nhanh</span>
                    </a>
                    <a href="{{ route('teacher.documents.index') }}" class="action-card">
                        <i class="fa-solid fa-cloud-arrow-up text-success"></i>
                        <span class="action-text">Up tài liệu</span>
                    </a>
                    <a href="{{ route('forum.index') }}" class="action-card">
                        <i class="fa-regular fa-comments text-info"></i>
                        <span class="action-text">Hỗ trợ HS</span>
                    </a>
                </div>
            </div>

            <div class="section-card" data-aos="fade-left" data-aos-delay="100">
                <div class="card-head">
                    <h5>Học sinh tiêu biểu</h5>
                </div>
                <div class="top-student-list">
                    @forelse($topStudents ?? [] as $index => $student)
                    <div class="student-row">
                        <div class="rank-badge {{ $index == 0 ? 'rank-1' : '' }}">#{{ $index + 1 }}</div>
                        <div class="user-cell flex-grow-1">
                            <img src="https://ui-avatars.com/api/?name={{ $student->name }}" class="avatar-circle" style="width: 32px; height: 32px;">
                            <div>
                                <span class="text-main" style="font-size: 0.9rem;">{{ $student->name }}</span>
                            </div>
                        </div>
                        <div class="fw-bold text-primary">{{ $student->avg_score }}</div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted small">Chưa có xếp hạng.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // 1. Chuẩn bị dữ liệu PHP sạch sẽ trước khi đưa vào JS
        @php
            // Nếu không có dữ liệu thì mặc định là mảng 4 số 0
            $safeChartData = $chartData ?? [0, 0, 0, 0];
        @endphp

        // 2. Truyền biến đã xử lý vào JS (An toàn tuyệt đối)
        const chartData = @json($safeChartData); 
        
        const ctx = document.getElementById('scoreChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Yếu (0-5)', 'Trung bình (5-7)', 'Khá (7-9)', 'Giỏi (9-10)'],
                datasets: [{
                    label: 'Số học sinh',
                    data: chartData,
                    backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6', '#10b981'],
                    borderRadius: 6, 
                    barThickness: 45
                }]
            },
            options: {
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { borderDash: [5, 5] }, 
                        ticks: { stepSize: 1 } 
                    },
                    x: { 
                        grid: { display: false } 
                    }
                }
            }
        });
    </script>
@endpush