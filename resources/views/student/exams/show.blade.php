@extends('layouts.student')

@section('title', $exam->title)

@push('styles')
    <style>
        /* Layout */
        body { background-color: #f1f5f9; padding-bottom: 80px; /* Chừa chỗ cho thanh thời gian ở dưới */ }
        .exam-container { max-width: 1200px; margin: 30px auto; }
        
        /* Sidebar (Danh sách câu hỏi) */
        .sidebar-nav {
            position: sticky; top: 90px; /* Cách top để không bị menu che */
            background: white; border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); padding: 20px; 
            max-height: 80vh; overflow-y: auto;
        }
        .q-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; }
        .q-btn {
            width: 100%; aspect-ratio: 1; border: 1px solid #e2e8f0; background: white; border-radius: 8px;
            font-weight: 600; color: #64748b; transition: 0.2s; font-size: 0.9rem; padding: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .q-btn:hover { background: #f1f5f9; color: #0f172a; }
        .q-btn.answered { background: #4f46e5; color: white; border-color: #4f46e5; }

        /* Main Content */
        .question-card {
            background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 24px;
            margin-bottom: 24px; scroll-margin-top: 100px; 
        }
        .q-title { font-weight: 700; color: #1e293b; font-size: 1.05rem; margin-bottom: 16px; line-height: 1.6; }
        
        /* Radio Custom */
        .option-label {
            display: flex; align-items: center; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 8px;
            cursor: pointer; transition: 0.2s; margin-bottom: 8px;
        }
        .option-label:hover { background-color: #f8fafc; border-color: #cbd5e0; }
        .form-check-input:checked + .option-text { color: #4f46e5; font-weight: 700; }
        .option-label:has(.form-check-input:checked) { border-color: #4f46e5; background-color: #e0e7ff; }

        /* Timer Header (ĐÃ CHUYỂN XUỐNG DƯỚI) */
        .timer-bar {
            background: #1e293b; color: white; padding: 12px 30px; 
            position: fixed; bottom: 0; left: 0; right: 0; z-index: 9999; /* Z-index cao để nổi lên trên */
            display: flex; justify-content: space-between; align-items: center; 
            box-shadow: 0 -4px 20px rgba(0,0,0,0.15); border-top: 4px solid #fbbf24;
        }
        .timer-clock { font-family: 'Courier New', monospace; font-size: 1.6rem; font-weight: 800; color: #fbbf24; letter-spacing: 2px; }
        
        .tf-table th { background: #f8fafc; font-size: 0.85rem; text-transform: uppercase; color: #64748b; }
    </style>
@endpush

@section('content')

<div class="container-fluid exam-container">
    <form action="{{ route('student.exams.store', $exam->id) }}" method="POST" id="examForm">
        @csrf
        <form id="examForm" action="{{ route('student.exams.store', $exam->id) }}" method="POST">
    @csrf
    {{-- THÊM DÒNG NÀY: Input để chứa thời gian làm bài (tính bằng giây) --}}
    <input type="hidden" name="completion_time" id="completion_time_input" value="0">
    
        <div class="row">
            
            <div class="col-lg-3 d-none d-lg-block">
                <div class="sidebar-nav">
                    <h6 class="fw-bold mb-3 text-secondary text-uppercase small ls-1">Mục lục câu hỏi</h6>
                    <div class="q-grid">
                        @foreach($exam->questions as $index => $q)
                            <a href="#q-{{ $q->id }}" class="btn q-btn" id="nav-btn-{{ $q->id }}">
                                {{ $index + 1 }}
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex align-items-center gap-2 mb-2 small text-muted">
                            <span class="d-inline-block bg-white border rounded" style="width: 15px; height: 15px;"></span> Chưa làm
                        </div>
                        <div class="d-flex align-items-center gap-2 small text-muted">
                            <span class="d-inline-block rounded" style="width: 15px; height: 15px; background: #4f46e5;"></span> Đã làm
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="card mb-4 border-0 shadow-sm bg-white">
                    <div class="card-body p-4">
                        <h4 class="fw-bold text-primary m-0">{{ $exam->title }}</h4>
                        <p class="text-muted m-0 mt-1"><i class="fa-regular fa-clock me-1"></i> Thời gian: {{ $exam->duration }} phút &bull; {{ $exam->questions->count() }} câu hỏi</p>
                    </div>
                </div>

                @foreach($exam->questions as $index => $q)
                    <div class="question-card" id="q-{{ $q->id }}">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="badge bg-light text-dark border fw-bold fs-6">
                                Câu {{ $index + 1 }} 
                            </span>
                            @if($q->level)
                                <span class="badge bg-info bg-opacity-10 text-info border border-info">{{ strtoupper($q->level) }}</span>
                            @endif
                        </div>

                        <div class="q-title" style="white-space: pre-wrap;">{{ $q->content }}</div>
                        
                        @if($q->image)
                            <div class="mb-3 text-center">
                                <img src="{{ asset('storage/' . $q->image) }}" class="img-fluid rounded border shadow-sm" style="max-height: 300px;">
                            </div>
                        @endif

                        @if($q->type == 'one_choice')
                            <div class="row g-2">
                                @foreach(['A', 'B', 'C', 'D'] as $opt)
                                    <div class="col-md-6">
                                        <label class="option-label">
                                            <input class="form-check-input me-3" type="radio" 
                                                   name="answers[{{ $q->id }}]" 
                                                   value="{{ $opt }}"
                                                   onchange="markAnswered({{ $q->id }})">
                                            <span class="option-text">
                                                <span class="fw-bold me-1">{{ $opt }}.</span> 
                                                {{ $q->{'option_'.strtolower($opt)} }}
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($q->type == 'true_false')
                            <div class="table-responsive border rounded-3 overflow-hidden">
                                <table class="table table-bordered table-striped mb-0 tf-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="50">Ý</th>
                                            <th>Nội dung mệnh đề</th>
                                            <th class="text-center" width="150">Đúng / Sai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(['a', 'b', 'c', 'd'] as $key)
                                            <tr>
                                                <td class="text-center fw-bold">{{ strtoupper($key) }}</td>
                                                <td>{{ $q->{'option_'.$key} }}</td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <input type="radio" class="btn-check" name="answers[{{ $q->id }}][{{ $key }}]" id="tf_{{$q->id}}_{{$key}}_T" value="T" onchange="markAnswered({{ $q->id }})">
                                                        <label class="btn btn-outline-success btn-sm fw-bold" for="tf_{{$q->id}}_{{$key}}_T">Đúng</label>

                                                        <input type="radio" class="btn-check" name="answers[{{ $q->id }}][{{ $key }}]" id="tf_{{$q->id}}_{{$key}}_F" value="F" onchange="markAnswered({{ $q->id }})">
                                                        <label class="btn btn-outline-danger btn-sm fw-bold" for="tf_{{$q->id}}_{{$key}}_F">Sai</label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @endforeach

                <div class="text-center mt-5 mb-5 pb-5">
                    <p class="text-muted small">Bạn đã hoàn thành bài thi? Hãy kiểm tra kỹ trước khi nộp.</p>
                    <button type="button" class="btn btn-primary btn-lg px-5 fw-bold shadow" onclick="submitExam()">
                        <i class="fa-solid fa-paper-plane me-2"></i> NỘP BÀI NGAY
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="timer-bar">
    <div class="d-flex align-items-center gap-3">
        <div class="d-flex flex-column">
            <small class="text-white-50" style="font-size: 0.75rem; text-transform: uppercase;">Thời gian còn lại</small>
            <div id="countdown" class="timer-clock">00:00:00</div>
        </div>
    </div>
    <button type="button" class="btn btn-warning fw-bold text-dark px-4" onclick="submitExam()">
        Nộp bài
    </button>
</div>

@endsection

@push('scripts')
<script>
    // 1. Logic đếm ngược thời gian
    let timeRemaining = {{ $exam->duration * 60 }}; // Đổi phút sang giây
    const countdownEl = document.getElementById('countdown');
    const examForm = document.getElementById('examForm');
    
    // Hàm format thời gian hh:mm:ss
    function formatTime(seconds) {
        const h = Math.floor(seconds / 3600).toString().padStart(2, '0');
        const m = Math.floor((seconds % 3600) / 60).toString().padStart(2, '0');
        const s = (seconds % 60).toString().padStart(2, '0');
        return `${h}:${m}:${s}`;
    }

    const timerInterval = setInterval(() => {
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            countdownEl.innerHTML = "00:00:00";
            
            // --- FIX LỖI: Gỡ cảnh báo trước khi tự động nộp ---
            window.onbeforeunload = null; 
            
            alert("Hết giờ làm bài! Hệ thống sẽ tự động nộp bài.");
            examForm.submit();
            return;
        }
        
        countdownEl.innerHTML = formatTime(timeRemaining);
        
        // Cảnh báo khi còn dưới 5 phút (đổi màu đỏ)
        if (timeRemaining < 300) {
            countdownEl.style.color = '#ef4444'; 
            document.querySelector('.timer-bar').style.borderColor = '#ef4444';
        }
        
        timeRemaining--;
    }, 1000);

    // 2. Đánh dấu câu hỏi đã làm trên Sidebar
    function markAnswered(id) {
        const navBtn = document.getElementById('nav-btn-' + id);
        if(navBtn) navBtn.classList.add('answered');
    }

    // 3. Xác nhận nộp bài (FIX LỖI Ở ĐÂY)
    function submitExam() {
        if(confirm('Bạn có chắc chắn muốn nộp bài không?\nHãy kiểm tra kỹ các câu hỏi trước khi xác nhận.')) {
            // --- QUAN TRỌNG: Gỡ bỏ cảnh báo rời trang ---
            window.onbeforeunload = null;
            
            // Sau đó mới submit form
            examForm.submit();
        }
    }

    // 4. Chặn F5/Back (Cảnh báo)
    window.onbeforeunload = function() {
        return "Cảnh báo: Nếu bạn tải lại trang, bài làm sẽ bị mất!";
    };
</script>
@endpush