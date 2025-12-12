@extends('layouts.teacher')

@section('title', 'Quản lý Đề thi')

@push('styles')
    <style>
        /* ... (Giữ nguyên phần CSS của bạn không thay đổi) ... */
        /* --- PREMIUM ADMIN TABLE STYLE --- */
        body { background-color: #f1f5f9; }
        .header-wrapper { display: flex; justify-content: space-between; align-items: end; margin-bottom: 25px; }
        .header-title { font-weight: 800; color: #0f172a; font-size: 1.6rem; letter-spacing: -0.5px; margin: 0; }
        .header-subtitle { color: #64748b; font-size: 0.9rem; margin-top: 5px; font-weight: 500; }
        .btn-base { padding: 10px 20px; border-radius: 10px; font-weight: 700; font-size: 0.9rem; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; border: 1px solid transparent; text-decoration: none !important; }
        .btn-primary-pro { background-color: #4f46e5; color: white; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); }
        .btn-primary-pro:hover { background-color: #4338ca; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3); color: white; }
        .btn-white-pro { background-color: white; color: #334155; border-color: #e2e8f0; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
        .btn-white-pro:hover { background-color: #f8fafc; border-color: #cbd5e0; color: #0f172a; }
        .card-container { background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01), 0 2px 4px -1px rgba(0, 0, 0, 0.01); overflow: hidden; }
        .table-toolbar { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; background: #fff; display: flex; justify-content: space-between; align-items: center; gap: 15px; flex-wrap: wrap; }
        .search-group { position: relative; max-width: 400px; width: 100%; }
        .search-input { width: 100%; padding: 10px 15px 10px 42px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 0.9rem; background-color: #f8fafc; transition: 0.2s; }
        .search-input:focus { background-color: white; border-color: #6366f1; outline: none; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }
        .search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1rem; }
        .filter-select { padding: 10px 35px 10px 15px; border-radius: 8px; border: 1px solid #e2e8f0; background-color: white; font-size: 0.9rem; color: #475569; font-weight: 600; cursor: pointer; appearance: none; }
        .filter-select:focus { border-color: #6366f1; outline: none; }
        .table-pro { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-pro th { background-color: #f8fafc; color: #64748b; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 16px 24px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        .table-pro td { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155; font-size: 0.95rem; }
        .table-pro tr:last-child td { border-bottom: none; }
        .table-pro tbody tr { transition: background-color 0.2s; }
        .table-pro tbody tr:hover { background-color: #f8fafc; }
        .exam-meta { display: flex; align-items: center; gap: 12px; }
        .exam-icon-box { width: 42px; height: 42px; border-radius: 10px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
        .exam-info div:first-child { font-weight: 700; color: #0f172a; margin-bottom: 2px; }
        .exam-info div:last-child { font-size: 0.8rem; color: #64748b; font-family: monospace; }
        .tag-info { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 6px; background: #f1f5f9; color: #475569; font-size: 0.8rem; font-weight: 600; margin-right: 6px; border: 1px solid #e2e8f0; }
        .tag-info i { margin-right: 6px; color: #94a3b8; font-size: 0.85rem; }
        .badge-status { padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; display: inline-block; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .btn-icon { width: 34px; height: 34px; border-radius: 8px; border: none; background: transparent; color: #94a3b8; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
        .btn-icon:hover, .dropdown.show .btn-icon { background: #f1f5f9; color: #334155; }
        .dropdown-menu-custom { border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border-radius: 12px; padding: 6px; }
        .dropdown-item-custom { border-radius: 8px; padding: 8px 12px; font-size: 0.9rem; font-weight: 600; color: #475569; }
        .dropdown-item-custom:hover { background: #f8fafc; color: #4f46e5; }
        .dropdown-item-danger:hover { background: #fef2f2; color: #ef4444; }
        .empty-wrapper { text-align: center; padding: 80px 20px; }
    </style>
@endpush

@section('content')

    <div class="header-wrapper" data-aos="fade-down">
        <div>
            <h1 class="header-title">Ngân hàng đề thi</h1>
            <p class="header-subtitle">Quản lý và tổ chức các bài kiểm tra cho học sinh.</p>
        </div>
        <div class="d-flex gap-3">
            <a href="{{ route('teacher.exams.create_quick') }}" class="btn-base btn-white-pro">
                <i class="fa-solid fa-bolt text-warning"></i> Nhập nhanh
            </a>
            <a href="{{ route('teacher.exams.create') }}" class="btn-base btn-primary-pro">
                <i class="fa-solid fa-plus"></i> Tạo đề mới
            </a>
        </div>
    </div>

    <div class="card-container" data-aos="fade-up" data-aos-delay="100">
        
        <div class="table-toolbar">
            <form action="{{ route('teacher.exams.index') }}" method="GET" class="search-group">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="search" class="search-input" 
                       placeholder="Tìm kiếm đề thi theo tên..." 
                       value="{{ request('search') }}">
            </form>

            <div class="d-flex align-items-center gap-2">
                <form action="{{ route('teacher.exams.index') }}" method="GET" id="filterForm">
                    @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                    
                    <select name="status" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="all">Tất cả trạng thái</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Bản nháp</option>
                    </select>
                </form>

                @if(request('search') || request('status'))
                    <a href="{{ route('teacher.exams.index') }}" class="btn btn-sm btn-light text-danger fw-bold border" style="height: 40px; display: flex; align-items: center; border-radius: 8px; text-decoration: none;">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-pro">
                <thead>
                    <tr>
                        {{-- Đã điều chỉnh lại width vì bỏ bớt 1 cột --}}
                        <th width="50%">Đề thi</th>
                        <th width="25%">Thông số</th>
                        {{-- ĐÃ XÓA CỘT ĐỘ KHÓ --}}
                        <th width="15%">Trạng thái</th>
                        <th width="10%" class="text-end">Tác vụ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                    <tr>
                        <td>
                            <div class="exam-meta">
                                <div class="exam-icon-box">
                                    <i class="fa-regular fa-file-lines"></i>
                                </div>
                                <div class="exam-info">
                                    <div>{{ $exam->title }}</div>
                                    <div>#ID: {{ $exam->id }} &bull; {{ $exam->created_at->format('d/m/Y') }}</div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex flex-column gap-1">
                                <span class="tag-info"><i class="fa-regular fa-clock"></i> {{ $exam->duration }} phút</span>
                                <span class="tag-info"><i class="fa-solid fa-list-ol"></i> {{ $exam->questions->count() }} câu</span>
                            </div>
                        </td>

                        {{-- ĐÃ XÓA CỘT ĐỘ KHÓ --}}

                        <td>
                            @if($exam->is_published)
                                <span class="badge-status badge-success"><i class="fa-solid fa-circle-check me-1"></i> Đã duyệt</span>
                            @else
                                <span class="badge-status badge-warning"><i class="fa-solid fa-circle-pause me-1"></i> Nháp/Chờ duyệt</span>
                            @endif
                        </td>

                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn-icon" type="button" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
                                    <li>
                                        <a class="dropdown-item dropdown-item-custom" href="{{ route('teacher.exams.edit', $exam->id) }}">
                                            <i class="fa-solid fa-pen-to-square me-2 text-primary"></i> Chỉnh sửa
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item dropdown-item-custom" href="{{ route('teacher.exams.show', $exam->id) }}">
                                            <i class="fa-solid fa-eye me-2 text-info"></i> Xem chi tiết
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="{{ route('teacher.exams.destroy', $exam->id) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn xóa?');">
                                            @csrf @method('DELETE')
                                            <button class="dropdown-item dropdown-item-custom dropdown-item-danger w-100 text-start">
                                                <i class="fa-solid fa-trash-can me-2 text-danger"></i> Xóa đề thi
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4"> {{-- Đã sửa colspan từ 5 xuống 4 vì mất 1 cột --}}
                            <div class="empty-wrapper">
                                <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-folder-4064360-3363921.png" width="100" style="opacity: 0.6; margin-bottom: 20px;">
                                <h5 class="fw-bold text-dark">Chưa có dữ liệu</h5>
                                <p class="text-muted">Bạn chưa tạo đề thi nào. Hãy bắt đầu ngay!</p>
                                <a href="{{ route('teacher.exams.create_quick') }}" class="btn btn-primary-pro btn-base">
                                    <i class="fa-solid fa-plus"></i> Tạo đề ngay
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($exams->hasPages())
            <div class="px-4 py-3 border-top bg-light">
                {{ $exams->links() }}
            </div>
        @endif
    </div>

@endsection