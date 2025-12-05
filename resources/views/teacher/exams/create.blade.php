<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Soạn thảo Đề thi - GV Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f0f2f5; color: #4a5568; padding-bottom: 100px; }
        
        /* Layout */
        .page-header { background: white; padding: 15px 0; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 100; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        
        /* Question Card */
        .question-card {
            background: white; border: none; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            position: relative; transition: 0.3s; border-left: 4px solid #cbd5e0;
        }
        .question-card:hover { box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); border-left-color: #4c6ef5; }
        .question-index {
            width: 28px; height: 28px; background: #edf2f7; color: #718096; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.8rem;
        }

        /* Sidebar Config */
        .config-card { background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); position: sticky; top: 100px; }
        
        /* Inputs */
        .form-control, .form-select { border-radius: 8px; border: 1px solid #e2e8f0; }
        .form-control:focus, .form-select:focus { border-color: #4c6ef5; box-shadow: 0 0 0 3px rgba(76, 110, 245, 0.1); }
        .input-group-text { background: #f8fafc; border-color: #e2e8f0; color: #64748b; font-weight: 700; }
        
        /* Action Bar Bottom */
        .action-bar {
            position: fixed; bottom: 0; left: 0; right: 0; background: white; padding: 15px;
            border-top: 1px solid #e2e8f0; display: flex; justify-content: center; gap: 15px; z-index: 99;
        }
    </style>
</head>
<body>

    <form action="{{ route('teacher.exams.store') }}" method="POST" id="examForm">
        @csrf
        
        <div class="page-header">
            <div class="container d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="{{ route('teacher.exams.index') }}" class="btn btn-light btn-sm rounded-circle me-3 border shadow-sm">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <div>
                        <h5 class="fw-bold m-0 text-dark">Soạn thảo đề thi mới</h5>
                        <small class="text-muted">Nhập thông tin và danh sách câu hỏi</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-light text-dark border p-2">
                        <i class="fa-solid fa-list-ol me-1"></i> <span id="totalQuestionsBadge">0</span> câu
                    </span>
                    <button type="submit" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm">
                        <i class="fa-solid fa-save me-2"></i> Hoàn tất & Lưu
                    </button>
                </div>
            </div>
        </div>

        <div class="container py-4">
            <div class="row">
                <div class="col-lg-8">
                    <div id="questions-wrapper">
                        </div>

                    <div class="text-center mt-4 p-5 border-2 border-dashed rounded-4" style="border-color: #cbd5e0; background-color: #f8fafc;">
                        <img src="https://cdni.iconscout.com/illustration/premium/thumb/add-files-4569666-3806981.png" width="80" style="opacity: 0.6">
                        <p class="text-muted small mt-2">Tiếp tục thêm câu hỏi trắc nghiệm</p>
                        <button type="button" class="btn btn-outline-primary fw-bold px-4 rounded-pill" onclick="addQuestion()">
                            <i class="fa-solid fa-plus-circle me-2"></i> Thêm câu hỏi
                        </button>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="config-card p-4">
                        <h6 class="fw-bold text-dark border-bottom pb-3 mb-3">
                            <i class="fa-solid fa-sliders me-2 text-primary"></i> Cấu hình chung
                        </h6>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Tên đề thi <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control fw-bold" placeholder="VD: Kiểm tra 1 tiết Tin học 11..." required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-secondary">Thời gian</label>
                                <div class="input-group">
                                    <input type="number" name="duration" class="form-control text-center fw-bold" value="45" min="5">
                                    <span class="input-group-text small">phút</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-secondary">Độ khó</label>
                                <select name="difficulty" class="form-select fw-bold">
                                    <option value="easy">🟢 Cơ bản</option>
                                    <option value="medium" selected>🟡 Vận dụng</option>
                                    <option value="hard">🔴 Nâng cao</option>
                                </select>
                            </div>
                        </div>

                        <div class="alert alert-info small border-0 bg-info-subtle text-info-emphasis">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            <strong>Lưu ý:</strong> Hãy chọn đúng <b>Chủ đề</b> cho từng câu hỏi để hệ thống gợi ý tài liệu ôn tập chính xác cho học sinh.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <template id="question-template">
        <div class="question-card p-4 mb-3" id="question_INDEX">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <div class="question-index me-3">#<span class="index-text">1</span></div>
                    <span class="badge bg-light text-secondary border">Trắc nghiệm</span>
                </div>
                <button type="button" class="btn btn-sm btn-light text-danger border-0 rounded-circle" onclick="removeQuestion(this)" title="Xóa câu này">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label small fw-bold text-secondary">Nội dung câu hỏi</label>
                    <textarea name="questions[INDEX][content]" class="form-control bg-light" rows="2" placeholder="Nhập nội dung câu hỏi..." required></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-secondary text-primary">Thuộc chủ đề (Quan trọng)</label>
                    <select name="questions[INDEX][category_id]" class="form-select border-primary bg-primary-subtle text-primary fw-bold" required>
                        <option value="">-- Chọn chủ đề --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text text-primary">A</span>
                        <input type="text" name="questions[INDEX][option_a]" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text text-primary">B</span>
                        <input type="text" name="questions[INDEX][option_b]" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text text-primary">C</span>
                        <input type="text" name="questions[INDEX][option_c]" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text text-primary">D</span>
                        <input type="text" name="questions[INDEX][option_d]" class="form-control" required>
                    </div>
                </div>

                <div class="col-12 pt-2 border-top mt-2">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <label class="small fw-bold me-2 text-success">Đáp án đúng:</label>
                            <div class="btn-group w-100" role="group">
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
                            <input type="text" name="questions[INDEX][explanation]" class="form-control form-control-sm border-0" placeholder="Nhập lời giải thích chi tiết (Tùy chọn)...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <script>
        let questionCounter = 0;

        function addQuestion() {
            const container = document.getElementById('questions-wrapper');
            const template = document.getElementById('question-template').innerHTML;
            
            // Thay thế placeholder INDEX bằng số đếm thực tế để name không trùng
            const currentHtml = template.replace(/INDEX/g, questionCounter);
            
            // Chèn vào cuối danh sách
            container.insertAdjacentHTML('beforeend', currentHtml);
            
            // Cập nhật số thứ tự hiển thị (1, 2, 3...)
            updateIndexes();
            
            questionCounter++;
            updateTotalBadge();
        }

        function removeQuestion(btn) {
            if(confirm('Bạn có chắc muốn xóa câu hỏi này?')) {
                btn.closest('.question-card').remove();
                updateIndexes();
                updateTotalBadge();
            }
        }

        function updateIndexes() {
            const cards = document.querySelectorAll('.question-card');
            cards.forEach((card, index) => {
                card.querySelector('.index-text').innerText = index + 1;
            });
        }

        function updateTotalBadge() {
            const count = document.querySelectorAll('.question-card').length;
            document.getElementById('totalQuestionsBadge').innerText = count;
        }

        // Tự động thêm 1 câu khi vào trang
        document.addEventListener('DOMContentLoaded', () => {
            addQuestion();
        });
    </script>

</body>
</html>