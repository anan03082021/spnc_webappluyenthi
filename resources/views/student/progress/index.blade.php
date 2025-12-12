@extends('layouts.student')

@section('title', 'Phân tích tiến độ')

@push('styles')
    <style>
        body { background-color: #f8fafc; }

        /* Card Styles */
        .analytics-card {
            background: white; border-radius: 20px; padding: 25px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); border: 1px solid #f1f5f9;
            height: 100%; transition: 0.3s;
        }
        .analytics-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px -10px rgba(0,0,0,0.05); }
        
        .card-header-custom {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;
        }
        .card-title-custom { font-weight: 800; color: #1e293b; font-size: 1.1rem; margin: 0; display: flex; align-items: center; }
        .card-title-custom i { margin-right: 10px; color: var(--primary-color); }

        /* Table Styles */
        .table-custom { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
        .table-custom th { color: #64748b; font-weight: 700; font-size: 0.85rem; padding: 15px; text-transform: uppercase; }
        .table-row { background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: 0.2s; }
        .table-row:hover { transform: scale(1.01); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .table-row td { padding: 15px; vertical-align: middle; border: 1px solid #f1f5f9; border-left: none; border-right: none; }
        .table-row td:first-child { border-left: 1px solid #f1f5f9; border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
        .table-row td:last-child { border-right: 1px solid #f1f5f9; border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

        /* Score Badge */
        .score-box {
            width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
            font-weight: 800; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .bg-high { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .bg-mid { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .bg-low { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

        /* Recommendation Box */
        .rec-box {
            background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%);
            border-radius: 16px; padding: 20px; border: 1px dashed #38bdf8;
            margin-bottom: 20px; display: flex; gap: 15px;
        }
        .rec-icon { font-size: 2rem; color: #0284c7; }
    </style>
@endpush

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <div>
            <h3 class="fw-bold text-dark mb-1">Phân tích tiến độ</h3>
            <p class="text-muted small mb-0">Theo dõi sự phát triển và cải thiện kỹ năng của bạn.</p>
        </div>
        <div>
            <button class="btn btn-white border fw-bold shadow-sm rounded-pill px-4">
                <i class="fa-solid fa-download me-2"></i> Xuất báo cáo
            </button>
        </div>
    </div>

    <div class="row g-4 mb-5">
        
        <div class="col-md-4">
            <div class="analytics-card">
                <div class="card-header-custom">
                    <h5 class="card-title-custom"><i class="fa-solid fa-chart-pie"></i> Mức độ hoàn thành</h5>
                </div>
                <div style="height: 250px; position: relative;">
                    <canvas id="completionChart"></canvas>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                        <h2 class="fw-bold m-0 text-dark">{{ $completedExams }}</h2>
                        <span class="small text-muted">Đề đã làm</span>
                    </div>
                </div>
                <div class="mt-3 text-center text-muted small">
                    Còn <strong>{{ $remainingExams }}</strong> đề thi đang chờ bạn chinh phục!
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="analytics-card">
                <div class="card-header-custom">
                    <h5 class="card-title-custom"><i class="fa-solid fa-chart-area"></i> Biến động điểm số</h5>
                    <select class="form-select form-select-sm w-auto border-0 bg-light fw-bold">
                        <option>20 bài gần nhất</option>
                    </select>
                </div>
                <div style="height: 300px;">
                    <canvas id="scoreHistoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        
        <div class="col-md-5">
            <div class="analytics-card">
                <div class="card-header-custom">
                    <h5 class="card-title-custom"><i class="fa-solid fa-crosshairs"></i> Phân tích kỹ năng</h5>
                </div>
                
                <div class="rec-box">
                    <div class="rec-icon"><i class="fa-solid fa-lightbulb"></i></div>
                    <div>
                        <h6 class="fw-bold text-dark m-0">Góc tư vấn AI</h6>
                        <p class="small text-muted m-0 mt-1">
                            Kỹ năng <strong>Lý thuyết</strong> của bạn khá tốt. Tuy nhiên cần cải thiện <strong>Tốc độ</strong> làm bài để đạt điểm tối đa.
                        </p>
                    </div>
                </div>

                <div style="height: 250px;">
                    <canvas id="skillRadarChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="analytics-card">
                <div class="card-header-custom">
                    <h5 class="card-title-custom"><i class="fa-solid fa-list-check"></i> Chi tiết bài thi</h5>
                </div>
                
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Bài thi</th>
                                <th>Thời gian nộp</th>
                                <th class="text-center">Kết quả</th>
                                <th class="text-center">Điểm số</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($historyDetails as $result)
                                @php
                                    $score = $result->score;
                                    $bgClass = $score >= 8 ? 'bg-high' : ($score >= 5 ? 'bg-mid' : 'bg-low');
                                    
                                    // Giả lập số câu đúng/sai (vì trong DB Result hiện tại chưa lưu chi tiết count này, bạn có thể tính toán trong Controller nếu muốn chính xác)
                                    $totalQ = $result->exam->total_questions ?? 40;
                                    $correct = round(($score / 10) * $totalQ);
                                    $wrong = $totalQ - $correct;
                                @endphp
                                <tr class="table-row">
                                    <td>
                                        <div class="fw-bold text-dark">{{ $result->exam->title ?? 'Đề đã xóa' }}</div>
                                        <div class="small text-muted">
                                            <i class="fa-regular fa-clock me-1"></i> Làm trong: {{ gmdate("i:s", $result->completion_time ?? 0) }} phút
                                        </div>
                                    </td>
                                    <td class="text-muted small fw-bold">
                                        {{ $result->created_at->format('d/m/Y') }}<br>
                                        {{ $result->created_at->format('H:i') }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-2">
                                            <i class="fa-solid fa-check me-1"></i> {{ $correct }}
                                        </span>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 ms-1">
                                            <i class="fa-solid fa-xmark me-1"></i> {{ $wrong }}
                                        </span>
                                    </td>
                                    <td align="center">
                                        <div class="score-box {{ $bgClass }}">{{ $score }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Chưa có dữ liệu bài thi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    {{ $historyDetails->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // --- 1. DOUGHNUT CHART (Tiến độ) ---
        const ctxPie = document.getElementById('completionChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: ['Hoàn thành', 'Chưa làm'],
                datasets: [{
                    data: [{{ $completedExams }}, {{ $remainingExams }}],
                    backgroundColor: ['#10b981', '#f1f5f9'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%', // Tạo lỗ tròn to ở giữa
                plugins: { legend: { display: false } }
            }
        });

        // --- 2. AREA CHART (Biến động điểm) ---
        const ctxArea = document.getElementById('scoreHistoryChart').getContext('2d');
        let gradientArea = ctxArea.createLinearGradient(0, 0, 0, 300);
        gradientArea.addColorStop(0, 'rgba(59, 130, 246, 0.4)'); // Blue fade
        gradientArea.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

        new Chart(ctxArea, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartDates) !!},
                datasets: [{
                    label: 'Điểm số',
                    data: {!! json_encode($chartScores) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: gradientArea,
                    fill: true, // Tạo hiệu ứng miền (Area)
                    tension: 0.4, // Đường cong mềm mại
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3b82f6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, max: 10, grid: { borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // --- 3. RADAR CHART (Kỹ năng - Gợi ý) ---
        const ctxRadar = document.getElementById('skillRadarChart').getContext('2d');
        new Chart(ctxRadar, {
            type: 'radar',
            data: {
                labels: {!! json_encode(array_keys($skillStats)) !!},
                datasets: [{
                    label: 'Chỉ số năng lực',
                    data: {!! json_encode(array_values($skillStats)) !!},
                    fill: true,
                    backgroundColor: 'rgba(245, 158, 11, 0.2)', // Cam nhạt
                    borderColor: '#f59e0b',
                    pointBackgroundColor: '#f59e0b',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#f59e0b'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: { color: '#e5e7eb' },
                        grid: { color: '#e5e7eb' },
                        suggestedMin: 0,
                        suggestedMax: 100,
                        ticks: { display: false } // Ẩn số trên trục radar cho gọn
                    }
                },
                plugins: { legend: { display: false } }
            }
        });
    </script>
@endpush