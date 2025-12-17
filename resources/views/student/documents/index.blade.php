@extends('layouts.student')

@section('title', 'Thư viện Tài liệu')

@push('styles')
    <style>
        /* --- GENERAL --- */
        body { background-color: #f1f5f9; font-family: 'Inter', sans-serif; color: #334155; }

        /* --- HEADER & TOOLBAR --- */
        .page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; }
        .header-title { font-weight: 800; color: #0f172a; font-size: 1.6rem; margin: 0; letter-spacing: -0.5px; }
        .header-subtitle { color: #64748b; font-size: 0.9rem; margin-top: 6px; }

        .toolbar-card {
            background: white; border-radius: 12px; padding: 16px 20px;
            border: 1px solid #e2e8f0; box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            display: flex; gap: 15px; align-items: center; flex-wrap: wrap; margin-bottom: 30px;
        }
        
        .search-group { position: relative; flex-grow: 1; }
        .search-input {
            width: 100%; padding: 10px 15px 10px 42px; border-radius: 8px; border: 1px solid #e2e8f0;
            background-color: #f8fafc; font-size: 0.9rem; transition: 0.2s;
        }
        .search-input:focus { background-color: white; border-color: #4f46e5; outline: none; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        .search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; }

        .filter-select {
            padding: 10px 35px 10px 15px; border-radius: 8px; border: 1px solid #e2e8f0;
            background-color: white; font-weight: 600; color: #475569; cursor: pointer; min-width: 200px;
        }
        .filter-select:focus { border-color: #4f46e5; outline: none; }

        /* --- DOCUMENT CARD --- */
        .doc-card {
            background: white; border-radius: 16px; border: 1px solid #e2e8f0;
            padding: 20px; position: relative; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%; display: flex; flex-direction: column;
        }
        .doc-card:hover {
            transform: translateY(-5px); box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.1); border-color: #cbd5e1;
        }

        .icon-wrapper {
            width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem; margin-bottom: 16px; align-self: flex-start;
        }
        /* Màu nền Icon */
        .bg-pdf { background-color: #fef2f2; color: #ef4444; }
        .bg-word { background-color: #eff6ff; color: #3b82f6; }
        .bg-excel { background-color: #f0fdf4; color: #22c55e; }
        .bg-ppt { background-color: #fff7ed; color: #f97316; }
        .bg-zip { background-color: #f1f5f9; color: #64748b; }
        .bg-default { background-color: #f8fafc; color: #475569; }

        .doc-category {
            font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
            color: #64748b; margin-bottom: 6px;
        }
        .doc-title { 
            font-weight: 700; color: #1e293b; font-size: 1rem; margin-bottom: 8px; line-height: 1.4; 
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; min-height: 2.8em;
        }
        .doc-meta { 
            font-size: 0.8rem; color: #94a3b8; margin-top: auto; padding-top: 15px; 
            display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #e2e8f0;
        }

        .btn-download-sm {
            padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;
            background-color: #f1f5f9; color: #475569; text-decoration: none; transition: 0.2s;
        }
        .btn-download-sm:hover { background-color: #4f46e5; color: white; }

        /* Empty State */
        .empty-state { text-align: center; padding: 80px 20px; }
        .empty-icon { font-size: 4rem; color: #cbd5e1; margin-bottom: 20px; }
    </style>
@endpush

@section('content')

<div class="container-fluid py-4">
    
    <div class="page-header" data-aos="fade-down">
        <div>
            <h1 class="header-title">Thư viện Tài liệu</h1>
            <p class="header-subtitle">Tổng hợp giáo trình, đề cương và tài liệu ôn thi chất lượng cao.</p>
        </div>
    </div>

    <div class="toolbar-card" data-aos="fade-up">
        <form action="{{ route('student.documents.index') }}" method="GET" class="d-flex w-100 gap-3 align-items-center flex-wrap" id="filterForm">
            
            <div class="search-group">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="search" class="search-input" 
                       placeholder="Tìm kiếm tài liệu..." 
                       value="{{ request('search') }}">
            </div>

            <div class="filter-group">
                <select name="category" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="all">📂 Tất cả danh mục</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if(request('search') || (request('category') && request('category') != 'all'))
                <a href="{{ route('student.documents.index') }}" class="btn btn-light border fw-bold text-danger">
                    <i class="fa-solid fa-xmark me-1"></i> Xóa lọc
                </a>
            @endif
        </form>
    </div>

    <div class="row g-4">
        @forelse($documents as $doc)
            @php
                // Xử lý icon và màu sắc
                $ext = strtolower($doc->file_type);
                $style = match($ext) {
                    'pdf' => ['icon' => 'fa-solid fa-file-pdf', 'bg' => 'bg-pdf'],
                    'doc', 'docx' => ['icon' => 'fa-solid fa-file-word', 'bg' => 'bg-word'],
                    'xls', 'xlsx' => ['icon' => 'fa-solid fa-file-excel', 'bg' => 'bg-excel'],
                    'ppt', 'pptx' => ['icon' => 'fa-solid fa-file-powerpoint', 'bg' => 'bg-ppt'],
                    'zip', 'rar' => ['icon' => 'fa-solid fa-file-zipper', 'bg' => 'bg-zip'],
                    default => ['icon' => 'fa-regular fa-file-lines', 'bg' => 'bg-default']
                };
            @endphp

            <div class="col-xl-3 col-lg-4 col-md-6" data-aos="fade-up">
                <div class="doc-card">
                    <div class="icon-wrapper {{ $style['bg'] }}">
                        <i class="{{ $style['icon'] }}"></i>
                    </div>
                    
                    <div class="doc-category">
                        {{ $doc->category->name ?? 'Chưa phân loại' }}
                    </div>
                    
                    <h6 class="doc-title" title="{{ $doc->title }}">
                        {{ $doc->title }}
                    </h6>

                    <div class="doc-meta">
                        <div class="d-flex flex-column">
                            <span class="small">{{ $doc->created_at->format('d/m/Y') }}</span>
                            <span class="small fw-bold">{{ $doc->file_size }} MB</span>
                        </div>
                        <a href="{{ route('student.documents.download', $doc->id) }}" class="btn-download-sm">
                            <i class="fa-solid fa-download me-1"></i> Tải về
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="fa-regular fa-folder-open empty-icon"></i>
                    <h5 class="fw-bold text-dark">Chưa có tài liệu nào</h5>
                    <p class="text-muted">Thử thay đổi bộ lọc hoặc quay lại sau nhé.</p>
                </div>
            </div>
        @endforelse
    </div>

    @if($documents->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $documents->withQueryString()->links() }}
        </div>
    @endif

</div>
@endsection