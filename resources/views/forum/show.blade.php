<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>{{ $post->title }} - Diễn đàn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f3f4f6; }
        .btn-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        
        .main-post { background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .avatar-lg { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; }
        .avatar-sm { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
        .avatar-placeholder { background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #718096; border-radius: 50%; }

        /* Style đặc biệt cho Giáo viên */
        .teacher-badge { background-color: #ffc107; color: #000; font-size: 0.75rem; padding: 2px 8px; border-radius: 10px; font-weight: 800; text-transform: uppercase; margin-left: 5px; }
        .teacher-reply { border: 2px solid #ffc107; background-color: #fffbf0; }
        .reply-card { border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    </style>
</head>
<body>

    <div class="container py-4" style="max-width: 900px;">
        <a href="{{ route('forum.index') }}" class="btn btn-outline-secondary rounded-pill mb-4 px-3 fw-bold">
            <i class="fa-solid fa-arrow-left me-2"></i>Quay lại danh sách
        </a>

        <div class="main-post p-4 mb-5">
            <div class="d-flex mb-4 border-bottom pb-3">
                <div class="me-3">
                    @if($post->user->avatar)
                        <img src="{{ asset('storage/' . $post->user->avatar) }}" class="avatar-lg shadow-sm">
                    @else
                        <div class="avatar-lg avatar-placeholder fs-4">{{ substr($post->user->name, 0, 1) }}</div>
                    @endif
                </div>
                <div>
                    <h2 class="fw-bold mb-1 text-primary">{{ $post->title }}</h2>
                    <div class="text-muted small">
                        Đăng bởi <strong class="text-dark">{{ $post->user->name }}</strong> 
                        <span class="mx-2">•</span> 
                        {{ $post->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>

            <div class="content-body fs-5 lh-lg text-dark">
                {!! nl2br(e($post->content)) !!}
            </div>
            
            <div class="mt-4 pt-3 border-top d-flex justify-content-between text-muted">
                <span><i class="fa-regular fa-comments me-2"></i>{{ $post->replies->count() }} thảo luận</span>
                <button class="btn btn-sm btn-light text-primary fw-bold" onclick="document.getElementById('replyForm').scrollIntoView();">
                    <i class="fa-solid fa-reply me-1"></i> Trả lời
                </button>
            </div>
        </div>

        <h5 class="fw-bold mb-4 ps-2 border-start border-4 border-primary">Các câu trả lời</h5>

        @foreach($post->replies as $reply)
            @php
                $isTeacher = $reply->user->role == 'teacher';
            @endphp
            <div class="card mb-3 reply-card border-0 {{ $isTeacher ? 'teacher-reply' : '' }}">
                <div class="card-body p-3">
                    <div class="d-flex">
                        <div class="me-3">
                            @if($reply->user->avatar)
                                <img src="{{ asset('storage/' . $reply->user->avatar) }}" class="avatar-sm">
                            @else
                                <div class="avatar-sm avatar-placeholder">{{ substr($reply->user->name, 0, 1) }}</div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                    <span class="fw-bold {{ $isTeacher ? 'text-dark' : 'text-secondary' }}">
                                        {{ $reply->user->name }}
                                    </span>
                                    @if($isTeacher)
                                        <span class="teacher-badge"><i class="fa-solid fa-check me-1"></i>Giáo viên</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-0 text-dark">{{ $reply->content }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @if($post->replies->isEmpty())
            <div class="text-center py-4 text-muted">
                <p>Chưa có câu trả lời nào. Bạn có biết câu trả lời không?</p>
            </div>
        @endif

        <div id="replyForm" class="card shadow-sm border-0 mt-5">
            <div class="card-body p-4 bg-white rounded-3">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-pen-to-square me-2"></i>Viết câu trả lời của bạn</h5>
                <form action="{{ route('forum.reply', $post->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <textarea name="content" class="form-control bg-light" rows="4" placeholder="Nhập nội dung thảo luận chi tiết..." required style="resize: none;"></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-gradient px-4 fw-bold shadow">
                            Gửi thảo luận <i class="fa-solid fa-paper-plane ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>