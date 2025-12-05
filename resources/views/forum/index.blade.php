<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Diễn đàn thảo luận - SPNC Edutech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f3f4f6; color: #4a5568; }
        .navbar { background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        
        /* Sidebar Menu */
        .sidebar-card { background: white; border-radius: 15px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden; }
        .menu-link { display: block; padding: 12px 20px; color: #4a5568; text-decoration: none; font-weight: 600; transition: 0.3s; border-left: 3px solid transparent; }
        .menu-link:hover, .menu-link.active { background-color: #f7fafc; color: #667eea; border-left-color: #667eea; }

        /* Post Item */
        .post-card {
            background: white; border-radius: 15px; border: none; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 20px;
            transition: transform 0.2s;
        }
        .post-card:hover { transform: translateY(-2px); box-shadow: 0 10px 15px rgba(0,0,0,0.1); }
        .avatar-box { width: 45px; height: 45px; object-fit: cover; border-radius: 50%; }
        .avatar-placeholder { width: 45px; height: 45px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #718096; }
        
        .btn-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; }
        .btn-gradient:hover { color: white; opacity: 0.9; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg sticky-top mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/dashboard') }}">
                <i class="fa-solid fa-graduation-cap text-primary me-2"></i>SPNC<span class="text-primary">Edu</span>
            </a>
            <div class="d-flex align-items-center">
                <a href="{{ url('/dashboard') }}" class="btn btn-light btn-sm rounded-pill fw-bold text-muted">
                    <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <div class="sidebar-card mb-3">
                    <div class="p-3 border-bottom">
                        <a href="{{ route('forum.create') }}" class="btn btn-gradient w-100 rounded-pill fw-bold py-2 shadow-sm">
                            <i class="fa-solid fa-plus me-1"></i> Đặt câu hỏi mới
                        </a>
                    </div>
                    <div class="py-2">
                        <a href="#" class="menu-link active"><i class="fa-solid fa-fire me-2"></i>Mới nhất</a>
                        <a href="#" class="menu-link"><i class="fa-regular fa-comments me-2"></i>Sôi nổi nhất</a>
                        <a href="#" class="menu-link"><i class="fa-regular fa-circle-check me-2"></i>Đã giải quyết</a>
                    </div>
                </div>

                <div class="sidebar-card p-4 text-center">
                    <h6 class="text-muted fw-bold text-uppercase small">Cộng đồng</h6>
                    <h3 class="fw-bold text-primary">{{ $posts->total() }}</h3>
                    <p class="small text-muted mb-0">Chủ đề thảo luận</p>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="mb-4">
                    <div class="input-group shadow-sm rounded-pill overflow-hidden bg-white">
                        <span class="input-group-text bg-white border-0 ps-4"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" class="form-control border-0 py-3" placeholder="Tìm kiếm câu hỏi hoặc chủ đề...">
                        <button class="btn btn-primary px-4 fw-bold">Tìm</button>
                    </div>
                </div>

                @forelse($posts as $post)
                <div class="card post-card p-3">
                    <div class="d-flex">
                        <div class="me-3">
                            @if($post->user->avatar)
                                <img src="{{ asset('storage/' . $post->user->avatar) }}" class="avatar-box border">
                            @else
                                <div class="avatar-placeholder">{{ substr($post->user->name, 0, 1) }}</div>
                            @endif
                        </div>
                        
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-dark">
                                    {{ $post->user->name }}
                                    @if($post->user->role == 'teacher')
                                        <i class="fa-solid fa-circle-check text-primary ms-1" title="Giáo viên"></i>
                                    @endif
                                </span>
                                <small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
                            </div>
                            
                            <h5 class="fw-bold mb-2">
                                <a href="{{ route('forum.show', $post->id) }}" class="text-decoration-none text-dark stretched-link">
                                    {{ $post->title }}
                                </a>
                            </h5>
                            
                            <p class="text-muted small mb-2 text-truncate" style="max-width: 90%;">
                                {{ Str::limit($post->content, 150) }}
                            </p>

                            <div class="d-flex align-items-center text-muted small mt-3">
                                <span class="me-4"><i class="fa-regular fa-comment-dots me-1"></i> {{ $post->replies_count }} trả lời</span>
                                <span><i class="fa-regular fa-eye me-1"></i> 0 lượt xem</span> </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/searching-data-2978368-2476744.png" width="200" style="opacity: 0.7;">
                    <h5 class="mt-3 text-muted">Chưa có chủ đề nào. Hãy là người đầu tiên!</h5>
                </div>
                @endforelse

                <div class="mt-4">
                    {{ $posts->links() }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>