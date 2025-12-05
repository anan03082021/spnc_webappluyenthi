<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo Đề Thi Mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f0f2f5; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .question-item { position: relative; border-left: 4px solid #4c6ef5; transition: 0.3s; }
        .question-item:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
        .btn-remove { position: absolute; top: 15px; right: 15px; cursor: pointer; color: #dc3545; opacity: 0.5; transition: 0.2s; }
        .btn-remove:hover { opacity: 1; transform: scale(1.1); }
        .form-label { font-weight: 700; color: #4a5568; font-size: 0.9rem; }
    </style>
</head>
<body class="py-4">
    <div class="container" style="max-width: 900px;">
        <form action="{{ route('teacher.exams.store') }}" method="POST" id="createExamForm">
            @csrf
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold m-0"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Soạn thảo đề thi</h3>
                <div class="d-flex gap-2">
                    <a href="{{ route('teacher.exams.index') }}" class="btn btn-light border fw-bold">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary fw-bold px-4"><i class="fa-solid fa-save me-2"></i>Lưu đề thi</button>
                </div>
            </div>

            <div class="card card-custom mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Thông tin chung</h5>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Tên đề thi</label>
                            <input type="text" name="title" class="form-control" placeholder="VD: Kiểm tra 1 tiết chương 2..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Thời gian (phút)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-regular fa-clock"></i></span>
                                <input type="number" name="duration" class="form-control" value="45" min="5">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Độ khó</label>
                            <select name="difficulty" class="form-select">
                                <option value="easy">Cơ bản</option>
                                <option value="medium" selected>Vận dụng (Trung bình)</option>
                                <option value="hard">Nâng cao</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Số lượng câu</label>
                            <input type="text" class="form-control bg-light" value="Tự động tính" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div id="questions-container">
                </div>

            <div class="text-center py-4">
                <button type="button" class="btn btn-outline-primary border-2 border-dashed fw-bold rounded-pill px-4 py-2" onclick="addQuestion()">
                    <i class="fa-solid fa-plus me-1"></i> Thêm câu hỏi mới
                </button>
            </div>
        </form>
    </div>

    <template id="question-template">
        <div class="card card-custom question-item mb-3">
            <div class="btn-remove" onclick="removeQuestion(this)"><i class="fa-solid fa-trash-can fa-lg"></i></div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-primary me-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 25px; height: 25px;">Q</span>
                    <h6 class="fw-bold m-0 text-primary">Nội dung câu hỏi</h6>
                </div>
                
                <textarea class="form-control mb-3 bg-light" name="questions[INDEX][content]" rows="2" placeholder="Nhập câu hỏi..." required></textarea>
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text fw-bold">A</span>
                            <input type="text" name="questions[INDEX][option_a]" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text fw-bold">B</span>
                            <input type="text" name="questions[INDEX][option_b]" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text fw-bold">C</span>
                            <input type="text" name="questions[INDEX][option_c]" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text fw-bold">D</span>
                            <input type="text" name="questions[INDEX][option_d]" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row align-items-center bg-light p-2 rounded">
                    <div class="col-md-4">
                        <label class="form-label mb-0 me-2">Đáp án đúng:</label>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="questions[INDEX][correct_answer]" value="A" id="qINDEX_A" autocomplete="off" checked>
                            <label class="btn btn-outline-success btn-sm" for="qINDEX_A">A</label>

                            <input type="radio" class="btn-check" name="questions[INDEX][correct_answer]" value="B" id="qINDEX_B" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="qINDEX_B">B</label>

                            <input type="radio" class="btn-check" name="questions[INDEX][correct_answer]" value="C" id="qINDEX_C" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="qINDEX_C">C</label>

                            <input type="radio" class="btn-check" name="questions[INDEX][correct_answer]" value="D" id="qINDEX_D" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="qINDEX_D">D</label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <input type="text" name="questions[INDEX][explanation]" class="form-control form-control-sm border-0 bg-white" placeholder="Giải thích đáp án (tùy chọn)...">
                    </div>
                </div>
            </div>
        </div>
    </template>

    <script>
        let questionIndex = 0;
        
        function addQuestion() {
            const template = document.getElementById('question-template').innerHTML;
            const newHtml = template.replace(/INDEX/g, questionIndex);
            
            document.getElementById('questions-container').insertAdjacentHTML('beforeend', newHtml);
            questionIndex++;
        }

        function removeQuestion(element) {
            if(confirm('Xóa câu hỏi này?')) {
                element.closest('.question-item').remove();
            }
        }

        // Thêm mặc định 1 câu khi load
        document.addEventListener('DOMContentLoaded', () => {
            addQuestion();
        });
    </script>
</body>
</html>