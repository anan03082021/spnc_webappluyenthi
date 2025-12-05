<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Làm bài thi: {{ $exam->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f0f2f5; }
        
        /* Sidebar Sticky (Đồng hồ & Palette) */
        .sidebar-sticky { position: sticky; top: 20px; z-index: 100; }
        .timer-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; }
        .palette-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; }
        .palette-item {
            width: 100%; aspect-ratio: 1; border: 1px solid #cbd5e0; border-radius: 8px;
            display: flex; align-items: center; justify-content: center; font-weight: bold;
            cursor: pointer; background: white; color: #4a5568; transition: 0.2s; text-decoration: none;
        }
        .palette-item:hover { background-color: #edf2f7; }
        .palette-item.answered { background-color: #48bb78; color: white; border-color: #48bb78; }

        /* Question Card */
        .question-card { background: white; border-radius: 15px; border: none; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .question-number { background-color: #ebf8ff; color: #3182ce; padding: 5px 15px; border-radius: 20px; font-weight: 800; font-size: 0.9rem; display: inline-block; margin-bottom: 15px; }
        
        /* Custom Radio Option */
        .option-label {
            display: block; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 10px;
            cursor: pointer; transition: all 0.2s; margin-bottom: 10px; position: relative;
        }
        .option-label:hover { background-color: #f7fafc; border-color: #cbd5e0; }
        
        /* Khi radio được chọn */
        .form-check-input:checked + .option-label {
            background-color: #ebf8ff; border-color: #3182ce; color: #2c5282; font-weight: 600;
        }
        /* Ẩn radio mặc định xấu xí đi */
        .form-check-input { position: absolute; opacity: 0; }
    </style>
</head>
<body class="py-4">

    <div class="container">
        <form action="{{ route('student.exams.store', $exam->id) }}" method="POST" id="examForm">
            @csrf
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold m-0">{{ $exam->title }}</h4>
                        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary btn-sm" onclick="return confirm('Bạn muốn thoát? Kết quả sẽ không được lưu.')">Thoát</a>
                    </div>

                    @foreach($exam->questions as $index => $q)
                    <div class="question-card p-4" id="question_{{ $q->id }}">
                        <span class="question-number">Câu {{ $index + 1 }}</span>
                        <h5 class="mb-4 lh-base">{{ $q->content }}</h5>

                        <div class="options-group">
                            <div class="mb-2">
                                <input class="form-check-input option-input" type="radio" 
                                       name="answers[{{ $q->id }}]" value="A" id="q{{$q->id}}A" 
                                       data-index="{{ $index + 1 }}">
                                <label class="option-label" for="q{{$q->id}}A">
                                    <strong class="me-2">A.</strong> {{ $q->option_a }}
                                </label>
                            </div>
                            <div class="mb-2">
                                <input class="form-check-input option-input" type="radio" 
                                       name="answers[{{ $q->id }}]" value="B" id="q{{$q->id}}B" 
                                       data-index="{{ $index + 1 }}">
                                <label class="option-label" for="q{{$q->id}}B">
                                    <strong class="me-2">B.</strong> {{ $q->option_b }}
                                </label>
                            </div>
                            <div class="mb-2">
                                <input class="form-check-input option-input" type="radio" 
                                       name="answers[{{ $q->id }}]" value="C" id="q{{$q->id}}C" 
                                       data-index="{{ $index + 1 }}">
                                <label class="option-label" for="q{{$q->id}}C">
                                    <strong class="me-2">C.</strong> {{ $q->option_c }}
                                </label>
                            </div>
                            <div class="mb-2">
                                <input class="form-check-input option-input" type="radio" 
                                       name="answers[{{ $q->id }}]" value="D" id="q{{$q->id}}D" 
                                       data-index="{{ $index + 1 }}">
                                <label class="option-label" for="q{{$q->id}}D">
                                    <strong class="me-2">D.</strong> {{ $q->option_d }}
                                </label>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="col-lg-4">
                    <div class="sidebar-sticky">
                        <div class="timer-card p-4 text-center mb-3 shadow-lg">
                            <p class="mb-0 small text-white-50 text-uppercase fw-bold">Thời gian còn lại</p>
                            <h1 class="fw-bold display-4 mb-0" id="countdownTimer">
                                {{ str_pad($exam->duration, 2, '0', STR_PAD_LEFT) }}:00
                            </h1>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3 text-muted">Danh sách câu hỏi</h6>
                                <div class="palette-grid">
                                    @foreach($exam->questions as $index => $q)
                                        <a href="#question_{{ $q->id }}" class="palette-item" id="palette_{{ $index + 1 }}">
                                            {{ $index + 1 }}
                                        </a>
                                    @endforeach
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between small text-muted mb-3">
                                    <span><span class="badge bg-white border text-dark">1</span> Chưa làm</span>
                                    <span><span class="badge bg-success">1</span> Đã làm</span>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" onclick="return confirm('Bạn chắc chắn muốn nộp bài? Kiểm tra kỹ lại nhé!')">
                                    Nộp Bài Thi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // 1. Đồng hồ đếm ngược
        let timeInMinutes = {{ $exam->duration }};
        let timeInSeconds = timeInMinutes * 60;
        
        const timerElement = document.getElementById('countdownTimer');
        
        const countdown = setInterval(() => {
            const minutes = Math.floor(timeInSeconds / 60);
            let seconds = timeInSeconds % 60;
            
            seconds = seconds < 10 ? '0' + seconds : seconds;
            timerElement.innerText = `${minutes}:${seconds}`;
            
            timeInSeconds--;
            
            // Hết giờ -> Tự động nộp bài
            if (timeInSeconds < 0) {
                clearInterval(countdown);
                alert('Đã hết giờ làm bài! Hệ thống sẽ tự động nộp bài.');
                document.getElementById('examForm').submit();
            }
        }, 1000);

        // 2. Logic tô màu Palette khi chọn đáp án
        const options = document.querySelectorAll('.option-input');
        
        options.forEach(option => {
            option.addEventListener('change', function() {
                const questionIndex = this.getAttribute('data-index');
                const paletteItem = document.getElementById(`palette_${questionIndex}`);
                
                // Thêm class 'answered' để tô xanh ô số
                if (paletteItem) {
                    paletteItem.classList.add('answered');
                }
            });
        });
    </script>
</body>
</html>