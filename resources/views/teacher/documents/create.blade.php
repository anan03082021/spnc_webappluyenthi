<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Upload Tài liệu Mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f0f2f5; }
        
        .upload-card {
            border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        
        /* Custom File Input */
        .file-upload-wrapper {
            position: relative;
            height: 150px;
            border: 2px dashed #cbd5e0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8fafc;
            transition: 0.3s;
            cursor: pointer;
        }
        .file-upload-wrapper:hover {
            border-color: #4c6ef5;
            background-color: #edf2ff;
        }
        .file-upload-input {
            position: absolute; width: 100%; height: 100%; opacity: 0; cursor: pointer;
        }
        .upload-icon { font-size: 40px; color: #a0aec0; margin-bottom: 10px; }
        .upload-text { color: #718096; font-weight: 600; }
    </style>
</head>
<body class="py-5">
    <div class="container" style="max-width: 600px;">
        <div class="card upload-card">
            <div class="card-header bg-white border-0 pt-4 px-4 text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-cloud-arrow-up fs-3"></i>
                </div>
                <h4 class="fw-bold text-dark">Tải lên tài liệu</h4>
                <p class="text-muted small">Chia sẻ kiến thức với học sinh của bạn</p>
            </div>

            <div class="card-body p-4">
                @if($errors->any())
                    <div class="alert alert-danger small rounded-3 mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('teacher.documents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">Tiêu đề tài liệu</label>
                        <input type="text" name="title" class="form-control form-control-lg bg-light fs-6" placeholder="VD: Đề cương ôn tập HK1..." required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary small">Thuộc chủ đề / Chương</label>
                        <select name="category_id" class="form-select bg-light">
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @if($categories->isEmpty())
                            <div class="form-text text-warning"><i class="fa-solid fa-circle-exclamation me-1"></i> Chưa có danh mục nào. Hãy tạo danh mục trong Database trước.</div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary small">Chọn file (PDF, Word, PPT)</label>
                        <div class="file-upload-wrapper">
                            <input type="file" name="file" class="file-upload-input" onchange="updateFileName(this)" required>
                            <div class="text-center">
                                <i class="fa-solid fa-file-arrow-up upload-icon" id="uploadIcon"></i>
                                <div class="upload-text" id="fileName">Kéo thả hoặc Click để chọn file</div>
                                <small class="text-muted d-block mt-1">Max size: 10MB</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                            <i class="fa-solid fa-upload me-2"></i> Bắt đầu Upload
                        </button>
                        <a href="{{ route('teacher.documents.index') }}" class="btn btn-light fw-bold text-muted">Hủy bỏ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateFileName(input) {
            const fileNameDisplay = document.getElementById('fileName');
            const iconDisplay = document.getElementById('uploadIcon');
            
            if (input.files && input.files[0]) {
                fileNameDisplay.textContent = input.files[0].name;
                fileNameDisplay.classList.add('text-primary');
                iconDisplay.classList.remove('fa-file-arrow-up');
                iconDisplay.classList.add('fa-check-circle', 'text-success');
            } else {
                fileNameDisplay.textContent = 'Kéo thả hoặc Click để chọn file';
                fileNameDisplay.classList.remove('text-primary');
                iconDisplay.classList.add('fa-file-arrow-up');
                iconDisplay.classList.remove('fa-check-circle', 'text-success');
            }
        }
    </script>
</body>
</html>