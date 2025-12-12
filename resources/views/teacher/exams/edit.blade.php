@extends('layouts.teacher')

@section('title', 'Chỉnh sửa đề thi')

@push('styles')
    <style>
        /* CSS cho nút gạt True/False */
        .form-switch .form-check-input {
            width: 3em !important; height: 1.5em !important; cursor: pointer;
        }
        .form-switch .form-check-input:checked {
            background-color: #10b981; border-color: #10b981;
        }
        .question-card { transition: all 0.3s; }
        .question-card:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    </style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <form action="{{ route('teacher.exams.update', $exam->id) }}" method="POST">
        @csrf
        @method('PUT') 
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark m-0">Chỉnh sửa đề thi</h3>
                <p class="text-muted small m-0">#ID: {{ $exam->id }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('teacher.exams.index') }}" class="btn btn-light border fw-bold">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary fw-bold px-4">
                    <i class="fa-solid fa-save me-2"></i> Lưu thay đổi
                </button>
            </div>
        </div>

        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white fw-bold text-primary"><i class="fa-solid fa-gear me-2"></i>Cấu hình chung</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Tên đề thi</label>
                        <input type="text" name="title" class="form-control fw-bold" value="{{ $exam->title }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Thời gian (phút)</label>
                        <input type="number" name="duration" class="form-control" value="{{ $exam->duration }}" required>
                    </div>
<div class="col-md-2">
    <label class="form-label fw-bold">Trạng thái hiện tại</label>
    <div>
        @if($exam->is_published)
            <span class="badge bg-success py-2 w-100"><i class="fa-solid fa-check me-1"></i>Đã duyệt</span>
        @else
            <span class="badge bg-warning text-dark py-2 w-100"><i class="fa-solid fa-clock me-1"></i>Chờ duyệt</span>
        @endif
        {{-- Input ẩn để tránh lỗi nếu Controller lỡ gọi --}}
        <input type="hidden" name="is_published" value="0">
    </div>
</div>
                </div>
            </div>
        </div>

        <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-list-ol me-2"></i>Danh sách câu hỏi</h5>
        
        <div id="questions-container">
            @foreach($exam->questions as $index => $q)
                <input type="hidden" name="questions[{{ $index }}][id]" value="{{ $q->id }}">
                <input type="hidden" name="questions[{{ $index }}][type]" value="{{ $q->type }}">

                {{-- Nếu là one_choice HOẶC null (cũ) thì hiện trắc nghiệm thường --}}
                @if($q->type == 'one_choice' || $q->type == null)
                    <div class="card mb-3 question-card border-start border-4 border-primary shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge bg-primary">Câu {{ $index + 1 }} (4 chọn 1)</span>
                                <button type="button" class="btn btn-sm text-danger" onclick="removeQuestion(this)"><i class="fa-solid fa-trash"></i></button>
                            </div>
                            
                            <textarea name="questions[{{ $index }}][content]" class="form-control mb-2 fw-bold" rows="2" required>{{ $q->content }}</textarea>
                            
                            <div class="row g-2 mb-2">
                                <div class="col-md-6"><div class="input-group"><span class="input-group-text">A</span><input type="text" name="questions[{{ $index }}][option_a]" class="form-control" value="{{ $q->option_a }}" required></div></div>
                                <div class="col-md-6"><div class="input-group"><span class="input-group-text">B</span><input type="text" name="questions[{{ $index }}][option_b]" class="form-control" value="{{ $q->option_b }}" required></div></div>
                                <div class="col-md-6"><div class="input-group"><span class="input-group-text">C</span><input type="text" name="questions[{{ $index }}][option_c]" class="form-control" value="{{ $q->option_c }}" required></div></div>
                                <div class="col-md-6"><div class="input-group"><span class="input-group-text">D</span><input type="text" name="questions[{{ $index }}][option_d]" class="form-control" value="{{ $q->option_d }}" required></div></div>
                            </div>

                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="small fw-bold me-2">Đáp án đúng:</label>
                                    <select name="questions[{{ $index }}][correct_answer]" class="form-select form-select-sm d-inline-block w-auto fw-bold text-success">
                                        <option value="A" {{ $q->correct_answer == 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ $q->correct_answer == 'B' ? 'selected' : '' }}>B</option>
                                        <option value="C" {{ $q->correct_answer == 'C' ? 'selected' : '' }}>C</option>
                                        <option value="D" {{ $q->correct_answer == 'D' ? 'selected' : '' }}>D</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    @php
                        // Tách chuỗi T,F,T,F thành mảng
                        $answers = explode(',', $q->correct_answer); 
                        // Map index 0,1,2,3 thành key a,b,c,d để dễ xử lý
                        $tfMap = [
                            'a' => $answers[0] ?? 'F',
                            'b' => $answers[1] ?? 'F',
                            'c' => $answers[2] ?? 'F',
                            'd' => $answers[3] ?? 'F',
                        ];
                    @endphp
                    <div class="card mb-3 question-card border-start border-4 border-warning shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge bg-warning text-dark">Câu {{ $index + 1 }} (Đúng/Sai)</span>
                                <button type="button" class="btn btn-sm text-danger" onclick="removeQuestion(this)"><i class="fa-solid fa-trash"></i></button>
                            </div>

                            <textarea name="questions[{{ $index }}][content]" class="form-control mb-3 fw-bold" rows="2" required>{{ $q->content }}</textarea>

                            <div class="table-responsive border rounded">
                                <table class="table table-sm table-borderless mb-0 align-middle">
                                    <thead class="bg-light text-secondary">
                                        <tr>
                                            <th class="ps-3" style="width: 50px;">Ý</th>
                                            <th>Nội dung mệnh đề</th>
                                            <th class="text-center" style="width: 100px;">Đúng/Sai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(['a', 'b', 'c', 'd'] as $key)
                                        <tr>
                                            <td class="ps-3 text-center"><span class="badge bg-secondary rounded-circle">{{ strtoupper($key) }}</span></td>
                                            <td>
                                                <input type="text" name="questions[{{ $index }}][tf_options][{{ $key }}]" 
                                                       class="form-control form-control-sm border-0" 
                                                       value="{{ $q->{'option_'.$key} }}" placeholder="Nhập mệnh đề...">
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input type="hidden" name="questions[{{ $index }}][tf_correct][{{ $key }}]" value="F">
                                                    <input class="form-check-input" type="checkbox" role="switch" 
                                                           name="questions[{{ $index }}][tf_correct][{{ $key }}]" value="T" 
                                                           {{ $tfMap[$key] == 'T' ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="row g-2 mt-3">
            <div class="col-md-6">
                <button type="button" class="btn btn-outline-primary w-100 py-2 dashed-border" onclick="addOneChoice()">
                    <i class="fa-solid fa-plus me-2"></i> Thêm Trắc nghiệm (4 chọn 1)
                </button>
            </div>
            <div class="col-md-6">
                <button type="button" class="btn btn-outline-warning text-dark w-100 py-2 dashed-border" onclick="addTrueFalse()">
                    <i class="fa-solid fa-toggle-on me-2"></i> Thêm Đúng / Sai
                </button>
            </div>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
    let qIndex = {{ count($exam->questions) }}; 

    function removeQuestion(btn) {
        if(confirm('Xóa câu hỏi này?')) {
            btn.closest('.question-card').remove();
        }
    }

    // 1. Thêm câu 4 chọn 1
    function addOneChoice() {
        const html = `
        <div class="card mb-3 question-card border-start border-4 border-primary shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="badge bg-primary">Câu mới (4 chọn 1)</span>
                    <button type="button" class="btn btn-sm text-danger" onclick="removeQuestion(this)"><i class="fa-solid fa-trash"></i></button>
                </div>
                <input type="hidden" name="questions[${qIndex}][type]" value="one_choice">
                
                <textarea name="questions[${qIndex}][content]" class="form-control mb-2" rows="2" placeholder="Nhập câu hỏi..." required></textarea>
                
                <div class="row g-2 mb-2">
                    <div class="col-md-6"><div class="input-group"><span class="input-group-text">A</span><input type="text" name="questions[${qIndex}][option_a]" class="form-control" placeholder="Đáp án A" required></div></div>
                    <div class="col-md-6"><div class="input-group"><span class="input-group-text">B</span><input type="text" name="questions[${qIndex}][option_b]" class="form-control" placeholder="Đáp án B" required></div></div>
                    <div class="col-md-6"><div class="input-group"><span class="input-group-text">C</span><input type="text" name="questions[${qIndex}][option_c]" class="form-control" placeholder="Đáp án C" required></div></div>
                    <div class="col-md-6"><div class="input-group"><span class="input-group-text">D</span><input type="text" name="questions[${qIndex}][option_d]" class="form-control" placeholder="Đáp án D" required></div></div>
                </div>

                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label class="small fw-bold me-2">Đáp án đúng:</label>
                        <select name="questions[${qIndex}][correct_answer]" class="form-select form-select-sm d-inline-block w-auto fw-bold text-success">
                            <option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>`;
        insertHtml(html);
    }

    // 2. Thêm câu Đúng/Sai
    function addTrueFalse() {
        const keys = ['a', 'b', 'c', 'd'];
        let rows = keys.map(k => `
            <tr>
                <td class="ps-3 text-center"><span class="badge bg-secondary rounded-circle">${k.toUpperCase()}</span></td>
                <td><input type="text" name="questions[${qIndex}][tf_options][${k}]" class="form-control form-control-sm border-0" placeholder="Nhập mệnh đề..."></td>
                <td class="text-center">
                    <div class="form-check form-switch d-flex justify-content-center">
                        <input type="hidden" name="questions[${qIndex}][tf_correct][${k}]" value="F">
                        <input class="form-check-input" type="checkbox" role="switch" name="questions[${qIndex}][tf_correct][${k}]" value="T">
                    </div>
                </td>
            </tr>
        `).join('');

        const html = `
        <div class="card mb-3 question-card border-start border-4 border-warning shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="badge bg-warning text-dark">Câu mới (Đúng/Sai)</span>
                    <button type="button" class="btn btn-sm text-danger" onclick="removeQuestion(this)"><i class="fa-solid fa-trash"></i></button>
                </div>
                <input type="hidden" name="questions[${qIndex}][type]" value="true_false">

                <textarea name="questions[${qIndex}][content]" class="form-control mb-3" rows="2" placeholder="Nhập câu dẫn (Ví dụ: Các mệnh đề sau đúng hay sai?)..." required></textarea>

                <div class="table-responsive border rounded">
                    <table class="table table-sm table-borderless mb-0 align-middle">
                        <thead class="bg-light text-secondary">
                            <tr><th class="ps-3" style="width: 50px;">Ý</th><th>Nội dung mệnh đề</th><th class="text-center" style="width: 100px;">Đúng/Sai</th></tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>
            </div>
        </div>`;
        insertHtml(html);
    }

    function insertHtml(html) {
        document.getElementById('questions-container').insertAdjacentHTML('beforeend', html);
        qIndex++; // Tăng index để không trùng name
    }
</script>
@endpush