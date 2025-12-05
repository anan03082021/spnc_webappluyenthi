<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kết quả thi - SPNC Edutech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f3f4f6; color: #4a5568; }
        
        /* Navbar Minimal */
        .navbar { background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

        /* Score Card (Phần trên cùng) */
        .score-card {
            background: white; border-radius: 20px; border: none;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); overflow: hidden;
            position: relative;
        }
        .score-circle {
            width: 150px; height: 150px; border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 800; font-size: 3rem;
            box-shadow: 0 10px 20px rgba(118, 75, 162, 0.3);
            margin: 0 auto;
        }
        .score-label { font-size: 1rem; opacity: 0.8; font-weight: 600; display: block; margin-top: -10px; }

        /* Question Review Cards */
        .review-card {
            background: white; border: none; border-radius: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02); margin-bottom: 20px;
            border-left: 5px solid transparent; /* Để tô màu trạng thái */
        }
        .review-card.correct { border-left-color: #48bb78; } /* Xanh lá */
        .review-card.wrong { border-left-color: #f56565; }   /* Đỏ */

        /* Styles cho các đáp án */
        .option-review {
            padding: 10px 15px; border-radius: 10px; border: 1px solid #e2e8f0;
            margin-bottom: 8px; position: relative;
        }
        
        /* Đáp án ĐÚNG (Luôn hiện màu xanh viền) */
        .option-review.is-key {
            border-color: #48bb78; background-color: #f0fff4; color: #22543d; font-weight: 700;
        }
        /* Icon tick xanh cho đáp án đúng */
        .option-review.is-key::after {
            content: "\f00c"; font-family: "Font Awesome 6 Free"; font-weight: 900;
            position: absolute; right: 15px; top: 12px; color: #48bb78;
        }

        /* Đáp án SAI do HS chọn (Màu đỏ nền) */
        .option-review.user-wrong {
            border-color: #f56565; background-color: #fff5f5; color: #742a2a;
        }
        /* Icon X đỏ cho đáp án sai */
        .option-review.user-wrong::after {
            content: "\f00d"; font-family: "Font Awesome 6 Free"; font-weight: 900;
            position: absolute; right: 15px; top: 12px; color: #f56565;
        }

        /* Hộp giải thích */
        .explanation-box {
            background-color: #ebf8ff; border-radius: 10px; padding: 15px;
            margin-top: 15px; border-left: 4px solid #4299e1; font-size: 0.95rem;
        }
    </style>
</head>
<body class="py-4">

    <div class="container mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold text-dark m-0">Kết quả bài thi</h4>
            <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary rounded-pill fw-bold">
                <i class="fa-solid fa-house me-1"></i> Về Dashboard
            </a>
        </div>
    </div>

    <div class="container">
        <div class="score-card p-4 mb-5">
            <div class="row align-items-center">
                <div class="col-md-4 text-center mb-4 mb-md-0">
                    <div class="score-circle">
                        <div>
                            {{ $result->score }}
                            <span class="score-label">điểm</span>
                        </div>
                    </div>
                    
                    <h4 class="mt-3 fw-bold text-dark">
                        @if($result->score >= 9) <span class="text-success">Xuất sắc! 🎉</span>
                        @elseif($result->score >= 7) <span class="text-primary">Làm tốt lắm! 💪</span>
                        @elseif($result->score >= 5) <span class="text-warning">Đạt yêu cầu 👍</span>
                        @else <span class="text-danger">Cần cố gắng hơn fighting! 📚</span>
                        @endif
                    </h4>
                    <p class="text-muted small">Hoàn thành lúc: {{ $result->created_at->format('H:i d/m/Y') }}</p>
                </div>

                <div class="col-md-4 mb-4 mb-md-0 border-start border-end">
                    <div class="px-4">
                        <h6 class="text-uppercase text-muted fw-bold small mb-3">Thống kê bài làm</h6>
                        
                        @php
                            $totalQ = $result->exam->questions->count();
                            $correctQ = 0;
                            foreach($result->exam->questions as $q) {
                                if(($result->selected_answers[$q->id] ?? '') === $q->correct_answer) {
                                    $correctQ++;
                                }
                            }
                            $wrongQ = $totalQ - $correctQ;
                            $percent = $totalQ > 0 ? round(($correctQ / $totalQ) * 100) : 0;
                        @endphp

                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fa-solid fa-circle-check text-success me-2"></i>Số câu đúng</span>
                            <span class="fw-bold">{{ $correctQ }} / {{ $totalQ }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="fa-solid fa-circle-xmark text-danger me-2"></i>Số câu sai</span>
                            <span class="fw-bold">{{ $wrongQ }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span><i class="fa-solid fa-bullseye text-primary me-2"></i>Tỷ lệ chính xác</span>
                            <span class="fw-bold">{{ $percent }}%</span>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('student.exams.show', $result->exam_id) }}" class="btn btn-primary w-100 rounded-pill fw-bold">
                                <i class="fa-solid fa-rotate-right me-1"></i> Làm lại đề này
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div style="height: 180px; width: 100%; display: flex; justify-content: center;">
                        <canvas id="resultChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        @if($recommendedDocuments->isNotEmpty())
    <div class="card border-0 shadow-sm rounded-4 mb-5" style="background: #f0f9ff; border-left: 5px solid #0ea5e9 !important;">
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-3">
                <i class="fa-solid fa-book-open-reader text-primary me-2"></i>
                Góc ôn tập dành cho bạn
            </h5>
            <p class="text-muted small">Dựa trên những câu bạn làm sai, hệ thống đề xuất các tài liệu sau để củng cố kiến thức:</p>
            
            <div class="row g-3">
                @foreach($recommendedDocuments as $doc)
                <div class="col-md-6">
                    <div class="bg-white p-3 rounded-3 shadow-sm d-flex align-items-center">
                        <div class="me-3 fs-3 text-danger"><i class="fa-regular fa-file-pdf"></i></div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark text-truncate">{{ $doc->title }}</div>
                            <small class="text-muted">Chương: {{ $doc->category->name }}</small>
                        </div>
                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                            Xem ngay
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

        <h5 class="fw-bold mb-3"><i class="fa-solid fa-list-check me-2"></i>Chi tiết lời giải</h5>
        
        @foreach($result->exam->questions as $index => $q)
            @php
                $userAns = $result->selected_answers[$q->id] ?? null;
                $isCorrect = $userAns === $q->correct_answer;
                $cardClass = $isCorrect ? 'correct' : 'wrong';
            @endphp

            <div class="card review-card {{ $cardClass }} p-4">
                <div class="d-flex justify-content-between">
                    <span class="badge {{ $isCorrect ? 'bg-success' : 'bg-danger' }} mb-2">
                        Câu {{ $index + 1 }}
                    </span>
                    @if($isCorrect)
                        <span class="text-success fw-bold small"><i class="fa-solid fa-check"></i> Chính xác</span>
                    @else
                        <span class="text-danger fw-bold small"><i class="fa-solid fa-xmark"></i> Sai rồi</span>
                    @endif
                </div>

                <h6 class="fw-bold mb-3">{{ $q->content }}</h6>

                <div class="row g-2">
                    @foreach(['A', 'B', 'C', 'D'] as $opt)
                        @php
                            // Logic xác định class CSS cho từng ô đáp án
                            $optKey = 'option_' . strtolower($opt); // option_a, option_b...
                            $optContent = $q->$optKey;
                            
                            $cssClass = '';
                            
                            // Nếu đây là đáp án ĐÚNG của đề -> Luôn tô xanh viền
                            if ($opt === $q->correct_answer) {
                                $cssClass .= ' is-key';
                            }
                            
                            // Nếu đây là đáp án SAI mà User CHỌN -> Tô đỏ nền
                            if ($opt === $userAns && !$isCorrect) {
                                $cssClass .= ' user-wrong';
                            }

                            // Làm mờ các đáp án không liên quan để nổi bật đáp án đúng/sai
                            if ($opt !== $q->correct_answer && $opt !== $userAns) {
                                $cssClass .= ' opacity-50';
                            }
                        @endphp

                        <div class="col-md-6">
                            <div class="option-review {{ $cssClass }}">
                                <strong class="me-2">{{ $opt }}.</strong> {{ $optContent }}
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(!$isCorrect && $q->explanation)
                    <div class="explanation-box">
                        <strong class="text-primary"><i class="fa-solid fa-lightbulb me-1"></i> Giải thích chi tiết:</strong><br>
                        {{ $q->explanation }}
                    </div>
                @endif
                
                @if($isCorrect && $q->explanation)
                     <div class="mt-2 text-end">
                        <button class="btn btn-sm btn-link text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#explain{{$q->id}}">
                            Xem giải thích
                        </button>
                        <div class="collapse text-start" id="explain{{$q->id}}">
                             <div class="explanation-box mt-2">
                                {{ $q->explanation }}
                            </div>
                        </div>
                     </div>
                @endif
            </div>
        @endforeach

        <div class="text-center mt-5 mb-5">
            <a href="{{ route('student.dashboard') }}" class="btn btn-secondary px-4 rounded-pill">Về màn hình chính</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('resultChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Đúng', 'Sai'],
                datasets: [{
                    data: [{{ $correctQ }}, {{ $wrongQ }}],
                    backgroundColor: ['#48bb78', '#f56565'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>
</body>
</html>