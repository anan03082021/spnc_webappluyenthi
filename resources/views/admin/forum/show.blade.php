<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết Thảo luận - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f1f5f9; }
        .sidebar { width: 260px; height: 100vh; position: fixed; top: 0; left: 0; background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); color: #94a3b8; padding: 20px; z-index: 1000; }
        .main-content { margin-left: 260px; padding: 30px; }
        .nav-link { color: #94a3b8; padding: 12px 15px; border-radius: 10px; margin-bottom: 5px; display: flex; align-items: center; font-weight: 600; text-decoration: none; }
        .nav-link:hover, .nav-link.active { background-color: rgba(255,255,255,0.1); color: white; }
        .sidebar-brand { color: white; font-size: 1.5rem; font-weight: 800; display: flex; align-items: center; margin-bottom: 40px; text-decoration: none; }

        /* Post Content */
        .post-card { background: white; border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; border: none; margin-bottom: 30px; }
        .comment-item { border-bottom: 1px solid #f1f5f9; padding: 20px; transition: 0.2s; }
        .comment-item:last-child { border-bottom: none; }
        .comment-item:hover { background-color: #f8fafc; }
        
        .avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0; }
        .btn-delete-cmt { color: #cbd5e0; transition: 0.2s; border: none; background: none; }
        .btn-delete-cmt:hover { color: #ef4444; transform: scale(1.1); }
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="#" class="sidebar-brand"><i class="fa-solid fa-shield-halved text-primary me-2"></i> SPNC Admin</a>
        <nav class="nav flex-column">
            <a href="{{ route('admin.dashboard') }}" class="nav-link"><i class="fa-solid fa-gauge-high me-3"></i> Dashboard</a>
            <p class="text-uppercase small fw-bold mt-4 mb-2 ps-3" style="opacity: 0.5; font-size: 0.75rem;">Quản lý</p>
            <a href="{{ route('admin.users.index') }}" class="nav-link"><i class="fa-solid fa-users me-3"></i> Thành viên</a>
            <a href="{{ route('admin.exams.index') }}" class="nav-link"><i class="fa-solid fa-file-circle-check me-3"></i> Ngân hàng đề</a>
            <a href="{{ route('admin.forum.index') }}" class="nav-link active"><i class="fa-solid fa-comments me-3"></i> Diễn đàn</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark m-0">Chi tiết thảo luận</h3>
            <a href="{{ route('admin.forum.index') }}" class="btn btn-outline-secondary rounded-pill px-3 fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4"><i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}</div>
        @endif

        <div class="card post-card p-4 mb-4">
            <div class="d-flex border-bottom pb-3 mb-3">
                <img src="{{ $post->user->avatar ? asset('storage/'.$post->user->avatar) : 'https://ui-avatars.com/api/?name='.$post->user->name }}" class="avatar me-3">
                <div class="flex-grow-1">
                    <h4 class="fw-bold text-primary mb-1">{{ $post->title }}</h4>
                    <div class="text-muted small">
                        Đăng bởi <strong>{{ $post->user->name }}</strong> 
                        <span class="mx-2">•</span> {{ $post->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                <form action="{{ route('admin.forum.delete', $post->id) }}" method="POST" onsubmit="return confirm('Xóa bài gốc sẽ mất hết bình luận. Tiếp tục?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm fw-bold rounded-pill px-3">
                        <i class="fa-solid fa-trash me-1"></i> Xóa bài viết
                    </button>
                </form>
            </div>
            <div class="text-dark" style="font-size: 1.05rem; line-height: 1.6;">
                {!! nl2br(e($post->content)) !!}
            </div>
        </div>

        <h5 class="fw-bold text-dark mb-3 ps-2 border-start border-4 border-primary">
            Bình luận ({{ $post->replies->count() }})
        </h5>

        <div class="card post-card">
            @forelse($post->replies as $reply)
            <div class="comment-item d-flex">
                <img src="{{ $reply->user->avatar ? asset('storage/'.$reply->user->avatar) : 'https://ui-avatars.com/api/?name='.$reply->user->name }}" class="avatar me-3" style="width: 35px; height: 35px;">
                
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <span class="fw-bold text-dark">{{ $reply->user->name }}</span>
                            @if($reply->user->role == 'teacher')
                                <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;">GIÁO VIÊN</span>
                            @endif
                            <span class="text-muted small ms-2">{{ $reply->created_at->diffForHumans() }}</span>
                        </div>
                        
                        <form action="{{ route('admin.forum.delete_comment', $reply->id) }}" method="POST" onsubmit="return confirm('Xóa bình luận này?')">
                            @csrf @method('DELETE')
                            <button class="btn-delete-cmt" title="Gỡ bỏ bình luận vi phạm">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                    <p class="mb-0 text-secondary">{{ $reply->content }}</p>
                </div>
            </div>
            @empty
                <div class="text-center py-4 text-muted">
                    Chưa có bình luận nào.
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>