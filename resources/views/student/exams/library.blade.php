@extends('layouts.student')

@section('title', 'Thư viện cá nhân')

@push('styles')
    <style>
        /* --- LIBRARY PRO STYLE --- */
        body { background-color: #f8fafc; }
        
        /* Ẩn footer để giao diện giống App */
        footer { display: none !important; }

        /* 1. Header & Navigation */
        .library-header {
            text-align: center; margin-bottom: 30px; padding-top: 20px;
        }
        .library-title { font-weight: 800; color: #1e293b; margin-bottom: 5px; }
        .library-subtitle { color: #64748b; font-size: 0.95rem; }

        /* Custom Centered Pills */
        .nav-pills-custom {
            background: white; padding: 6px; border-radius: 50px; display: inline-flex;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;
            margin-bottom: 40px;
        }
        .nav-pills-custom .nav-link {
            border-radius: 50px; color: #64748b; font-weight: 700; padding: 10px 30px;
            transition: 0.3s; font-size: 0.95rem;
        }
        .nav-pills-custom .nav-link:hover { color: var(--primary-color); background: #f8fafc; }
        .nav-pills-custom .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
            color: white; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        }
        .nav-pills-custom .nav-link i { margin-right: 8px; }

                .nav-pills-custom .nav-link.active {
            background-color: #4f46e5; /* Nền Tím Đậm (Indigo) */
            color: #ffffff !important; /* Chữ TRẮNG tinh (Bắt buộc) */
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
            transform: scale(1.02); /* Phóng to nhẹ */
        }
        

        /* 2. Stat Cards (Overview Tab) */
        .stat-card-big {
            background: white; border-radius: 20px; padding: 30px; text-align: center;
            border: 1px solid #f1f5f9; box-shadow: 0 10px 20px -5px rgba(0,0,0,0.03);
            transition: 0.3s; height: 100%; position: relative; overflow: hidden;
        }
        .stat-card-big:hover { transform: translateY(-5px); border-color: var(--primary-color); box-shadow: 0 15px 30px -10px rgba(5, 150, 105, 0.1); }
        
        .stat-icon-lg {
            width: 70px; height: 70px; border-radius: 50%; margin: 0 auto 20px;
            display: flex; align-items: center; justify-content: center; font-size: 2rem;
            color: white; box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }
        /* Gradients for Stats */
        .bg-grad-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); } /* Lượt làm */
        .bg-grad-2 { background: linear-gradient(135deg, #2af598 0%, #009efd 100%); } /* Đề đã làm */
        .bg-grad-3 { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); } /* Điểm TB */
        .bg-grad-4 { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); } /* Điểm cao nhất */

        .stat-value { font-size: 2.2rem; font-weight: 800; color: #1e293b; line-height: 1; margin-bottom: 5px; }
        .stat-label { font-size: 0.9rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }

        /* 3. List Item Cards (Used for Recent & Saved) */
        .list-item-pro {
            background: white; border-radius: 16px; padding: 20px; margin-bottom: 15px;
            display: flex; align-items: center; justify-content: space-between;
            border: 1px solid #f1f5f9; transition: 0.2s; text-decoration: none; color: inherit;
        }
        .list-item-pro:hover { transform: translateX(5px); border-color: #667eea; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        
        .list-left { display: flex; align-items: center; gap: 20px; overflow: hidden; }
        .list-icon-box {
            width: 55px; height: 55px; border-radius: 15px; background: #f8fafc; color: #64748b;
            display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;
        }
        .list-item-pro:hover .list-icon-box { background: #eef2ff; color: #667eea; }
        
        .list-info h6 { font-weight: 700; font-size: 1.05rem; color: #1e293b; margin: 0 0 5px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .list-meta { font-size: 0.85rem; color: #64748b; font-weight: 500; }
        
        .score-tag {
            padding: 5px 12px; border-radius: 8px; font-weight: 800; font-size: 0.85rem;
            background: #f1f5f9; color: #64748b;
        }
        .score-high { background: #dcfce7; color: #166534; } /* Xanh lá */

        /* 4. Empty State */
        .empty-state { text-align: center; padding: 60px 0; }
        .empty-img { width: 150px; opacity: 0.6; margin-bottom: 20px; }
        .empty-text { font-weight: 700; color: #94a3b8; font-size: 1.1rem; margin-bottom: 15px; }
    </style>
@endpush

@section('content')

    <div class="library-header">
        <h2 class="library-title">Thư viện cá nhân</h2>
        <p class="library-subtitle">Theo dõi quá trình tiến bộ của bạn</p>
    </div>

    <div class="d-flex justify-content-center">
        <ul class="nav nav-pills-custom" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#overview" type="button">
                    <i class="fa-solid fa-chart-pie"></i> Tổng quan
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#recent" type="button">
                    <i class="fa-solid fa-clock-rotate-left"></i> Gần đây
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#saved" type="button">
                    <i class="fa-solid fa-bookmark"></i> Đã lưu
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content mt-4">
        
        <div class="tab-pane fade show active" id="overview">
            <div class="row g-4 mb-5">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card-big">
                        <div class="stat-icon-lg bg-grad-1"><i class="fa-solid fa-pen-to-square"></i></div>
                        <div class="stat-value">{{ $totalAttempts }}</div>
                        <div class="stat-label">Lượt làm bài</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card-big">
                        <div class="stat-icon-lg bg-grad-2"><i class="fa-solid fa-file-circle-check"></i></div>
                        <div class="stat-value">{{ $uniqueExamsCount }}</div>
                        <div class="stat-label">Đề đã làm</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card-big">
                        <div class="stat-icon-lg bg-grad-3"><i class="fa-solid fa-chart-line"></i></div>
                        <div class="stat-value">{{ number_format($avgScore, 1) }}</div>
                        <div class="stat-label">Điểm trung bình</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card-big">
                        <div class="stat-icon-lg bg-grad-4"><i class="fa-solid fa-crown"></i></div>
                        <div class="stat-value">{{ $highestScore ?? 0 }}</div>
                        <div class="stat-label">Điểm cao nhất</div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-chart-area me-2 text-primary"></i>Biểu đồ năng lực</h5>
                <div style="height: 300px;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="recent">
            @if(isset($recentAccess) && $recentAccess->count() > 0)
                <div class="d-flex flex-column">
                    @foreach($recentAccess as $result)
                    <a href="{{ route('student.exams.result', $result->id) }}" class="list-item-pro">
                        <div class="list-left">
                            <div class="list-icon-box">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            <div class="list-info">
                                <h6 title="{{ $result->exam->title ?? '' }}">{{ $result->exam->title ?? 'Đề thi đã bị xóa' }}</h6>
                                <div class="list-meta">
                                    <i class="fa-regular fa-calendar me-1"></i> {{ $result->created_at->format('d/m/Y H:i') }} 
                                    <span class="mx-2">•</span>
                                    <span class="{{ $result->score >= 8 ? 'text-success' : 'text-muted' }}">Kết quả: {{ $result->score }} điểm</span>
                                </div>
                            </div>
                        </div>
                        <div class="list-right">
                            <span class="score-tag {{ $result->score >= 8 ? 'score-high' : '' }}">
                                {{ $result->score }}/10
                            </span>
                        </div>
                    </a>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/time-management-4560773-3786191.png" class="empty-img">
                    <div class="empty-text">Chưa có hoạt động nào gần đây!</div>
                    <a href="{{ route('student.exams.explore') }}" class="btn btn-outline-primary rounded-pill px-4">Làm bài thi ngay</a>
                </div>
            @endif
        </div>

        <div class="tab-pane fade" id="saved">
            @if(isset($favorites) && $favorites->count() > 0)
                <div class="row g-3">
                    @foreach($favorites as $exam)
                    <div class="col-md-6">
                        <div class="list-item-pro p-3">
                            <div class="list-left">
                                <div class="list-icon-box" style="color: #f59e0b; background: #fffbeb;">
                                    <i class="fa-solid fa-bookmark"></i>
                                </div>
                                <div class="list-info">
                                    <h6 title="{{ $exam->title }}">{{ $exam->title }}</h6>
                                    <div class="list-meta">
                                        {{ $exam->total_questions }} câu hỏi • {{ $exam->difficulty }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="{{ route('student.exams.show', $exam->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">Làm bài</a>
                                <form action="{{ route('student.exams.bookmark', $exam->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-light text-danger rounded-circle border shadow-sm" title="Bỏ lưu">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/save-money-2661089-2227241.png" class="empty-img">
                    <div class="empty-text">Bạn chưa lưu đề thi nào!</div>
                    <a href="{{ route('student.exams.explore') }}" class="btn btn-outline-primary rounded-pill px-4">Khám phá kho đề</a>
                </div>
            @endif
        </div>

    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = @json($months ?? []);
        const data = @json($scores ?? []);

        // Vẽ biểu đồ nếu có dữ liệu
        if(document.getElementById('monthlyChart')) {
            const ctx = document.getElementById('monthlyChart').getContext('2d');
            let gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Điểm trung bình',
                        data: data,
                        borderColor: '#10b981',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, max: 10 }, x: { grid: { display: false } } }
                }
            });
        }
    </script>
@endpush