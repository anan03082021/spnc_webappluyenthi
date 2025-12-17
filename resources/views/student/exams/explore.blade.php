@extends('layouts.student')

@section('title', 'Khám phá Đề thi')

@push('styles')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        /* --- EXPLORE STYLES --- */
        body { background-color: #f3f4f6; }

        /* 1. Search Hero */
        .search-hero {
            background-image: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 0 0 30px 30px; padding: 60px 0 80px; color: white;
            text-align: center; margin-top: -30px; margin-bottom: 40px; position: relative;
            box-shadow: 0 10px 30px -10px rgba(5, 150, 105, 0.5); overflow: hidden;
        }
        .search-hero::before { content: ''; position: absolute; width: 300px; height: 300px; top: -100px; left: -100px; border-radius: 50%; background: rgba(255,255,255,0.1); backdrop-filter: blur(5px); }
        
        .search-box-wrapper { position: relative; max-width: 700px; margin: 0 auto; transform: translateY(20px); z-index: 10; }
        .search-input { width: 100%; padding: 20px 70px 20px 30px; border-radius: 50px; border: none; font-size: 1.1rem; box-shadow: 0 15px 30px -5px rgba(0,0,0,0.1); outline: none; }
        .btn-search { position: absolute; right: 10px; top: 10px; bottom: 10px; width: 50px; border-radius: 50%; border: none; background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); color: white; font-size: 1.2rem; cursor: pointer; transition: 0.3s; }
        .btn-search:hover { transform: scale(1.1); }

        .filter-container { display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; margin-top: 30px; }
        .filter-pill { background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); color: white; padding: 8px 20px; border-radius: 30px; text-decoration: none; font-weight: 700; transition: 0.3s; }
        .filter-pill:hover, .filter-pill.active { background: white; color: var(--primary-color); transform: translateY(-3px); }

        /* 2. Exam Cards (Popup Style) */
        .exam-card-wrapper { position: relative; height: 100%; transition: 0.3s; }
        .exam-card-wrapper:hover { transform: translateY(-8px); }

        .exam-card-pop {
            background: white; border-radius: 25px; overflow: hidden; border: none; height: 100%;
            display: block; text-decoration: none; color: inherit; box-shadow: 0 10px 20px -5px rgba(0,0,0,0.05);
        }
        
        .pop-thumb { height: 150px; width: 100%; position: relative; display: flex; align-items: center; justify-content: center; }
        .pop-icon { font-size: 4rem; color: white; opacity: 0.9; filter: drop-shadow(0 5px 5px rgba(0,0,0,0.2)); transition: 0.5s; }
        .exam-card-wrapper:hover .pop-icon { transform: scale(1.2) rotate(-10deg); }

        .pop-badge { position: absolute; top: 15px; left: 15px; background: rgba(255,255,255,0.95); color: #333; font-weight: 800; font-size: 0.7rem; padding: 5px 12px; border-radius: 20px; box-shadow: 0 5px 10px rgba(0,0,0,0.1); text-transform: uppercase; }
        
        /* Badge Đã làm (Mới thêm) */
        .badge-done {
            position: absolute; top: 45px; left: 15px; /* Nằm dưới badge độ khó */
            background: #10b981; color: white;
            padding: 4px 10px; border-radius: 10px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .pop-body { padding: 25px; }
        .pop-title { font-weight: 800; font-size: 1.1rem; color: #1e293b; margin-bottom: 8px; line-height: 1.4; height: 3rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
        .pop-meta { display: flex; gap: 15px; font-size: 0.85rem; color: #94a3b8; font-weight: 600; margin-bottom: 20px; }
        
        .pop-btn { width: 100%; padding: 12px; border-radius: 15px; border: none; font-weight: 800; background: #f1f5f9; color: #64748b; transition: 0.3s; }
        .exam-card-wrapper:hover .pop-btn { background: #667eea; color: white; box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4); }

        /* Style riêng cho nút Làm lại */
        .pop-btn.btn-redo { background: #d1fae5 !important; color: #059669 !important; }
        .exam-card-wrapper:hover .pop-btn.btn-redo { background: #059669 !important; color: white !important; box-shadow: 0 5px 15px rgba(5, 150, 105, 0.4); }

        /* Gradients */
        .bg-grad-1 { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); }
        .bg-grad-2 { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); }
        .bg-grad-3 { background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); }
        .bg-grad-4 { background: linear-gradient(135deg, #fccb90 0%, #d57eeb 100%); }

        /* NÚT BOOKMARK */
        .btn-bookmark {
            position: absolute; top: 15px; right: 15px; z-index: 10;
            width: 35px; height: 35px; border-radius: 50%; border: none;
            background: rgba(255,255,255,0.9); box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex; align-items: center; justify-content: center; transition: 0.2s; cursor: pointer;
        }
        .btn-bookmark:hover { transform: scale(1.1); background: white; }
        .bm-active { color: #f59e0b; }
        .bm-inactive { color: #cbd5e0; }
        
        .stats-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: white; padding: 15px 25px; border-radius: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); }
    </style>
@endpush

@section('content')
    
    <div class="search-hero" data-aos="fade-down">
        <div class="container">
            <h2 class="fw-bold mb-2">Kho tàng kiến thức</h2>
            <p class="opacity-75 mb-4">Tìm kiếm hàng trăm đề thi để nâng cao trình độ.</p>
            <form action="{{ route('student.exams.explore') }}" method="GET" class="search-box-wrapper">
                <input type="text" name="search" class="search-input" placeholder="Nhập tên đề thi..." value="{{ request('search') }}">
                <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
            <div class="filter-container">
                <a href="{{ route('student.exams.explore') }}" class="filter-pill {{ !request('category') ? 'active' : '' }}">Tất cả</a>
                @foreach($categories as $cat)
                    <a href="{{ route('student.exams.explore', ['category' => $cat->id]) }}" class="filter-pill {{ request('category') == $cat->id ? 'active' : '' }}">{{ $cat->name }}</a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="container">
        <div class="stats-bar" data-aos="fade-up">
            <h5 class="fw-bold m-0 text-dark"><i class="fa-solid fa-layer-group me-2 text-primary"></i> Danh sách đề thi</h5>
            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">{{ $exams->total() }} kết quả</span>
        </div>

        <div class="row g-4 pb-5">
            @forelse($exams as $index => $exam)
                @php
                    $bgClass = 'bg-grad-' . (($index % 4) + 1);
                    $iconClass = match($index % 4) { 0 => 'fa-file-code', 1 => 'fa-database', 2 => 'fa-microchip', 3 => 'fa-globe', default => 'fa-file' };
                    
                    // Check bookmark
                    $isSaved = Auth::user()->bookmarks ? Auth::user()->bookmarks->contains($exam->id) : false;

                    // Check đã làm (Dựa trên mảng ID từ Controller)
                    $isAttempted = in_array($exam->id, $attemptedExamIds ?? []);
                @endphp

                <div class="col-md-3 col-sm-6" data-aos="fade-up" data-aos-delay="{{ ($index % 4) * 100 }}">
                    <div class="exam-card-wrapper">
                        
                        <form action="{{ route('student.exams.bookmark', $exam->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-bookmark {{ $isSaved ? 'bm-active' : 'bm-inactive' }}" title="{{ $isSaved ? 'Bỏ lưu' : 'Lưu đề này' }}">
                                <i class="fa-solid fa-bookmark"></i>
                            </button>
                        </form>

                        <a href="{{ route('student.exams.show', $exam->id) }}" class="exam-card-pop">
                            <div class="pop-thumb {{ $bgClass }}">
                                <span class="pop-badge">{{ $exam->difficulty }}</span>
                                
                                {{-- HIỂN THỊ BADGE ĐÃ LÀM --}}
                                @if($isAttempted)
                                    <span class="badge-done"><i class="fa-solid fa-check"></i> Đã làm</span>
                                @endif

                                <i class="fa-solid {{ $iconClass }} pop-icon"></i>
                            </div>
                            <div class="pop-body">
                                <h6 class="pop-title" title="{{ $exam->title }}">{{ $exam->title }}</h6>
                                <div class="pop-meta">
                                    <span><i class="fa-regular fa-clock"></i> {{ $exam->duration }}p</span>
                                    <span><i class="fa-solid fa-list-ol"></i> {{ $exam->total_questions }} câu</span>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <img src="https://ui-avatars.com/api/?name={{ $exam->creator->name ?? 'T' }}&background=random" class="rounded-circle me-2" width="25" height="25">
                                    <small class="text-muted fw-bold" style="font-size: 0.75rem;">{{ $exam->creator->name ?? 'Giáo viên' }}</small>
                                </div>
                                
                                {{-- ĐỔI NÚT DỰA TRÊN TRẠNG THÁI --}}
                                @if($isAttempted)
                                    <button class="pop-btn btn-redo"><i class="fa-solid fa-rotate-right me-1"></i> Làm lại</button>
                                @else
                                    <button class="pop-btn">Làm bài ngay</button>
                                @endif
                            </div>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/no-search-results-2511443-2103512.png" width="200" style="opacity: 0.8">
                    <h5 class="mt-4 text-muted fw-bold">Không tìm thấy đề thi phù hợp.</h5>
                    <a href="{{ route('student.exams.explore') }}" class="btn btn-link fw-bold">Xóa bộ lọc</a>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-2 mb-5">
            {{ $exams->appends(request()->query())->links() }}
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script> AOS.init({ duration: 800, once: true, offset: 50 }); </script>
@endpush