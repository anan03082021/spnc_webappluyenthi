@extends('layouts.student')

@section('title', 'Trung tâm điều khiển')

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        /* --- DASHBOARD PROFESSIONAL STYLES --- */
        body { background-color: #f8fafc; }

        /* 1. WELCOME BANNER */
        .welcome-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
            border-radius: 24px; padding: 40px; color: white; margin-bottom: 40px;
            position: relative; overflow: hidden;
            box-shadow: 0 10px 30px -10px rgba(5, 150, 105, 0.4);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .welcome-card::before, .welcome-card::after {
            content: ''; position: absolute; border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
        }
        .welcome-card::before { width: 300px; height: 300px; top: -100px; right: -50px; }
        .welcome-card::after { width: 200px; height: 200px; bottom: -50px; left: 50px; opacity: 0.5; }

        .btn-banner {
            background: rgba(255,255,255,0.9); color: var(--primary-color); font-weight: 800;
            border: none; padding: 12px 25px; border-radius: 50px; transition: 0.3s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .btn-banner:hover { transform: translateY(-3px); background: white; box-shadow: 0 8px 20px rgba(0,0,0,0.15); }

        /* 2. SECTION HEADERS */
        .section-header {
            display: flex; justify-content: space-between; align-items: flex-end;
            margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;
        }
        .section-title {
            font-size: 1.25rem; font-weight: 800; color: #1e293b; margin: 0;
            display: flex; align-items: center; gap: 10px;
        }
        .section-title i { color: var(--primary-color); }
        .link-more {
            font-size: 0.9rem; font-weight: 700; color: #64748b; text-decoration: none; transition: 0.2s;
        }
        .link-more:hover { color: var(--secondary-color); }

        /* 3. EXAM CARD PRO */
        .exam-card-wrapper { position: relative; height: 100%; transition: 0.3s; }
        .exam-card-wrapper:hover { transform: translateY(-5px); }

        .exam-card-pro {
            background: white; border-radius: 20px; overflow: hidden; border: 1px solid #f1f5f9;
            box-shadow: 0 4px 10px -2px rgba(0,0,0,0.03); height: 100%; display: block; text-decoration: none; color: inherit;
        }
        .card-cover {
            height: 120px; position: relative; display: flex; align-items: center; justify-content: center;
        }
        .grad-1 { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); }
        .grad-2 { background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); }
        .grad-3 { background: linear-gradient(135deg, #ffedd5 0%, #fed7aa 100%); }
        .grad-4 { background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 100%); }

        .card-icon { font-size: 3rem; color: white; opacity: 0.6; mix-blend-mode: overlay; transition: 0.5s; }
        .exam-card-wrapper:hover .card-icon { transform: scale(1.1) rotate(-10deg); opacity: 0.9; }

        .badge-diff {
            position: absolute; top: 10px; right: 10px; background: rgba(255,255,255,0.9);
            padding: 4px 10px; border-radius: 10px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05); color: #334155;
        }
        
        /* Badge Đã làm */
        .badge-done {
            position: absolute; top: 10px; left: 50px; /* Cách nút bookmark */
            background: #10b981; color: white;
            padding: 4px 10px; border-radius: 10px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .card-body { padding: 20px; }
        .exam-title { font-weight: 800; font-size: 1rem; color: #1e293b; margin-bottom: 10px; line-height: 1.4; height: 2.8em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
        .exam-meta { font-size: 0.8rem; color: #64748b; font-weight: 600; display: flex; gap: 15px; margin-bottom: 15px; }
        
        .btn-action {
            width: 100%; padding: 10px; border-radius: 12px; font-weight: 700; border: none; font-size: 0.9rem;
            background: #f8fafc; color: #64748b; transition: 0.3s;
        }
        .exam-card-wrapper:hover .btn-action { background: var(--primary-color); color: white; box-shadow: 0 4px 10px rgba(5, 150, 105, 0.3); }

        /* Style riêng cho nút Làm lại */
        .btn-redo {
            background: #d1fae5 !important; color: #059669 !important;
        }
        .exam-card-wrapper:hover .btn-redo {
            background: #059669 !important; color: white !important;
        }

        /* Bookmark Button */
        .btn-bookmark {
            position: absolute; top: 10px; left: 10px; z-index: 10;
            width: 32px; height: 32px; border-radius: 50%; border: none;
            background: rgba(255,255,255,0.9); box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex; align-items: center; justify-content: center; transition: 0.2s; cursor: pointer;
        }
        .btn-bookmark:hover { transform: scale(1.1); background: white; }
        .bm-active { color: #f59e0b; } .bm-inactive { color: #cbd5e0; }

        /* 4. SIDEBAR WIDGETS */
        .widget-box {
            background: white; border-radius: 20px; padding: 20px; margin-bottom: 30px;
            box-shadow: 0 4px 10px -2px rgba(0,0,0,0.03); border: 1px solid #f1f5f9;
        }
        .widget-title { font-weight: 800; font-size: 1rem; color: #334155; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; }
        
        .list-item {
            display: flex; align-items: center; padding: 12px; border-radius: 12px;
            transition: 0.2s; text-decoration: none; color: inherit; border: 1px solid transparent; margin-bottom: 8px;
        }
        .list-item:hover { background: #f0fdf4; border-color: var(--primary-color); }
        .list-icon {
            width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; margin-right: 15px; flex-shrink: 0;
        }
        .icon-forum { background: #f3e8ff; color: #9333ea; }
        .icon-doc { background: #e0f2fe; color: #0284c7; }
    </style>
@endpush

@section('content')
    
    <div class="welcome-card" data-aos="fade-down" data-aos-duration="800">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-md-8">
                <h2 class="fw-bold mb-2">Xin chào, {{ Auth::user()->name }}! 👋</h2>
                <p class="mb-4 opacity-90">Hôm nay là một ngày tuyệt vời để bứt phá giới hạn bản thân.</p>
                <div class="d-flex gap-3">
                    <div class="badge bg-white bg-opacity-25 px-3 py-2 rounded-pill fw-normal">
                        <i class="fa-solid fa-check-circle me-1"></i> Đã làm: <strong>{{ $attemptedExamsCount }}</strong>
                    </div>
                    <div class="badge bg-white bg-opacity-25 px-3 py-2 rounded-pill fw-normal">
                        <i class="fa-solid fa-clock me-1"></i> Chờ làm: <strong>{{ $notAttemptedCount }}</strong>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-4 mt-md-0">
                <button class="btn-banner" data-bs-toggle="modal" data-bs-target="#progressModal">
                    <i class="fa-solid fa-chart-pie me-2"></i> Xem tiến độ
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            
            <div class="section-header" data-aos="fade-right">
                <h3 class="section-title"><i class="fa-solid fa-graduation-cap"></i> Đề thi đề xuất</h3>
                <a href="{{ route('student.exams.explore') }}" class="link-more">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
            </div>

            <div class="row g-4 mb-5">
                @forelse($exams as $index => $exam)
                    @php
                        $gradClass = 'grad-' . (($index % 4) + 1);
                        $iconClass = match($index % 4) { 0 => 'fa-code', 1 => 'fa-database', 2 => 'fa-network-wired', default => 'fa-file-signature' };
                        
                        // Check bookmarks
                        $isSaved = Auth::user()->bookmarks ? Auth::user()->bookmarks->contains($exam->id) : false;

                        // Check đã làm hay chưa (Sử dụng mảng ID từ Controller)
                        $isAttempted = in_array($exam->id, $attemptedExamIds ?? []);
                    @endphp

                    <div class="col-md-6 col-xl-4" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        <div class="exam-card-wrapper">
                            <form action="{{ route('student.exams.bookmark', $exam->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-bookmark {{ $isSaved ? 'bm-active' : 'bm-inactive' }}" title="{{ $isSaved ? 'Bỏ lưu' : 'Lưu đề này' }}">
                                    <i class="fa-solid fa-bookmark"></i>
                                </button>
                            </form>

                            <a href="{{ route('student.exams.show', $exam->id) }}" class="exam-card-pro">
                                <div class="card-cover {{ $gradClass }}">
                                    <i class="fa-solid {{ $iconClass }} card-icon"></i>
                                    <span class="badge-diff">{{ $exam->difficulty }}</span>
                                    
                                    {{-- HIỂN THỊ BADGE ĐÃ LÀM --}}
                                    @if($isAttempted)
                                        <span class="badge-done"><i class="fa-solid fa-check"></i> Đã làm</span>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <h6 class="exam-title" title="{{ $exam->title }}">{{ $exam->title }}</h6>
                                    <div class="exam-meta">
                                        <span><i class="fa-regular fa-clock me-1"></i> {{ $exam->duration }}'</span>
                                        <span><i class="fa-solid fa-list-ol me-1"></i> {{ $exam->total_questions }} câu</span>
                                    </div>
                                    
                                    {{-- ĐỔI NÚT DỰA TRÊN TRẠNG THÁI --}}
                                    @if($isAttempted)
                                        <button class="btn-action btn-redo">
                                            <i class="fa-solid fa-rotate-right me-1"></i> Làm lại
                                        </button>
                                    @else
                                        <button class="btn-action">Làm bài ngay</button>
                                    @endif
                                </div>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-5 border rounded-4 border-dashed">
                        <i class="fa-regular fa-folder-open fs-1 mb-3 opacity-25"></i>
                        <p>Chưa có đề thi nào khả dụng.</p>
                    </div>
                @endforelse
            </div>

        </div>

        <div class="col-lg-4">
            
            <div class="widget-box" data-aos="fade-left" data-aos-delay="200">
                <div class="widget-title">
                    <span><i class="fa-solid fa-fire text-danger me-2"></i> Thảo luận HOT</span>
                    <a href="{{ route('forum.index') }}" class="small text-muted text-decoration-none">Xem thêm</a>
                </div>
                
                @forelse($trendingPosts as $post)
                <a href="{{ route('forum.show', $post->id) }}" class="list-item">
                    <div class="list-icon icon-forum">
                        <i class="fa-regular fa-comments"></i>
                    </div>
                    <div style="overflow: hidden;">
                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.95rem;">{{ $post->title }}</div>
                        <div class="small text-muted">
                            <span class="fw-bold text-primary">{{ $post->replies_count }}</span> trả lời • {{ $post->created_at->diffForHumans(null, true) }}
                        </div>
                    </div>
                </a>
                @empty
                <div class="text-center small text-muted py-3">Chưa có thảo luận nổi bật.</div>
                @endforelse
            </div>

            <div class="widget-box" data-aos="fade-left" data-aos-delay="400">
                <div class="widget-title">
                    <span><i class="fa-solid fa-book-open text-primary me-2"></i> Tài liệu mới</span>
                    <a href="{{ route('student.documents.index') }}" class="small text-muted text-decoration-none">Thư viện</a>
                </div>

                @forelse($latestDocuments as $doc)
                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="list-item">
                    <div class="list-icon icon-doc">
                        @if(str_contains($doc->file_path, '.pdf')) 
                            <i class="fa-regular fa-file-pdf text-danger"></i> 
                        @else 
                            <i class="fa-regular fa-file-word text-primary"></i> 
                        @endif
                    </div>
                    <div style="overflow: hidden;">
                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.95rem;">{{ $doc->title }}</div>
                        <div class="small text-muted">{{ $doc->category->name ?? 'Tài liệu chung' }}</div>
                    </div>
                </a>
                @empty
                <div class="text-center small text-muted py-3">Chưa có tài liệu mới.</div>
                @endforelse
            </div>

        </div>
    </div>

    {{-- MODAL TIẾN ĐỘ --}}
    <div class="modal fade" id="progressModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-chart-pie me-2 text-primary"></i>Tổng quan tiến độ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-5 text-center border-end">
                            <h6 class="text-muted fw-bold mb-3 small text-uppercase">Tỷ lệ hoàn thành</h6>
                            <div style="height: 200px; display: flex; justify-content: center;">
                                <canvas id="progressChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-7 ps-md-4">
                            <h6 class="text-muted fw-bold mb-3 small text-uppercase">Biểu đồ điểm số</h6>
                            <div style="height: 200px;">
                                <canvas id="scoreChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <a href="{{ route('student.exams.history') }}" class="btn btn-success w-100 rounded-pill fw-bold" style="background: var(--primary-color); border:none;">Vào trang thành tựu</a>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Init Animation
        AOS.init({ duration: 800, once: true });

        // Safe Data Injection
        const doneCount = {{ $attemptedExamsCount ?? 0 }};
        const pendingCount = {{ $notAttemptedCount ?? 0 }};
        const chartLabels = {!! json_encode($chartLabels ?? []) !!};
        const chartScores = {!! json_encode($chartScores ?? []) !!};

        // 1. Doughnut Chart
        new Chart(document.getElementById('progressChart').getContext('2d'), {
            type: 'doughnut',
            data: { 
                labels: ['Đã làm', 'Chưa làm'], 
                datasets: [{ 
                    data: [doneCount, pendingCount], 
                    backgroundColor: ['#10b981', '#f1f5f9'], 
                    borderWidth: 0 
                }] 
            },
            options: { responsive: true, cutout: '75%', plugins: { legend: { display: false } } }
        });

        // 2. Line Chart
        const ctxScore = document.getElementById('scoreChart').getContext('2d');
        let gradient = ctxScore.createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        new Chart(ctxScore, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Điểm', data: chartScores, 
                    borderColor: '#10b981', 
                    backgroundColor: gradient, 
                    fill: true, tension: 0.4, pointRadius: 5,
                    pointBackgroundColor: '#fff', pointBorderColor: '#10b981'
                }]
            },
            options: { 
                responsive: true, maintainAspectRatio: false, 
                plugins: { legend: { display: false } }, 
                scales: { 
                    y: { beginAtZero: true, max: 10, grid: { borderDash: [5, 5] } }, 
                    x: { display: false } 
                } 
            } 
        });
    </script>
@endpush