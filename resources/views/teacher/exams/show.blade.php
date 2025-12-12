@extends('layouts.teacher')

@section('title', $exam->title)

@push('styles')
    <style>
        .question-card { transition: all 0.2s; border-left: 4px solid #cbd5e1; }
        .question-card:hover { border-left-color: #3b82f6; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .badge-diff { font-size: 0.75rem; padding: 5px 10px; border-radius: 4px; }
        .bg-easy { background: #dcfce7; color: #166534; }
        .bg-medium { background: #fef9c3; color: #854d0e; }
        .bg-hard { background: #fee2e2; color: #991b1b; }
        .option-item { position: relative; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 5px; }
        .option-correct { background-color: #ecfdf5; border-color: #10b981; color: #065f46; font-weight: 600; }
        .option-correct::after { content: '\f00c'; font-family: "Font Awesome 6 Free"; font-weight: 900; position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #10b981; }
        .tf-table th { background-color: #f8fafc; font-size: 0.8rem; text-transform: uppercase; color: #64748b; }
        .tf-badge { min-width: 60px; display: inline-block; text-align: center; }
    </style>
@endpush

@section('content')
<div class="container-fluid py-4">
    
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('teacher.exams.index') }}" class="text-muted"><i class="fa-solid fa-arrow-left"></i></a>
                <h3 class="fw-bold text-dark m-0">{{ $exam->title }}</h3>
                @if($exam->is_published)
                    <span class="badge bg-success fs-6"><i class="fa-solid fa-check-circle me-1"></i>Admin đã duyệt</span>
                @else
                    <span class="badge bg-warning text-dark fs-6"><i class="fa-solid fa-hourglass-half me-1"></i>Đang chờ Admin duyệt</span>
                @endif
            </div>
            <p class="text-muted small m-0 ms-4 ps-1">Ngày tạo: {{ $exam->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('teacher.exams.edit', $exam->id) }}" class="btn btn-warning fw-bold text-dark shadow-sm">
                <i class="fa-solid fa-pen-to-square me-2"></i>Chỉnh sửa
            </a>
            <button class="btn btn-outline-primary fw-bold shadow-sm" onclick="window.print()">
                <i class="fa-solid fa-print me-2"></i>In đề
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 20px; z-index: 1;">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fa-solid fa-circle-info me-2 text-primary"></i>Thông số đề thi
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Thời gian</span>
                        <span class="fw-bold">{{ $exam->duration }} phút</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Số câu hỏi</span>
                        <span class="fw-bold">{{ $exam->questions->count() }} câu</span>
                    </div>
                    
                    {{-- ĐÃ XÓA PHẦN ĐỘ KHÓ TẠI ĐÂY --}}

                    @if($exam->password)
                    <div class="list-group-item">
                        <span class="text-muted small d-block mb-1">Mật khẩu bài thi</span>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control fw-bold text-danger bg-white" value="{{ $exam->password }}" readonly>
                            <span class="input-group-text"><i class="fa-solid fa-key"></i></span>
                        </div>
                    </div>
                    @endif
                    <div class="list-group-item bg-light text-center p-3">
                        <small class="text-muted d-block mb-2">Link chia sẻ cho học sinh:</small>
                        <div class="d-flex gap-1">
                            <input type="text" class="form-control form-control-sm" value="{{ route('student.exams.show', $exam->id) }}" id="shareLink" readonly>
                            <button class="btn btn-sm btn-dark" onclick="copyLink()"><i class="fa-regular fa-copy"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            @forelse($exam->questions as $index => $q)
                <div class="card mb-4 question-card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <div>
                                <span class="badge bg-primary me-2">Câu {{ $index + 1 }}</span>
                                <span class="badge bg-light text-dark border">
                                    {{ $q->type == 'true_false' ? 'Đúng / Sai' : '4 Chọn 1' }}
                                </span>
                                @if($q->level)
                                    <span class="badge bg-light text-secondary border ms-1">{{ strtoupper($q->level) }}</span>
                                @endif
                            </div>
                            <small class="text-muted">ID: {{ $q->id }}</small>
                        </div>

                        <div class="mb-3">
                            <h6 class="fw-bold text-dark" style="line-height: 1.6; white-space: pre-wrap;">{{ $q->content }}</h6>
                            @if($q->image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $q->image) }}" class="img-fluid rounded border" style="max-height: 250px;">
                                </div>
                            @endif
                        </div>

                        @if($q->type == 'true_false')
                            {{-- LOGIC HIỂN THỊ ĐÚNG/SAI --}}
                            @php
                                $answers = explode(',', $q->correct_answer);
                                $keys = ['a', 'b', 'c', 'd'];
                            @endphp
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm tf-table mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="5%">Ý</th>
                                            <th width="75%">Nội dung mệnh đề</th>
                                            <th class="text-center" width="20%">Đáp án đúng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($keys as $k => $keyChar)
                                            @php 
                                                $isTrue = isset($answers[$k]) && ($answers[$k] == 'T' || $answers[$k] == 'Đ');
                                            @endphp
                                            <tr>
                                                <td class="text-center fw-bold">{{ strtoupper($keyChar) }}</td>
                                                <td>{{ $q->{'option_'.$keyChar} }}</td>
                                                <td class="text-center">
                                                    @if($isTrue)
                                                        <span class="badge bg-success tf-badge">ĐÚNG</span>
                                                    @else
                                                        <span class="badge bg-secondary tf-badge">SAI</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            {{-- LOGIC HIỂN THỊ TRẮC NGHIỆM --}}
                            <div class="row g-2">
                                @foreach(['a', 'b', 'c', 'd'] as $key)
                                    @php
                                        $isCorrect = strtoupper($q->correct_answer) === strtoupper($key);
                                    @endphp
                                    <div class="col-md-6">
                                        <div class="option-item {{ $isCorrect ? 'option-correct' : '' }}">
                                            <span class="fw-bold me-2">{{ strtoupper($key) }}.</span>
                                            {{ $q->{'option_'.$key} }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($q->explanation)
                            <div class="mt-3 p-3 bg-light rounded border-start border-4 border-warning">
                                <span class="fw-bold text-warning"><i class="fa-regular fa-lightbulb me-1"></i> Giải thích:</span>
                                <span class="text-secondary">{{ $q->explanation }}</span>
                            </div>
                        @endif

                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png" width="150" class="opacity-50">
                    <p class="text-muted mt-3">Đề thi này chưa có câu hỏi nào.</p>
                    <a href="{{ route('teacher.exams.edit', $exam->id) }}" class="btn btn-primary">Thêm câu hỏi ngay</a>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    function copyLink() {
        var copyText = document.getElementById("shareLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        alert("Đã sao chép đường dẫn đề thi!");
    }
</script>
@endsection