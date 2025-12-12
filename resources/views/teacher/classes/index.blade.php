@extends('layouts.teacher')

@section('title', 'Quản lý Lớp học')

@push('styles')
    <style>
        /* --- CLASS MANAGEMENT STYLE --- */
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; color: #334155; }

        /* Header */
        .page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 32px; }
        .header-title h1 { font-size: 1.8rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.5px; }
        .header-title p { font-size: 0.95rem; color: #64748b; margin-top: 6px; }

        /* Button Create */
        .btn-create {
            background-color: #4f46e5; color: white; padding: 12px 24px; border-radius: 10px;
            font-weight: 600; text-decoration: none; border: none; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
            transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; cursor: pointer;
        }
        .btn-create:hover { background-color: #4338ca; transform: translateY(-2px); color: white; }

        /* Main Card */
        .card-box { background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01); overflow: hidden; }

        /* Toolbar */
        .toolbar { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; gap: 15px; flex-wrap: wrap; }
        .search-group { position: relative; max-width: 350px; width: 100%; }
        .search-input { width: 100%; padding: 10px 15px 10px 40px; border-radius: 8px; border: 1px solid #e2e8f0; background: #f8fafc; transition: 0.2s; }
        .search-input:focus { background: white; border-color: #6366f1; outline: none; }
        .search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; }

        /* Table */
        .table-pro th { background: #f8fafc; color: #64748b; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; padding: 16px 24px; border-bottom: 1px solid #e2e8f0; }
        .table-pro td { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155; font-size: 0.95rem; }
        .table-pro tr:last-child td { border-bottom: none; }
        .table-pro tr:hover td { background-color: #fcfcfd; }

        /* Elements */
        .class-meta { display: flex; align-items: center; gap: 15px; }
        .class-icon { width: 48px; height: 48px; border-radius: 12px; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; }
        .class-name { font-weight: 700; color: #0f172a; font-size: 1rem; display: block; text-decoration: none; }
        .class-name:hover { color: #4f46e5; text-decoration: underline; }
        .class-code { font-family: monospace; background: #f1f5f9; padding: 2px 8px; border-radius: 6px; color: #64748b; font-size: 0.8rem; font-weight: 700; border: 1px solid #e2e8f0; }

        .stat-row span { margin-right: 15px; font-size: 0.85rem; color: #64748b; font-weight: 500; }
        .pill-pending { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; animation: pulse-red 2s infinite; }
        @keyframes pulse-red { 0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); } 70% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); } 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); } }

        /* Action Dropdown */
        .btn-dots { border: none; background: transparent; color: #94a3b8; font-size: 1.2rem; transition: 0.2s; }
        .btn-dots:hover { color: #4f46e5; }
    </style>
@endpush

@section('content')

    <div class="page-header" data-aos="fade-down">
        <div class="header-title">
            <h1>Quản lý Lớp học</h1>
            <p>Theo dõi sĩ số, duyệt thành viên và tổ chức lớp học.</p>
        </div>
        <div>
            <button type="button" class="btn-create" data-bs-toggle="modal" data-bs-target="#createClassModal">
                <i class="fa-solid fa-plus"></i> Tạo lớp mới
            </button>
        </div>
    </div>

    <div class="card-box" data-aos="fade-up" data-aos-delay="100">
        
        <div class="toolbar">
            <form action="{{ route('teacher.classes.index') }}" method="GET" class="search-group">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="search" class="search-input" placeholder="Tìm kiếm tên lớp, mã lớp..." value="{{ request('search') }}">
            </form>
        </div>

        <div class="table-responsive">
            <table class="table-pro w-100 mb-0">
                <thead>
                    <tr>
                        <th width="40%">Thông tin lớp</th>
                        <th width="25%">Sĩ số</th>
                        <th width="25%">Trạng thái</th>
                        <th width="10%" class="text-end">Tác vụ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $class)
                    <tr>
                        <td>
                            <div class="class-meta">
                                <div class="class-icon">
                                    <i class="fa-solid fa-chalkboard-user"></i>
                                </div>
                                <div>
                                    <a href="{{ route('teacher.classes.show', $class->id) }}" class="class-name">{{ $class->name }}</a>
                                    <div class="mt-1">
                                        <span class="class-code">{{ $class->code }}</span>
                                        <span class="text-muted small ms-2">{{ $class->academic_year }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="stat-row">
                                <span><i class="fa-solid fa-users text-primary me-1"></i> <b>{{ $class->students_count }}</b> chính thức</span>
                            </div>
                        </td>

                        <td>
                            @if(isset($class->pending_students_count) && $class->pending_students_count > 0)
                                <div class="pill-pending">
                                    <i class="fa-solid fa-user-clock"></i> {{ $class->pending_students_count }} chờ duyệt
                                </div>
                            @else
                                <span class="text-success fw-bold small"><i class="fa-solid fa-circle-check me-1"></i> Hoạt động tốt</span>
                            @endif
                        </td>

                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn-dots" type="button" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item py-2" href="{{ route('teacher.classes.show', $class->id) }}"><i class="fa-solid fa-eye me-2 text-primary"></i> Xem chi tiết</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('teacher.classes.destroy', $class->id) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn giải tán lớp này?');">
                                            @csrf @method('DELETE')
                                            <button class="dropdown-item py-2 text-danger"><i class="fa-solid fa-trash me-2"></i> Giải tán lớp</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-classroom-4064360-3363921.png" width="100" style="opacity: 0.6; margin-bottom: 20px;">
                            <h5 class="fw-bold text-dark">Chưa có lớp học nào</h5>
                            <p class="text-muted mb-4">Hãy tạo lớp học đầu tiên để bắt đầu quản lý.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($classes->hasPages())
            <div class="px-4 py-3 border-top bg-light">
                {{ $classes->links() }}
            </div>
        @endif
    </div>

    <div class="modal fade" id="createClassModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('teacher.classes.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tạo lớp học mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên lớp học <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Ví dụ: Lớp 12A1 - Toán Cô Mai" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Năm học <span class="text-danger">*</span></label>
                        <select name="academic_year" class="form-select">
                            <option value="2024 - 2025">2024 - 2025</option>
                            <option value="2025 - 2026">2025 - 2026</option>
                        </select>
                    </div>
                    <div class="alert alert-info small">
                        <i class="fa-solid fa-info-circle me-1"></i> Mã lớp sẽ được hệ thống tạo tự động.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Tạo lớp</button>
                </div>
            </form>
        </div>
    </div>

@endsection