@extends('layouts.teacher')

@section('title', 'Tạo đề thi nhanh')

@push('styles')
    <style>
        .form-switch .form-check-input { width: 3em !important; height: 1.5em !important; cursor: pointer; }
        .form-switch .form-check-input:checked { background-color: #10b981; border-color: #10b981; }
        #rawInput { font-family: 'Consolas', monospace; font-size: 0.9rem; line-height: 1.5; background-color: #f8fafc; }
        .question-content-preview { white-space: pre-wrap; font-family: sans-serif; }
    </style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark m-0">Tạo đề thi nhanh</h3>
        <a href="{{ route('teacher.exams.index') }}" class="btn btn-secondary fw-bold"><i class="fa-solid fa-arrow-left me-2"></i>Quay lại</a>
    </div>

    <form action="{{ route('teacher.exams.store_quick') }}" method="POST" id="quickForm" enctype="multipart/form-data">
        @csrf
        <div class="row h-100">
            
            <div class="col-md-5 d-flex flex-column">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-header bg-primary text-white fw-bold">
                        <i class="fa-solid fa-paste me-2"></i>1. Dán nội dung text
                    </div>
                    <div class="card-body p-0 d-flex flex-column">
                        <div class="alert alert-info m-0 rounded-0 border-0 border-bottom small">
                            <strong><i class="fa-solid fa-lightbulb me-1"></i> Quy tắc nhập liệu:</strong><br>
                            1. Bắt đầu câu bằng <b>[NB], [TH], [VD], [VDC]</b>.<br>
                            2. Đặt dấu <b>*</b> trước đáp án đúng.<br>
                            3. Đáp án phải bắt đầu bằng A., B., C., D. (hoặc a, b, c, d)
                        </div>
                        <textarea id="rawInput" class="form-control border-0 flex-grow-1 p-3" 
                            style="resize: none; min-height: 600px;" 
                            placeholder="[NB] Câu 1: Cho hình vẽ bên dưới...
A. 1
B. 2
*C. 3
D. 4

[TH] Câu 2: Các mệnh đề sau đúng hay sai?
*a) Excel là phần cứng
b) Excel là bảng tính
*c) Word là trình duyệt
d) CPU là bộ nhớ"></textarea>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fa-solid fa-list-check me-2"></i>2. Kết quả & Thêm ảnh</span>
                        <span id="qCountBadge" class="badge bg-white text-success fw-bold fs-6">0 câu</span>
                    </div>
                    <div class="card-body bg-light" style="overflow-y: auto; max-height: 80vh;">
                        
                        <div class="bg-white p-4 rounded-3 mb-4 border shadow-sm">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Thiết lập chung</h6>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Tên đề thi <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control fw-bold" required placeholder="VD: Đề thi thử Tin học 12...">
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold small text-muted">Thời gian làm bài</label>
                                    <div class="input-group">
                                        <input type="number" name="duration" class="form-control fw-bold text-center" value="45" required>
                                        <span class="input-group-text bg-light">phút</span>
                                    </div>
                                </div>
                                </div>
                            <button type="button" class="btn btn-light border w-100 fw-bold" onclick="parseText()">
                                <i class="fa-solid fa-rotate me-2"></i> Phân tích lại
                            </button>
                        </div>

                        <div id="previewArea">
                            <div class="text-center text-muted py-5">
                                <i class="fa-regular fa-image fs-1 mb-3 opacity-50"></i>
                                <p>Dán câu hỏi bên trái để xem kết quả tại đây.</p>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer bg-white p-3">
                        <button type="submit" class="btn btn-success w-100 py-3 fw-bold text-uppercase shadow-sm">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Lưu đề thi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const categories = <?php echo json_encode($categories ?? []); ?>;
    let typingTimer;

    document.getElementById('rawInput').addEventListener('input', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(parseText, 500); 
    });

    function parseText() {
        const text = document.getElementById('rawInput').value;
        const previewArea = document.getElementById('previewArea');
        const qCountBadge = document.getElementById('qCountBadge');
        
        if (!text.trim()) {
            previewArea.innerHTML = '<div class="text-center text-muted py-5"><i class="fa-regular fa-file-lines fs-1 mb-3 opacity-50"></i><p>Chưa có dữ liệu...</p></div>';
            if(qCountBadge) qCountBadge.innerText = '0 câu';
            return;
        }

        previewArea.innerHTML = ''; 

        // Regex tìm [NB].. và dấu *
        const regex = /(?:^|\n)\[(NB|TH|VD|VDC)\]\s*(?:Câu\s+\d+[:.]|Bài\s+\d+[:.]|\d+\.|)(.*?)(?:\n(\*?)(?:A\.|a\))\s*(.*?))(?:\n(\*?)(?:B\.|b\))\s*(.*?))(?:\n(\*?)(?:C\.|c\))\s*(.*?))(?:\n(\*?)(?:D\.|d\))\s*(.*?))(?=\n\[|\n$|$)/gmis;
        
        let match;
        let index = 0;

        while ((match = regex.exec(text)) !== null) {
            const levelCode = match[1].toUpperCase();
            const content = match[2].trim();
            
            // Xác định dấu sao
            const starA = match[3];
            const starB = match[5];
            const starC = match[7];
            const starD = match[9];

            const rawOptions = {
                a: match[4].trim(), b: match[6].trim(), c: match[8].trim(), d: match[10].trim()
            };

            // Phân loại
            let type = 'one_choice';
            let tfValues = {a: 'F', b: 'F', c: 'F', d: 'F'};
            let correctOne = '';

            const starCount = (starA ? 1 : 0) + (starB ? 1 : 0) + (starC ? 1 : 0) + (starD ? 1 : 0);

            if (starCount > 1) {
                type = 'true_false';
                tfValues.a = starA ? 'T' : 'F';
                tfValues.b = starB ? 'T' : 'F';
                tfValues.c = starC ? 'T' : 'F';
                tfValues.d = starD ? 'T' : 'F';
            } else if (starCount === 1) {
                type = 'one_choice';
                if (starA) correctOne = 'A';
                if (starB) correctOne = 'B';
                if (starC) correctOne = 'C';
                if (starD) correctOne = 'D';
            } else {
                correctOne = 'A'; // Mặc định nếu quên đánh sao
            }

            // Map Level
            let levelVal = 'medium';
            if (levelCode === 'NB') levelVal = 'easy';
            if (levelCode === 'TH') levelVal = 'medium';
            if (levelCode === 'VD') levelVal = 'hard';
            if (levelCode === 'VDC') levelVal = 'very_hard';

            // Tạo Select Chủ đề
            let categoryOptions = '<option value="">-- Chủ đề --</option>';
            categories.forEach(cat => {
                categoryOptions += `<option value="${cat.id}">${cat.name}</option>`;
            });

            // HTML Đáp án
            let answersHtml = '';
            if (type === 'one_choice') {
                const optionsHtml = ['a', 'b', 'c', 'd'].map(k => `
                    <div class="col-6">
                        <div class="p-2 rounded border ${correctOne === k.toUpperCase() ? 'bg-success text-white' : 'bg-white'} small d-flex align-items-center">
                            <span class="fw-bold me-2 text-uppercase badge bg-light text-dark border">${k}</span> 
                            <span class="text-truncate flex-grow-1">${rawOptions[k]}</span>
                            <input type="hidden" name="questions[${index}][option_${k}]" value="${rawOptions[k]}">
                        </div>
                    </div>
                `).join('');
                answersHtml = `<div class="row g-2 mt-2">${optionsHtml}</div><input type="hidden" name="questions[${index}][correct_answer]" value="${correctOne}">`;
            } else {
                const rowsHtml = ['a', 'b', 'c', 'd'].map(k => `
                    <tr>
                        <td class="ps-3 text-center align-middle"><span class="badge bg-secondary rounded-circle">${k.toUpperCase()}</span></td>
                        <td class="align-middle"><input type="text" name="questions[${index}][option_${k}]" value="${rawOptions[k]}" class="form-control form-control-sm border-0 bg-transparent fw-bold" readonly></td>
                        <td class="text-center align-middle">
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input type="hidden" name="questions[${index}][tf_correct][${k}]" value="F">
                                <input class="form-check-input shadow-none" type="checkbox" role="switch" name="questions[${index}][tf_correct][${k}]" value="T" ${tfValues[k] === 'T' ? 'checked' : ''} onclick="this.previousElementSibling.value = this.checked ? 'T' : 'F'">
                            </div>
                        </td>
                    </tr>
                `).join('');
                answersHtml = `<div class="table-responsive border rounded mt-2"><table class="table table-sm table-borderless mb-0 small bg-white"><tbody>${rowsHtml}</tbody></table></div><input type="hidden" name="questions[${index}][correct_answer]" value="${Object.values(tfValues).join(',')}">`;
            }

            // HTML Card
            const html = `
                <div class="card mb-3 border-start border-4 ${type === 'one_choice' ? 'border-primary' : 'border-warning'} shadow-sm">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="badge ${type === 'one_choice' ? 'bg-primary' : 'bg-warning text-dark'} me-2">Câu ${index + 1}</span>
                                <span class="badge bg-info text-white me-1 border">${levelCode}</span>
                            </div>
                            <div class="d-flex gap-2">
                                <select name="questions[${index}][level]" class="form-select form-select-sm border-0 bg-light fw-bold" style="width: 110px;">
                                    <option value="easy" ${levelVal === 'easy' ? 'selected' : ''}>NB</option>
                                    <option value="medium" ${levelVal === 'medium' ? 'selected' : ''}>TH</option>
                                    <option value="hard" ${levelVal === 'hard' ? 'selected' : ''}>VD</option>
                                    <option value="very_hard" ${levelVal === 'very_hard' ? 'selected' : ''}>VDC</option>
                                </select>
                                <select name="questions[${index}][category_id]" class="form-select form-select-sm border-0 bg-light fw-bold" style="width: 140px;">
                                    ${categoryOptions}
                                </select>
                            </div>
                        </div>

                        <div class="mb-2 text-end">
                            <label class="btn btn-sm btn-outline-primary border-0 fw-bold" for="img_${index}"><i class="fa-regular fa-image me-1"></i> Thêm hình</label>
                            <input type="file" id="img_${index}" name="questions[${index}][image]" class="d-none" accept="image/*" onchange="previewImage(this, 'preview_${index}')">
                        </div>
                        <div id="preview_${index}" class="mb-2 text-center" style="display:none;">
                            <img src="" style="max-height: 150px; border-radius: 8px; border: 1px solid #ddd;">
                            <button type="button" class="btn btn-sm text-danger d-block mx-auto mt-1" onclick="removeImage('${index}')"><i class="fa-solid fa-trash"></i> Xóa</button>
                        </div>

                        <input type="hidden" name="questions[${index}][type]" value="${type}">
                        <textarea name="questions[${index}][content]" class="d-none">${content}</textarea>
                        <div class="p-2 bg-light rounded text-dark mb-2 border-start border-3 question-content-preview">${content}</div>
                        
                        ${answersHtml}
                    </div>
                </div>
            `;
            
            previewArea.insertAdjacentHTML('beforeend', html);
            index++;
        }
        
        if(qCountBadge) qCountBadge.innerText = index + ' câu';
        
        if (index === 0 && text.trim().length > 10) {
             previewArea.innerHTML = `
                <div class="alert alert-warning small border-0 shadow-sm">
                    <div class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Lỗi định dạng!</div>
                    Quy tắc mới:<br> 
                    1. Bắt buộc có <b>[NB], [TH], [VD]</b> ở đầu câu.<br> 
                    2. Đặt dấu <b>*</b> trước đáp án đúng.<br>
                    Ví dụ: <b>[NB] Câu 1: ... \n A. Sai \n *B. Đúng</b>
                </div>`;
        }
    }

    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var c = document.getElementById(previewId);
                c.style.display = 'block';
                c.querySelector('img').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    function removeImage(index) {
        document.getElementById('img_' + index).value = "";
        document.getElementById('preview_' + index).style.display = 'none';
    }
</script>
@endpush