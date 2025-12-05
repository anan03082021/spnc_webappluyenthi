<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Tài liệu - Giáo viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8f9fa; color: #4a5568; }
        
        /* Navbar */
        .navbar { background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

        /* Card & Table */
        .table-card { border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden; }
        .table-custom thead { background-color: #f1f5f9; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .table-custom th { padding: 15px 20px; font-weight: 700; border-bottom: none; }
        .table-custom td { padding: 15px 20px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; background: white; }
        
        /* File Icon Styles */
        .file-icon-box {
            width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-right: 15px;
        }
        .bg-soft-danger { background-color: #fee2e2; color: #ef4444; }   /* PDF */
        .bg-soft-primary { background-color: #dbeafe; color: #3b82f6; } /* Word */
        .bg-soft-success { background-color: #d1fae5; color: #10b981; } /* Excel */
        .bg-soft-warning { background-color: #fef3c7; color: #f59e0b; } /* PPT */
        .bg-soft-secondary { background-color: #e2e8f0; color: #64748b; } /* Other */

        .btn-icon { width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; transition: 0.2s; border: none; }
        .btn-icon:hover { background-color: #f1f5f9; transform: scale(1.1); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('teacher.dashboard') }}">
                <i class="fa-solid fa-chalkboard-user text-primary me-2"></i>GV.<span class="text-primary">Portal</span>
            </a>
            <a href="{{ route('teacher.dashboard') }}" class="btn btn-light btn-sm fw-bold border">
                <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </nav>

    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold m-0 text-dark">Kho Tài liệu</h3>
                <p class="text-muted small m-0">Quản lý giáo trình và tài liệu ôn tập cho học sinh.</p>
            </div>
            <a href="{{ route('teacher.documents.create') }}" class="btn btn-primary fw-bold shadow-sm px-4 py-2 rounded-pill">
                <i class="fa-solid fa-cloud-arrow-up me-2"></i>Upload mới
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
                            <th style="width: 40%">Tên Tài Liệu</th>
                            <th>Chủ đề / Chương</th>
                            <th>Ngày đăng</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents as $doc)
                            @php
                                // Logic chọn màu icon dựa trên đuôi file
                                $ext = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
                                $iconClass = match($ext) {
                                    'pdf' => 'bg-soft-danger',
                                    'doc', 'docx' => 'bg-soft-primary',
                                    'xls', 'xlsx' => 'bg-soft-success',
                                    'ppt', 'pptx' => 'bg-soft-warning',
                                    default => 'bg-soft-secondary'
                                };
                                $faIcon = match($ext) {
                                    'pdf' => 'fa-file-pdf',
                                    'doc', 'docx' => 'fa-file-word',
                                    'ppt', 'pptx' => 'fa-file-powerpoint',
                                    'xls', 'xlsx' => 'fa-file-excel',
                                    default => 'fa-file'
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="file-icon-box {{ $iconClass }}">
                                            <i class="fa-regular {{ $faIcon }}"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark text-truncate" style="max-width: 300px;" title="{{ $doc->title }}">
                                                {{ $doc->title }}
                                            </div>
                                            <small class="text-muted text-uppercase">{{ $ext }} File</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border rounded-pill px-3">
                                        {{ $doc->category->name ?? 'Tài liệu chung' }}
                                    </span>
                                </td>
                                <td class="text-muted">
                                    {{ $doc->created_at->format('d/m/Y') }}
                                </td>
                                <td class="text-end">
                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-icon text-primary" title="Xem / Tải về">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                    
                                    <form action="{{ route('teacher.documents.destroy', $doc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa tài liệu này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-icon text-danger" title="Xóa tài liệu">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-box-4064358-3363919.png" width="120" style="opacity: 0.6">
                                    <h5 class="mt-3 text-muted fw-bold">Thư viện trống</h5>
                                    <p class="text-muted small">Bạn chưa tải lên tài liệu nào.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>