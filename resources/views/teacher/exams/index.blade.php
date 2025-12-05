<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Đề thi - Giáo viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8f9fa; color: #4a5568; }
        
        /* Navbar */
        .navbar { background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

        /* Table Styles */
        .table-card { border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden; }
        .table-custom thead { background-color: #f1f5f9; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .table-custom th { padding: 15px 20px; font-weight: 700; border-bottom: none; }
        .table-custom td { padding: 15px 20px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; background: white; }
        .table-custom tr:last-child td { border-bottom: none; }
        
        /* Badges */
        .badge-soft-success { background-color: #d1fae5; color: #065f46; }
        .badge-soft-warning { background-color: #fef3c7; color: #92400e; }
        .badge-soft-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-soft-primary { background-color: #dbeafe; color: #1e40af; }
        
        /* Action Buttons */
        .btn-icon { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: 0.2s; border: none; }
        .btn-icon:hover { transform: translateY(-2px); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('teacher.dashboard') }}">
                <i class="fa-solid fa-chalkboard-user text-primary me-2"></i>GV.<span class="text-primary">Portal</span>
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('teacher.dashboard') }}" class="btn btn-light btn-sm fw-bold border">
                    <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold m-0 text-dark">Danh sách Đề thi</h3>
                <p class="text-muted small m-0">Quản lý các bài kiểm tra và ngân hàng câu hỏi của bạn.</p>
            </div>
            <a href="{{ route('teacher.exams.create') }}" class="btn btn-primary fw-bold shadow-sm px-4 py-2 rounded-pill">
                <i class="fa-solid fa-plus me-2"></i>Tạo đề mới
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
                <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="card table-card">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th style="width: 30%">Tên Đề Thi</th>
                            <th>Thông số</th>
                            <th>Độ khó</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $exam)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $exam->title }}</div>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-light text-secondary border">
                                        <i class="fa-regular fa-clock me-1"></i> {{ $exam->duration }}p
                                    </span>
                                    <span class="badge bg-light text-secondary border">
                                        <i class="fa-solid fa-list-ol me-1"></i> {{ $exam->total_questions }} câu
                                    </span>
                                </div>
                            </td>
                            <td>
                                @php
                                    $diffClass = match($exam->difficulty) {
                                        'easy' => 'badge-soft-success',
                                        'medium' => 'badge-soft-warning',
                                        'hard' => 'badge-soft-danger',
                                        default => 'badge-soft-primary'
                                    };
                                    $diffLabel = match($exam->difficulty) {
                                        'easy' => 'Cơ bản',
                                        'medium' => 'Vận dụng',
                                        'hard' => 'Nâng cao',
                                        default => 'Trung bình'
                                    };
                                @endphp
                                <span class="badge {{ $diffClass }} px-3 py-2 rounded-pill">
                                    {{ $diffLabel }}
                                </span>
                            </td>
                            <td>
                                @if($exam->is_published)
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded">
                                        <i class="fa-solid fa-check me-1"></i> Đã duyệt
                                    </span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1 rounded">
                                        <i class="fa-solid fa-hourglass-half me-1"></i> Chờ duyệt
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $exam->created_at->format('d/m/Y') }}
                            </td>
                            <td class="text-end">
    <a href="{{ route('teacher.exams.edit', $exam->id) }}" class="btn btn-icon btn-light text-primary" title="Chỉnh sửa">
        <i class="fa-solid fa-pen"></i>
    </a>
    
    <form action="{{ route('teacher.exams.destroy', $exam->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa đề thi này sẽ xóa toàn bộ câu hỏi và kết quả thi của học sinh. Bạn có chắc chắn không?');">
        @csrf 
        @method('DELETE') <button class="btn btn-icon btn-light text-danger" title="Xóa đề thi">
            <i class="fa-solid fa-trash"></i>
        </button>
    </form>
</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <img src="https://cdni.iconscout.com/illustration/premium/thumb/folder-is-empty-4064360-3363921.png" width="120" style="opacity: 0.6">
                                <h5 class="mt-3 text-muted fw-bold">Chưa có đề thi nào</h5>
                                <p class="text-muted small">Hãy bắt đầu bằng việc tạo một đề thi mới cho học sinh.</p>
                                <a href="{{ route('teacher.exams.create') }}" class="btn btn-sm btn-outline-primary mt-2">Tạo ngay</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-4 d-flex justify-content-center">
            </div>
    </div>
</body>
</html>