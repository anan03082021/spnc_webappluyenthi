@extends(Auth::user()->role == 'teacher' ? 'layouts.teacher' : 'layouts.student')

@section('title', $isInternal ? 'Góc Chuyên Môn' : 'Cộng đồng Luyện thi')

@push('styles')
    <style>
        body { background-color: #f1f5f9; }

        /* HEADER */
        .forum-banner {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            border-radius: 16px; padding: 30px; color: white; margin-bottom: 25px; position: relative; overflow: hidden;
        }
        .forum-banner.internal-mode { background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%); }
        .banner-icon { position: absolute; right: 20px; bottom: -20px; font-size: 6rem; opacity: 0.15; transform: rotate(-15deg); }

        /* POST CARD */
        .post-card {
            background: white; border-radius: 12px; padding: 20px; margin-bottom: 15px;
            border: 1px solid #e2e8f0; transition: 0.2s; position: relative;
        }
        .post-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-color: #6366f1; }
        
        /* Badges */
        .badge-exam { background: #eef2ff; color: #4f46e5; font-size: 0.75rem; font-weight: 700; padding: 5px 10px; border-radius: 6px; display: inline-block; margin-bottom: 8px; }
        .badge-solved { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; font-size: 0.7rem; font-weight: 700; padding: 2px 8px; border-radius: 20px; }
        
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
        
        /* Sidebar Link */
        .nav-link-custom {
            display: flex; justify-content: space-between; align-items: center; padding: 12px 15px;
            color: #475569; text-decoration: none; border-radius: 8px; font-weight: 600; transition: 0.2s;
        }
        .nav-link-custom:hover, .nav-link-custom.active { background: #fff; color: #4f46e5; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    </style>
@endpush

@section('content')

    <div class="forum-banner {{ $isInternal ? 'internal-mode' : '' }}">
        <div class="position-relative" style="z-index: 2;">
            @if($isInternal)
                <h2 class="fw-bold mb-1"><i class="fa-solid fa-briefcase me-2"></i> Góc Chuyên Môn</h2>
                <p class="mb-0 opacity-90 small">Trao đổi nghiệp vụ dành riêng cho Giáo viên.</p>
            @else
                <h2 class="fw-bold mb-1"><i class="fa-solid fa-comments me-2"></i> Thảo luận Đề thi</h2>
                <p class="mb-0 opacity-90 small">Hỏi đáp, giải thích chi tiết các câu hỏi trong đề thi THPT.</p>
            @endif
        </div>
        <i class="fa-solid {{ $isInternal ? 'fa-user-tie' : 'fa-users' }} banner-icon"></i>
    </div>

    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="d-grid gap-2 mb-4">
                <button class="btn btn-success fw-bold py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#createPostModal">
                    <i class="fa-solid fa-pen-to-square me-2"></i> Đăng câu hỏi mới
                </button>
                
                @if(Auth::user()->role == 'teacher')
                    <hr class="my-2">
                    <a href="{{ route('forum.index') }}" class="nav-link-custom {{ !$isInternal ? 'active' : '' }}">
                        <span><i class="fa-solid fa-users me-2"></i> Cộng đồng chung</span>
                    </a>
                    <a href="{{ route('teacher.forum.internal') }}" class="nav-link-custom {{ $isInternal ? 'active' : '' }}">
                        <span><i class="fa-solid fa-lock me-2 text-danger"></i> Phòng Giáo viên</span>
                    </a>
                @endif
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <h6 class="fw-bold text-uppercase text-muted small mb-3">Bộ lọc</h6>
                    <div class="d-flex flex-column gap-1">
                        <a href="#" class="nav-link-custom py-2 text-sm"><i class="fa-solid fa-circle-question me-2 text-warning"></i> Chưa có đáp án</a>
                        <a href="#" class="nav-link-custom py-2 text-sm"><i class="fa-solid fa-check-circle me-2 text-success"></i> Đã giải quyết</a>
                        <a href="#" class="nav-link-custom py-2 text-sm"><i class="fa-solid fa-fire me-2 text-danger"></i> Thảo luận sôi nổi</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            
            <div class="bg-white p-3 rounded-3 border mb-3 shadow-sm d-flex gap-2">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" placeholder="Tìm kiếm câu hỏi, mã đề...">
                </div>
                <select class="form-select w-auto fw-bold text-secondary">
                    <option>Mới nhất</option>
                    <option>Cũ nhất</option>
                </select>
            </div>

            @forelse($posts as $post)
            <div class="post-card">
                <div class="d-flex gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ $post->user->name }}&background=random" class="user-avatar flex-shrink-0">
                    
                    <div class="flex-grow-1">
                        @if($post->related_exam_id)
                            <a href="{{ route('forum.show', $post->id) }}" class="text-decoration-none">
                                <span class="badge-exam">
                                    <i class="fa-solid fa-link"></i> {{ $post->exam->title ?? 'Đề thi đã xóa' }} 
                                    @if($post->related_question_no)
                                        - Câu {{ $post->related_question_no }}
                                    @endif
                                </span>
                            </a>
                        @endif

                        <h5 class="fw-bold mb-1">
                            <a href="{{ route('forum.show', $post->id) }}" class="text-decoration-none text-dark stretched-link">
                                {{ $post->title }}
                            </a>
                        </h5>
                        
                        <p class="text-secondary small mb-2 text-truncate" style="max-width: 600px;">
                            {{ Str::limit(strip_tags($post->content), 120) }}
                        </p>

                        <div class="d-flex align-items-center gap-3 small text-muted">
                            <span class="fw-bold text-dark">{{ $post->user->name }}</span>
                            <span>&bull; {{ $post->created_at->diffForHumans() }}</span>
                            
                            @if($post->is_solved)
                                <span class="badge-solved"><i class="fa-solid fa-check"></i> Đã có đáp án</span>
                            @endif

                            @if($post->is_pinned)
                                <span class="text-danger fw-bold"><i class="fa-solid fa-thumbtack"></i> Đã ghim</span>
                            @endif
                        </div>
                    </div>

                    <div class="text-end ps-3 d-flex flex-column justify-content-center border-start">
                        <div class="fs-5 fw-bold text-primary">{{ $post->replies_count ?? 0 }}</div>
                        <div class="small text-muted" style="font-size: 0.7rem;">Trả lời</div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <img src="https://cdni.iconscout.com/illustration/premium/thumb/no-data-found-8867280-7265556.png" width="150" class="opacity-50 mb-3">
                <h6 class="fw-bold text-muted">Chưa có thảo luận nào.</h6>
                <p class="small text-muted">Hãy là người đầu tiên đặt câu hỏi!</p>
            </div>
            @endforelse
            
            <div class="mt-4">
                {{ $posts->links() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="createPostModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('forum.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Tạo thảo luận mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if(Auth::user()->role == 'teacher')
                    <div class="mb-3 p-3 bg-fce border rounded">
                        <label class="fw-bold mb-2 small text-uppercase text-muted">Phạm vi đăng bài:</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="scope" id="scopePublic" value="public" {{ !$isInternal ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary fw-bold" for="scopePublic"><i class="fa-solid fa-users"></i> Cộng đồng Học sinh</label>

                            <input type="radio" class="btn-check" name="scope" id="scopeTeacher" value="teacher" {{ $isInternal ? 'checked' : '' }}>
                            <label class="btn btn-outline-danger fw-bold" for="scopeTeacher"><i class="fa-solid fa-lock"></i> Góc Chuyên môn (GV)</label>
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="fw-bold">Tiêu đề tóm tắt</label>
                        <input type="text" name="title" class="form-control" placeholder="Ví dụ: Hỏi về cách giải câu 10 đề thi thử số 1..." required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="fw-bold">Đoạn mã (Nếu có)</label>
                        <textarea name="code_snippet" class="form-control font-monospace bg-dark text-white small" rows="3" placeholder="// Paste code Pascal/C++ vào đây..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Nội dung chi tiết</label>
                        <textarea name="content" class="form-control" rows="5" placeholder="Mô tả chi tiết vấn đề của bạn..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Đăng bài ngay</button>
                </div>
            </form>
        </div>
    </div>

@endsection