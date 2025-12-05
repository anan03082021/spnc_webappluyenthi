<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt câu hỏi mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f3f4f6; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .btn-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-custom p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="fw-bold m-0 text-primary">Tạo chủ đề thảo luận</h3>
                        <a href="{{ route('forum.index') }}" class="btn-close"></a>
                    </div>

                    <div class="alert alert-info border-0 rounded-3 small">
                        <i class="fa-solid fa-circle-info me-2"></i>
                        <strong>Mẹo:</strong> Đặt tiêu đề ngắn gọn nhưng đầy đủ ý. Mô tả chi tiết vấn đề bạn đang gặp phải để được hỗ trợ nhanh nhất.
                    </div>

                    <form action="{{ route('forum.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">Tiêu đề câu hỏi</label>
                            <input type="text" name="title" class="form-control form-control-lg bg-light" placeholder="Ví dụ: Làm sao để tối ưu hóa truy vấn SQL?" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted">Nội dung chi tiết</label>
                            <textarea name="content" class="form-control bg-light" rows="8" placeholder="Mô tả chi tiết vấn đề..." required></textarea>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-gradient btn-lg w-100 fw-bold shadow">Đăng bài ngay</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>