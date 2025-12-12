@extends('layouts.teacher')

@section('title', 'Tạo đề thi mới')

@push('styles')
    <style>
        /* Question Card Style */
        .question-card { transition: all 0.3s; border-left: 4px solid #cbd5e1; }
        .question-card:hover { border-left-color: #3b82f6; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        
        .question-index {
            width: 28px; height: 28px; background: #e2e8f0; color: #475569; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8rem;
        }
        
        /* Config Card Sticky */
        .config-card { position: sticky; top: 20px; z-index: 10; }
    </style>
@endpush

@section('content')
<div class="container-fluid py-4">
    
    <form action="{{ route('teacher.exams.store') }}" method="POST" id="examForm" enctype="multipart/form-data">
        @csrf
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark m-0">Tạo đề thi mới</h3>
                <p class="text-muted small m-0">Soạn thảo đề thi thủ công từng câu hỏi</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('teacher.exams.index') }}" class="btn btn-light border fw-bold">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">
                    <i class="fa-solid fa-save me-2"></i> Hoàn tất & Lưu
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div id="questions-wrapper"></div>

                <div class="text-center mt-4 p-4 border-2 border-dashed rounded-3 bg-light">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/add-files-4569666-3806981.png" width="60" style="opacity: 0.6">
                    <p class="text-muted small mt-2 mb-3">Thêm câu hỏi mới vào đề thi</p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-primary fw-bold" onclick="addOneChoice()">
                            <i class="fa-solid fa-plus me-1"></i> Trắc nghiệm (4 chọn 1)
                        </button>
                        <button type="button" class="btn btn-outline-warning text-dark fw-bold" onclick="addTrueFalse()">
                            <i class="fa-solid fa-toggle-on me-1"></i> Đúng / Sai
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm border-0 config-card">
                    <div class="card-header bg-white fw-bold text-primary py-3">
                        <i class="fa-solid fa-sliders me-2"></i> Cấu hình chung
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Tên đề thi <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control fw-bold" placeholder="VD: Kiểm tra 1 tiết Tin học 11..." required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">Thời gian làm bài</label>
                            <div class="input-group">
                                <input type="number" name="duration" class="form-control fw-bold text-center" value="45" min="5" required>
                                <span class="input-group-text bg-light">phút</span>
                            </div>
                        </div>

                        <div class="alert alert-info small border-0 bg-info-subtle text-info-emphasis mb-0">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            <strong>Lưu ý:</strong> Chọn đúng <b>Chủ đề</b> và <b>Mức độ Bloom</b> để thống kê chất lượng câu hỏi chính xác.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    let questionCounter = 0; // Để tạo index unique cho name="questions[0]..."

    // 1. Thêm câu 4 chọn 1 (Có Select Mức độ)
    function addOneChoice() {
        const index = document.querySelectorAll('.question-card').length + 1;
        const html = `
        <div class="card mb-3 question-card border-start border-4 border-primary shadow-sm" id="card_${questionCounter}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary">Câu ${index} (4 chọn 1)</span>
                        
                        <select name="questions[${questionCounter}][level]" class="form-select form-select-sm border-0 bg-light fw-bold text-secondary" style="width: 140px;">
                            <option value="easy">NB (Nhận biết)</option>
                            <option value="medium" selected>TH (Thông hiểu)</option>
                            <option value="hard">VD (Vận dụng)</option>
                            <option value="very_hard">VDC (Vận dụng cao)</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-sm text-danger" onclick="removeQuestion(this)"><i class="fa-solid fa-trash"></i></button>
                </div>
                
                <input type="hidden" name="questions[${questionCounter}][type]" value="one_choice">
                
                <div class="mb-2">
                    <textarea name="questions[${questionCounter}][content]" class="form-control fw-bold" rows="2" placeholder="Nhập nội dung câu hỏi..." required></textarea>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-primary">Chủ đề</label>
                        <select name="questions[${questionCounter}][category_id]" class="form-select form-select-sm border-primary bg-primary-subtle text-primary fw-bold">
                            <option value="">-- Chọn chủ đề --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                         <div class="input-group input-group-sm">
                            <input type="file" name="questions[${questionCounter}][image]" class="form-control" accept="image/*">
                            <span class="input-group-text"><i class="fa-regular fa-image"></i></span>
                        </div>
                    </div>
                </div>
                
                <div class="row g-2 mb-2">
                    <div class="col-md-6"><div class="input-group"><span class="input-group-text">A</span><input type="text" name="questions[${questionCounter}][option_a]" class="form-control" required></div></div>
                    <div class="col-md-6"><div class="input-group"><span class="input-group-text">B</span><input type="text" name="questions[${questionCounter}][option_b]" class="form-control" required></div></div>
                    <div class="col-md-6"><div class="input-group"><span class="input-group-text">C</span><input type="text" name="questions[${questionCounter}][option_c]" class="form-control" required></div></div>
                    <div class="col-md-6"><div class="input-group"><span class="input-group-text">D</span><input type="text" name="questions[${questionCounter}][option_d]" class="form-control" required></div></div>
                </div>

                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label class="small fw-bold me-2 text-success">Đáp án đúng:</label>
                        <select name="questions[${questionCounter}][correct_answer]" class="form-select form-select-sm d-inline-block w-auto fw-bold text-success border-success">
                            <option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>`;
        
        insertHtml(html);
    }

    // 2. Thêm câu Đúng/Sai (Có Select Mức độ)
    function addTrueFalse() {
        const index = document.querySelectorAll('.question-card').length + 1;
        const keys = ['a', 'b', 'c', 'd'];
        let rows = keys.map(k => `
            <tr>
                <td class="ps-3 text-center"><span class="badge bg-secondary rounded-circle">${k.toUpperCase()}</span></td>
                <td><input type="text" name="questions[${questionCounter}][tf_options][${k}]" class="form-control form-control-sm border-0" placeholder="Nhập mệnh đề..."></td>
                <td class="text-center">
                    <div class="form-check form-switch d-flex justify-content-center">
                        <input type="hidden" name="questions[${questionCounter}][tf_correct][${k}]" value="F">
                        <input class="form-check-input" type="checkbox" role="switch" name="questions[${questionCounter}][tf_correct][${k}]" value="T">
                    </div>
                </td>
            </tr>
        `).join('');

        const html = `
        <div class="card mb-3 question-card border-start border-4 border-warning shadow-sm" id="card_${questionCounter}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning text-dark">Câu ${index} (Đúng / Sai)</span>
                        
                        <select name="questions[${questionCounter}][level]" class="form-select form-select-sm border-0 bg-light fw-bold text-secondary" style="width: 140px;">
                            <option value="easy">NB (Nhận biết)</option>
                            <option value="medium" selected>TH (Thông hiểu)</option>
                            <option value="hard">VD (Vận dụng)</option>
                            <option value="very_hard">VDC (Vận dụng cao)</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-sm text-danger" onclick="removeQuestion(this)"><i class="fa-solid fa-trash"></i></button>
                </div>
                
                <input type="hidden" name="questions[${questionCounter}][type]" value="true_false">

                <div class="mb-2">
                    <textarea name="questions[${questionCounter}][content]" class="form-control fw-bold" rows="2" placeholder="Nhập câu dẫn (VD: Các mệnh đề sau đúng hay sai?)..." required></textarea>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-primary">Chủ đề</label>
                        <select name="questions[${questionCounter}][category_id]" class="form-select form-select-sm border-primary bg-primary-subtle text-primary fw-bold">
                            <option value="">-- Chọn chủ đề --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

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
        document.getElementById('questions-wrapper').insertAdjacentHTML('beforeend', html);
        questionCounter++;
    }

    function removeQuestion(btn) {
        if(confirm('Xóa câu hỏi này?')) {
            btn.closest('.question-card').remove();
            // Cập nhật lại số thứ tự (Optional)
            document.querySelectorAll('.question-card').forEach((card, idx) => {
                // Logic cập nhật số câu nếu cần
                const badge = card.querySelector('.badge');
                if(badge.innerText.includes('4 chọn 1')) badge.innerText = `Câu ${idx + 1} (4 chọn 1)`;
                else badge.innerText = `Câu ${idx + 1} (Đúng / Sai)`;
            });
        }
    }

    // Tự động thêm 1 câu mặc định khi vào trang
    document.addEventListener('DOMContentLoaded', () => {
        addOneChoice();
    });
</script>
@endsection