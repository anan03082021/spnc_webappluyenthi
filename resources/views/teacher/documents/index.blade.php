@extends('layouts.teacher')

@section('title', 'Kho Tài liệu')

@push('styles')
    <style>
        /* --- DOCUMENT MANAGER STYLE --- */
        body { background-color: #f8fafc; }

        /* Storage Info Bar */
        .storage-bar {
            background: white; border-radius: 12px; padding: 20px; border: 1px solid #e2e8f0;
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;
        }
        .progress-stack { width: 200px; height: 8px; background: #f1f5f9; border-radius: 10px; overflow: hidden; margin-top: 5px; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, #4f46e5, #818cf8); width: 45%; } /* Giả lập */

        /* Document Grid */
        .doc-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;
        }

        .doc-card {
            background: white; border-radius: 16px; padding: 20px; border: 1px solid #e2e8f0;
            position: relative; transition: 0.2s; cursor: pointer; text-decoration: none; display: block;
        }
        .doc-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-color: #6366f1; }

        .doc-icon {
            width: 50px; height: 50px; border-radius: 12px; background: #f8fafc; 
            display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 15px;
        }
        
        .doc-title { font-weight: 700; color: #334155; font-size: 0.95rem; margin-bottom: 5px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 44px; }
        .doc-meta { font-size: 0.75rem; color: #94a3b8; font-weight: 600; display: flex; justify-content: space-between; }

        /* Action Menu (3 dots) */
        .doc-menu-btn {
            position: absolute; top: 15px; right: 15px; color: #cbd5e1; border: none; background: transparent;
        }
        .doc-menu-btn:hover { color: #334155; }

        /* Upload Button */
        .btn-upload {
            background: #4f46e5; color: white; border-radius: 10px; padding: 10px 20px; 
            font-weight: 700; border: none; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);
        }
        .btn-upload:hover { background: #4338ca; color: white; }
    </style>
@endpush

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-down">
        <div>
            <h2 class="fw-bold text-dark m-0">Kho Tài liệu</h2>
            <p class="text-muted m-0 small">Quản lý giáo trình và tài liệu tham khảo.</p>
        </div>
        <button class="btn-upload" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="fa-solid fa-cloud-arrow-up"></i> Tải lên
        </button>
    </div>

    <div class="storage-bar" data-aos="fade-up">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-light p-3 text-primary"><i class="fa-solid fa-hard-drive fs-4"></i></div>
            <div>
                <h6 class="fw-bold m-0 text-dark">Dung lượng sử dụng</h6>
                <div class="progress-stack">
                    <div class="progress-fill" style="width: {{ min(($totalUsage / 500) * 100, 100) }}%"></div>
                </div>
                <div class="small text-muted mt-1">{{ number_format($totalUsage, 2) }} MB / 500 MB</div>
            </div>
        </div>
        <div class="text-end d-none d-md-block">
            <div class="fw-bold text-dark fs-4">{{ $documents->count() }}</div>
            <div class="small text-muted fw-bold text-uppercase">Tài liệu</div>
        </div>
    </div>

    <div class="doc-grid" data-aos="fade-up" data-aos-delay="100">
        @forelse($documents as $doc)
        <div class="position-relative">
            <a href="{{ route('teacher.documents.download', $doc->id) }}" class="doc-card">
                <div class="doc-icon">
                    <i class="fa-regular {{ $doc->icon }}"></i>
                </div>
                <div class="doc-title" title="{{ $doc->title }}">{{ $doc->title }}</div>
                <div class="doc-meta">
                    <span>{{ strtoupper($doc->file_type) }}</span>
                    <span>{{ $doc->file_size }} MB</span>
                </div>
            </a>

            <div class="dropdown" style="position: absolute; top: 15px; right: 15px;">
                <button class="doc-menu-btn" type="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li><a class="dropdown-item small fw-bold" href="{{ route('teacher.documents.download', $doc->id) }}"><i class="fa-solid fa-download me-2 text-primary"></i> Tải xuống</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('teacher.documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Xóa tài liệu này?');">
                            @csrf @method('DELETE')
                            <button class="dropdown-item small fw-bold text-danger"><i class="fa-solid fa-trash me-2"></i> Xóa file</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-folder-4064360-3363921.png" width="120" style="opacity: 0.5;">
            <p class="text-muted mt-3 fw-bold">Chưa có tài liệu nào.</p>
        </div>
        @endforelse
    </div>

    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('teacher.documents.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tải lên tài liệu mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên hiển thị</label>
                        <input type="text" name="title" class="form-control" placeholder="Ví dụ: Đề cương Toán HK1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Chọn file</label>
                        <input type="file" name="file" class="form-control" required>
                        <div class="form-text">Hỗ trợ: PDF, Word, Excel, PowerPoint, ZIP (Max 10MB)</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Tải lên</button>
                </div>
            </form>
        </div>
    </div>

@endsection