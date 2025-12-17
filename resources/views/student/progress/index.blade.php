@extends('layouts.student')

@section('title', 'Phân tích tiến độ')

@push('styles')
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; color: #334155; }

        /* Card Styles */
        .analytics-card {
            background: white; border-radius: 20px; padding: 25px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); border: 1px solid #e2e8f0;
            height: 100%; transition: 0.3s;
        }
        .analytics-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px -5px rgba(0,0,0,0.05); border-color: #cbd5e1; }
        
        .card-header-custom {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;
        }
        .card-title-custom { font-weight: 800; color: #1e293b; font-size: 1.1rem; margin: 0; display: flex; align-items: center; }
        .card-title-custom i { margin-right: 10px; color: #4f46e5; }

        /* Table Styles */
        .table-custom { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
        .table-custom th { color: #64748b; font-weight: 700; font-size: 0.75rem; padding: 15px; text-transform: uppercase; letter-spacing: 0.5px; }
        .table-row { background: white; box-shadow: 0 1px 2px rgba(0,0,0,0.02); transition: 0.2s; }
        .table-row:hover { transform: scale(1.005); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .table-row td { padding: 15px; vertical-align: middle; border: 1px solid #f1f5f9; border-left: none; border-right: none; }
        .table-row td:first-child { border-left: 1px solid #f1f5f9; border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
        .table-row td:last-child { border-right: 1px solid #f1f5f9; border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

        /* Score Badge */
        .score-box {
            width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
            font-weight: 800; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); font-size: 0.9rem;
        }
        .bg-high { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .bg-mid { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .bg-low { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

        /* Recommendation Box */
        .rec-box {
            background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 100%);
            border-radius: 16px; padding: 20px; border: 1px dashed #60a5fa;
            margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-start;
        }
        .rec-icon { font-size: 1.5rem; color: #3b82f6; background: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); flex-shrink: 0; }
    </style>
@endpush

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <div>
            <h3 class="fw-bold text-dark mb-1">Phân tích tiến độ</h3>
            <p class="text-muted small mb-0">Theo dõi sự phát triển và cải thiện kỹ năng của bạn.</p>
        </div>
        <div>
            <button class="btn btn-white border fw-bold shadow-sm rounded-pill px-4 btn-sm py-2">
                <i class="fa-solid fa-download me-2 text-secondary"></i> Xuất báo cáo
            </button>
        </div>
    </div>

    <div class="row g-4 mb-4">
        
        <div class="col-md-4">
            <div class="analytics-card">
                <div class="card-header-custom">
                    <h5 class="card-title-custom"><i class="fa-solid fa-chart-pie"></i> Mức độ hoàn thành</h5>
                </div>
                <div style="height: 220px; position: relative;">
                    <canvas id="completionChart"></canvas>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                        <h2 class="fw-bold m-0 text-dark">{{ $completedExams }}</h2>
                        <span class="small text-muted fw-bold">Đã làm</span>
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
                    <span class="badge bg-light text-secondary border">20 bài gần nhất</span>
                </div>
                <div style="height: 250px;">
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
                        <p class="small text-muted m-0 mt-1 line-height-base">
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
                                <th width="45%">Bài thi</th>
                                <th width="25%">Thời gian nộp</th>
                                <th width="15%" class="text-center">Kết quả</th>
                                <th width="15%" class="text-center">Điểm số</th>
                            </tr>
                        </thead>
<tbody>
    @forelse($historyDetails as $result)
        @php
            $score = $result->score;
            $bgClass = $score >= 8 ? 'bg-high' : ($score >= 5 ? 'bg-mid' : 'bg-low');
            
            // 1. LẤY TỔNG SỐ CÂU THỰC TẾ
            $exam = $result->exam;
            $questions = $exam ? $exam->questions : collect([]);
            $totalQ = $questions->count(); // <--- LẤY TỔNG SỐ CÂU Ở ĐÂY

            // 2. TÍNH SỐ CÂU ĐÚNG THỰC TẾ (Chấm lại để chính xác)
            $realCorrect = 0;
            
            // Lấy đáp án của học sinh
            $uAnswers = $result->student_answers; 
            // Xử lý trường hợp lưu ở cột selected_answers hoặc student_answers
            if (empty($uAnswers)) $uAnswers = $result->selected_answers;
            if (is_string($uAnswers)) $uAnswers = json_decode($uAnswers, true);
            if (!is_array($uAnswers)) $uAnswers = [];

            if ($totalQ > 0) {
                foreach ($questions as $q) {
                    $uAns = $uAnswers[$q->id] ?? null;

                    // Logic chấm trắc nghiệm
                    if ($q->type == 'one_choice') {
                        if (strtoupper($uAns ?? '') === strtoupper($q->correct_answer)) {
                            $realCorrect++;
                        }
                    } 
                    // Logic chấm đúng sai (Phải đúng cả 4 ý mới tính là 1 câu hoàn chỉnh)
                    elseif ($q->type == 'true_false') {
                        $correctArr = explode(',', $q->correct_answer);
                        $keys = ['a', 'b', 'c', 'd'];
                        $isFullCorrect = true;
                        foreach($keys as $idx => $key) {
                            $subU = $uAns[$key] ?? 'F';
                            $subC = $correctArr[$idx] ?? 'F';
                            if ($subU != $subC) { $isFullCorrect = false; break; }
                        }
                        if ($isFullCorrect) $realCorrect++;
                    }
                }
            }

            $realWrong = $totalQ - $realCorrect;

            // 3. XỬ LÝ THỜI GIAN
            $timeString = "--:--";
            if ($result->completion_time > 0) {
                $timeString = gmdate("i:s", $result->completion_time);
            } elseif ($result->submitted_at && $result->created_at) {
                try {
                    $diff = $result->submitted_at->diffInSeconds($result->created_at);
                    $timeString = gmdate("i:s", $diff);
                } catch(\Exception $e) {}
            }
        @endphp

        <tr class="table-row">
            <td>
                <div class="fw-bold text-dark text-truncate" style="max-width: 200px;" title="{{ $exam->title ?? 'Đề đã xóa' }}">
                    {{ $exam->title ?? 'Đề đã xóa' }}
                </div>
                <div class="small text-muted mt-1">
                    <i class="fa-regular fa-clock me-1"></i> 
                    Làm trong: {{ $timeString }} phút
                </div>
            </td>
            <td class="text-muted small fw-bold">
                {{ $result->created_at->format('d/m/Y') }}<br>
                <span class="fw-normal text-secondary">{{ $result->created_at->format('H:i') }}</span>
            </td>
            <td class="text-center">
                @if($totalQ > 0)
                    <div class="d-flex flex-column align-items-center gap-1">
                        {{-- HIỂN THỊ SỐ CÂU ĐÚNG / TỔNG SỐ CÂU --}}
                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1 w-100" title="Số câu đúng">
                            <i class="fa-solid fa-check me-1"></i> {{ $realCorrect }}/{{ $totalQ }}
                        </span>
                        
                        {{-- Hiển thị số câu sai --}}
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1 w-100" title="Số câu sai">
                            <i class="fa-solid fa-xmark me-1"></i> {{ $realWrong }}
                        </span>
                    </div>
                @else
                    <span class="badge bg-light text-muted border">Không có dữ liệu câu hỏi</span>
                @endif
            </td>
            <td align="center">
                <div class="score-box {{ $bgClass }}">{{ $score }}</div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="text-center py-5 text-muted">
                <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-folder-4048243-3353215.png" width="80" style="opacity: 0.5" class="mb-3">
                <p class="m-0">Chưa có dữ liệu bài thi nào.</p>
            </td>
        </tr>
    @endforelse
</tbody>
                    </table>
                </div>
                
                @if($historyDetails->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $historyDetails->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // --- 0. CHUẨN BỊ DỮ LIỆU PHP (Tránh lỗi cú pháp Blade) ---
        @php
            // Xử lý dữ liệu Radar Chart (Kỹ năng)
            // Tách Key và Value ra mảng riêng trước khi đưa vào @json
            $skillKeys = array_keys($skillStats ?? []);
            $skillVals = array_values($skillStats ?? []);

            // Xử lý dữ liệu Line Chart (Biến động điểm)
            // Đảm bảo dữ liệu là mảng hoặc collection hợp lệ
            $safeDates = $chartDates ?? [];
            $safeScores = $chartScores ?? [];
        @endphp

        // --- 1. DOUGHNUT CHART (Mức độ hoàn thành) ---
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
                responsive: true, maintainAspectRatio: false,
                cutout: '75%',
                plugins: { legend: { display: false } }
            }
        });

        // --- 2. AREA CHART (Biến động điểm số) ---
        const ctxArea = document.getElementById('scoreHistoryChart').getContext('2d');
        let gradientArea = ctxArea.createLinearGradient(0, 0, 0, 300);
        gradientArea.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
        gradientArea.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

        // Sử dụng biến đã xử lý ở bước 0
        const chartDates = @json($safeDates);
        const chartScores = @json($safeScores);

        new Chart(ctxArea, {
            type: 'line',
            data: {
                labels: chartDates,
                datasets: [{
                    label: 'Điểm số',
                    data: chartScores,
                    borderColor: '#3b82f6',
                    backgroundColor: gradientArea,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3b82f6'
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, max: 10, grid: { borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // --- 3. RADAR CHART (Phân tích kỹ năng) ---
        const ctxRadar = document.getElementById('skillRadarChart').getContext('2d');
        
        // Sử dụng biến đã xử lý ở bước 0
        const skillLabels = @json($skillKeys);
        const skillData = @json($skillVals);

        new Chart(ctxRadar, {
            type: 'radar',
            data: {
                labels: skillLabels,
                datasets: [{
                    label: 'Năng lực',
                    data: skillData,
                    fill: true,
                    backgroundColor: 'rgba(245, 158, 11, 0.2)',
                    borderColor: '#f59e0b',
                    pointBackgroundColor: '#f59e0b',
                    pointBorderColor: '#fff'
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: { color: '#e5e7eb' },
                        grid: { color: '#e5e7eb' },
                        suggestedMin: 0, suggestedMax: 100,
                        ticks: { display: false, stepSize: 20 },
                        pointLabels: { font: { size: 11, weight: 'bold' }, color: '#64748b' }
                    }
                },
                plugins: { legend: { display: false } }
            }
        });
    </script>
@endpush