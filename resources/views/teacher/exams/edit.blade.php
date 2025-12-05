<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa Đề thi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light py-4">
    <div class="container" style="max-width: 900px;">
        <form action="{{ route('teacher.exams.update', $exam->id) }}" method="POST">
            @csrf
            @method('PUT') <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold m-0 text-primary">Chỉnh sửa đề thi</h3>
                <div class="d-flex gap-2">
                    <a href="{{ route('teacher.exams.index') }}" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-primary fw-bold">Lưu thay đổi</button>
                </div>
            </div>

            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Tên đề thi</label>
                            <input type="text" name="title" class="form-control" value="{{ $exam->title }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Thời gian (phút)</label>
                            <input type="number" name="duration" class="form-control" value="{{ $exam->duration }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Độ khó</label>
                            <select name="difficulty" class="form-select">
                                <option value="easy" {{ $exam->difficulty == 'easy' ? 'selected' : '' }}>Cơ bản</option>
                                <option value="medium" {{ $exam->difficulty == 'medium' ? 'selected' : '' }}>Vận dụng</option>
                                <option value="hard" {{ $exam->difficulty == 'hard' ? 'selected' : '' }}>Nâng cao</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div id="questions-container">
                @foreach($exam->questions as $index => $q)
                <div class="card mb-3 question-item border-primary border-start border-4">
                    <div class="card-body bg-white">
                        <div class="d-flex justify-content-between mb-2">
                            <h6 class="fw-bold text-primary">Câu hỏi {{ $index + 1 }}</h6>
                            <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="this.closest('.question-item').remove()"><i class="fa-solid fa-trash"></i></button>
                        </div>
                        
                        <textarea name="questions[{{ $index }}][content]" class="form-control mb-2 bg-light" rows="2" required>{{ $q->content }}</textarea>
                        
                        <div class="row g-2 mb-2">
                            <div class="col-md-6"><input type="text" name="questions[{{ $index }}][option_a]" class="form-control form-control-sm" value="{{ $q->option_a }}" placeholder="A" required></div>
                            <div class="col-md-6"><input type="text" name="questions[{{ $index }}][option_b]" class="form-control form-control-sm" value="{{ $q->option_b }}" placeholder="B" required></div>
                            <div class="col-md-6"><input type="text" name="questions[{{ $index }}][option_c]" class="form-control form-control-sm" value="{{ $q->option_c }}" placeholder="C" required></div>
                            <div class="col-md-6"><input type="text" name="questions[{{ $index }}][option_d]" class="form-control form-control-sm" value="{{ $q->option_d }}" placeholder="D" required></div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <label class="small fw-bold me-2">Đáp án đúng:</label>
                                <select name="questions[{{ $index }}][correct_answer]" class="form-select form-select-sm d-inline-block w-auto">
                                    <option value="A" {{ $q->correct_answer == 'A' ? 'selected' : '' }}>A</option>
                                    <option value="B" {{ $q->correct_answer == 'B' ? 'selected' : '' }}>B</option>
                                    <option value="C" {{ $q->correct_answer == 'C' ? 'selected' : '' }}>C</option>
                                    <option value="D" {{ $q->correct_answer == 'D' ? 'selected' : '' }}>D</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="questions[{{ $index }}][explanation]" class="form-control form-control-sm" value="{{ $q->explanation }}" placeholder="Giải thích...">
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <button type="button" class="btn btn-outline-primary w-100 py-2 dashed-border" onclick="addQuestion()">+ Thêm câu hỏi mới</button>
        </form>
    </div>

    <script>
        // Bắt đầu index từ số lượng câu hiện có để tránh trùng name
        let questionIndex = {{ count($exam->questions) }}; 

        function addQuestion() {
            questionIndex++;
            const html = `
            <div class="card mb-3 question-item border-secondary border-start border-4">
                <div class="card-body bg-white">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="fw-bold text-secondary">Câu hỏi mới</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="this.closest('.question-item').remove()"><i class="fa-solid fa-trash"></i></button>
                    </div>
                    <textarea name="questions[${questionIndex}][content]" class="form-control mb-2" rows="2" placeholder="Nội dung..." required></textarea>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6"><input type="text" name="questions[${questionIndex}][option_a]" class="form-control form-control-sm" placeholder="A" required></div>
                        <div class="col-md-6"><input type="text" name="questions[${questionIndex}][option_b]" class="form-control form-control-sm" placeholder="B" required></div>
                        <div class="col-md-6"><input type="text" name="questions[${questionIndex}][option_c]" class="form-control form-control-sm" placeholder="C" required></div>
                        <div class="col-md-6"><input type="text" name="questions[${questionIndex}][option_d]" class="form-control form-control-sm" placeholder="D" required></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <select name="questions[${questionIndex}][correct_answer]" class="form-select form-select-sm">
                                <option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>`;
            document.getElementById('questions-container').insertAdjacentHTML('beforeend', html);
        }
    </script>
</body>
</html>