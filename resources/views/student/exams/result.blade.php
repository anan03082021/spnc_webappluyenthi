@extends('layouts.student')

@section('title', 'Kết quả: ' . $result->exam->title)

@push('styles')
    <style>
        body { background-color: #f1f5f9; font-family: 'Inter', sans-serif; }
        
        /* 1. Header Card */
        .result-header-card {
            background: white; border-radius: 16px; padding: 30px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 30px;
            display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;
        }
        .score-circle {
            width: 100px; height: 100px; border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: white; display: flex; flex-direction: column; align-items: center; justify-content: center;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        }
        .score-val { font-size: 2rem; font-weight: 800; line-height: 1; }
        .score-label { font-size: 0.75rem; font-weight: 600; opacity: 0.9; }

        /* 2. Question Review Card */
        .q-review-card {
            background: white; border-radius: 12px; border: 1px solid #e2e8f0; 
            margin-bottom: 20px; overflow: hidden; position: relative;
        }
        /* Border màu trạng thái */
        .q-review-card.correct { border-left: 5px solid #10b981; } /* Xanh lá */
        .q-review-card.wrong { border-left: 5px solid #ef4444; }   /* Đỏ */
        .q-review-card.partial { border-left: 5px solid #f59e0b; }  /* Cam (Vàng đậm) */

        .q-header {
            padding: 15px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;
            display: flex; justify-content: space-between; align-items: center;
        }
        .q-body { padding: 20px; }

        /* 3. Options (Trắc nghiệm) */
        .opt-row {
            padding: 10px 15px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 8px;
            display: flex; align-items: center; font-size: 0.95rem; position: relative;
        }
        /* Chọn Đúng */
        .opt-correct-user { background-color: #dcfce7; border-color: #10b981; color: #166534; font-weight: 600; }
        .opt-correct-user::after { content: '\f00c'; font-family: "Font Awesome 6 Free"; font-weight: 900; margin-left: auto; color: #15803d; }
        /* Chọn Sai */
        .opt-wrong-user { background-color: #fee2e2; border-color: #ef4444; color: #991b1b; }
        .opt-wrong-user::after { content: '\f00d'; font-family: "Font Awesome 6 Free"; font-weight: 900; margin-left: auto; color: #b91c1c; }
        /* Đáp án đúng (HS không chọn) */
        .opt-key { background-color: #f0fdf4; border-color: #86efac; border-style: dashed; color: #15803d; }
        .opt-key::after { content: '(Đáp án đúng)'; font-size: 0.75rem; margin-left: auto; font-weight: 700; }

        /* 4. Table True/False */
        .tf-table th { background: #f8fafc; font-size: 0.85rem; text-transform: uppercase; color: #64748b; }
        .tf-cell-correct { color: #10b981; font-weight: 700; background: #ecfdf5; }
        .tf-cell-wrong { color: #ef4444; font-weight: 700; background: #fef2f2; }

        /* 5. Explanation */
        .explain-box {
            margin-top: 15px; background: #fffbeb; border: 1px solid #fcd34d; 
            border-radius: 8px; padding: 15px; font-size: 0.9rem; color: #92400e;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid py-4" style="max-width: 1100px;">
    
    <div class="result-header-card" data-aos="fade-down">
        <div class="d-flex align-items-center gap-4">
            <div class="score-circle">
                <span class="score-val">{{ $result->score }}</span>
                <span class="score-label">ĐIỂM</span>
            </div>
            <div>
                <h4 class="fw-bold text-dark m-0 mb-1">
                    @if($result->score >= 8) Xuất sắc! 🎉
                    @elseif($result->score >= 5) Làm tốt lắm! 👍
                    @else Cần cố gắng hơn! 💪
                    @endif
                </h4>
                <p class="text-muted m-0 small">Hoàn thành: {{ $result->created_at->format('H:i d/m/Y') }}</p>
                <div class="mt-2">
                    <a href="{{ route('student.exams.show', $result->exam_id) }}" class="btn btn-sm btn-primary fw-bold">
                        <i class="fa-solid fa-rotate-right me-1"></i>Làm lại
                    </a>
                    <a href="{{ route('student.dashboard') }}" class="btn btn-sm btn-light border fw-bold ms-2">
                        <i class="fa-solid fa-house me-1"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(isset($recommendedDocuments) && $recommendedDocuments->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-3 mb-4" style="background: #f0f9ff; border-left: 4px solid #0ea5e9 !important;">
            <div class="card-body p-3">
                <h6 class="fw-bold text-dark mb-2">
                    <i class="fa-solid fa-book-open-reader text-primary me-2"></i>Góc ôn tập
                </h6>
                <p class="text-muted small mb-2">Dựa trên kết quả, hệ thống đề xuất tài liệu sau:</p>
                <div class="row g-2">
                    @foreach($recommendedDocuments as $doc)
                        <div class="col-md-4">
                            <a href="{{ route('student.documents.download', $doc->id) }}" class="text-decoration-none">
                                <div class="bg-white p-2 rounded border shadow-sm d-flex align-items-center hover-shadow">
                                    <i class="fa-solid fa-file-pdf text-danger fs-4 me-2"></i>
                                    <div class="text-truncate flex-grow-1 text-dark small fw-bold">{{ $doc->title }}</div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <h5 class="fw-bold text-dark mb-3 ps-2 border-start border-4 border-primary">Chi tiết bài làm</h5>

    @foreach($result->exam->questions as $index => $q)
        @php
            $userAns = $studentAnswers[$q->id] ?? null;
            
            // --- CẤU HÌNH TRẠNG THÁI HIỂN THỊ ---
            $cardStatus = 'wrong'; // Class CSS cho viền thẻ (correct, wrong, partial)
            $statusText = 'Sai rồi';
            $badgeClass = 'bg-danger bg-opacity-10 text-danger border-danger'; 

            // 1. TRẮC NGHIỆM
            if ($q->type == 'one_choice') {
                if (($userAns ?? '') === $q->correct_answer) {
                    $cardStatus = 'correct';
                    $statusText = 'Chính xác';
                    $badgeClass = 'bg-success bg-opacity-10 text-success border-success';
                }
            } 
            // 2. ĐÚNG / SAI
            elseif ($q->type == 'true_false') {
                $correctArr = explode(',', $q->correct_answer);
                $keys = ['a', 'b', 'c', 'd'];
                $countRight = 0;
                
                foreach($keys as $idx => $key) {
                    $u = $userAns[$key] ?? 'F';
                    $c = $correctArr[$idx] ?? 'F';
                    if ($u == $c) $countRight++;
                }

                if ($countRight == 4) {
                    $cardStatus = 'correct';
                    $statusText = 'Chính xác tuyệt đối';
                    $badgeClass = 'bg-success bg-opacity-10 text-success border-success';
                } elseif ($countRight > 0) {
                    $cardStatus = 'partial'; // Trạng thái: Đúng một phần
                    $statusText = "Đúng $countRight/4 ý";
                    $badgeClass = 'bg-warning bg-opacity-10 text-dark border-warning'; 
                } else {
                    $cardStatus = 'wrong';
                    $statusText = 'Sai toàn bộ';
                    $badgeClass = 'bg-danger bg-opacity-10 text-danger border-danger';
                }
            }
        @endphp

        <div class="q-review-card {{ $cardStatus }}">
            <div class="q-header">
                <span class="fw-bold text-secondary">Câu {{ $index + 1 }}</span>
                <span class="badge border {{ $badgeClass }}">
                    {{ $statusText }}
                </span>
            </div>
            
            <div class="q-body">
                <p class="fw-bold mb-3" style="white-space: pre-wrap;">{{ $q->content }}</p>
                @if($q->image)
                    <img src="{{ asset('storage/' . $q->image) }}" class="img-fluid rounded border mb-3" style="max-height: 200px">
                @endif

                {{-- A. TRẮC NGHIỆM --}}
                @if($q->type == 'one_choice')
                    @foreach(['A', 'B', 'C', 'D'] as $opt)
                        @php
                            $cssClass = '';
                            $isUserSelect = ($userAns === $opt);
                            $isKey = ($q->correct_answer === $opt);

                            if ($isUserSelect && $isKey) $cssClass = 'opt-correct-user';
                            elseif ($isUserSelect && !$isKey) $cssClass = 'opt-wrong-user';
                            elseif (!$isUserSelect && $isKey) $cssClass = 'opt-key';
                            else $cssClass = 'bg-light opacity-75'; 
                        @endphp
                        <div class="opt-row {{ $cssClass }}">
                            <span class="fw-bold me-2">{{ $opt }}.</span> {{ $q->{'option_'.strtolower($opt)} }}
                        </div>
                    @endforeach

                {{-- B. ĐÚNG / SAI --}}
                @elseif($q->type == 'true_false')
                    @php
                        $correctArr = explode(',', $q->correct_answer);
                        $keys = ['a', 'b', 'c', 'd'];
                    @endphp
                    <div class="table-responsive border rounded">
                        <table class="table table-bordered table-sm mb-0 tf-table">
                            <thead>
                                <tr>
                                    <th class="text-center" width="50">Ý</th>
                                    <th>Nội dung</th>
                                    <th class="text-center" width="100">Bạn chọn</th>
                                    <th class="text-center" width="100">Đáp án</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($keys as $idx => $key)
                                    @php
                                        $u = $userAns[$key] ?? 'F';
                                        $c = $correctArr[$idx] ?? 'F';
                                        $match = ($u == $c);
                                    @endphp
                                    <tr>
                                        <td class="text-center fw-bold">{{ strtoupper($key) }}</td>
                                        <td>{{ $q->{'option_'.$key} }}</td>
                                        <td class="text-center {{ $match ? 'tf-cell-correct' : 'tf-cell-wrong' }}">
                                            {{ $u == 'T' ? 'Đúng' : 'Sai' }}
                                        </td>
                                        <td class="text-center fw-bold text-primary">
                                            {{ $c == 'T' ? 'Đúng' : 'Sai' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- GIẢI THÍCH --}}
                @if($q->explanation)
                    <div class="explain-box">
                        <div class="fw-bold mb-1"><i class="fa-regular fa-lightbulb me-1"></i> Giải thích chi tiết:</div>
                        {{ $q->explanation }}
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    <div class="text-center mt-5 mb-5">
        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary px-4 rounded-pill">
            <i class="fa-solid fa-arrow-left me-1"></i> Trở về trang chủ
        </a>
    </div>

</div>
@endsection