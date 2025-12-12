@extends(Auth::user()->role == 'teacher' ? 'layouts.teacher' : 'layouts.student')

@section('title', $post->title)

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-okaidia.min.css" rel="stylesheet" />
    <style>
        body { background-color: #f8fafc; }
        .post-container { max-width: 900px; margin: 0 auto; padding-bottom: 50px; }
        
        /* Question Box */
        .question-box { background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 25px; margin-bottom: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .user-avatar { width: 45px; height: 45px; border-radius: 50%; border: 2px solid #f1f5f9; object-fit: cover; }
        
        /* Code Block Styling */
        .code-wrapper { position: relative; margin-top: 15px; }
        pre { border-radius: 8px !important; margin: 0 !important; }
        .btn-copy { position: absolute; top: 10px; right: 10px; background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); font-size: 0.75rem; padding: 4px 10px; border-radius: 4px; cursor: pointer; transition: 0.2s; }
        .btn-copy:hover { background: rgba(255,255,255,0.2); }

        /* Answer Box */
        .answer-box { background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 20px; margin-bottom: 15px; position: relative; transition: 0.2s; }
        
        /* Accepted Answer Style */
        .answer-accepted { border: 2px solid #10b981; background: #ecfdf5; }
        .accepted-badge { 
            position: absolute; top: -12px; right: 20px; background: #10b981; color: white; 
            padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; 
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2); z-index: 2;
        }

        /* Badge Styles */
        .badge-teacher { background: #4f46e5; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; margin-left: 8px; vertical-align: text-bottom; }
        .badge-author { background: #e2e8f0; color: #475569; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; margin-left: 5px; }
    </style>
@endpush

@section('content')

<div class="post-container">
    
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <a href="{{ $post->scope == 'teacher' ? route('teacher.forum.internal') : route('forum.index') }}" class="text-decoration-none text-muted fw-bold small">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại diễn đàn
        </a>
    </div>

    <div class="question-box">
        @if($post->related_exam_id)
            <div class="alert alert-primary d-flex align-items-center p-2 mb-3 small bg-opacity-10 border-0 text-primary fw-bold">
                <i class="fa-solid fa-link me-2"></i>
                Đang thảo luận về: <a href="#" class="text-primary text-decoration-underline ms-1">{{ $post->exam->title ?? 'Đề thi' }}</a>
                @if($post->related_question_no) <span class="ms-1">- Câu số {{ $post->related_question_no }}</span> @endif
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
            <div class="d-flex align-items-center">
                <img src="https://ui-avatars.com/api/?name={{ $post->user->name }}&background=random" class="user-avatar">
                <div class="ms-3">
                    <h5 class="fw-bold text-dark mb-0" style="line-height: 1.2;">{{ $post->title }}</h5>
                    <div class="mt-1">
                        <span class="fw-bold small text-secondary">{{ $post->user->name }}</span>
                        @if($post->user->role == 'teacher') <span class="badge-teacher">GIÁO VIÊN</span> @endif
                        <span class="text-muted small ms-1">&bull; {{ $post->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            @if(Auth::id() == $post->user_id || Auth::user()->role == 'admin')
                <div class="dropdown">
                    <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-ellipsis"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                        <li>
                            <a class="dropdown-item fw-bold text-secondary" href="{{ route('forum.edit', $post->id) }}">
                                <i class="fa-solid fa-pen me-2"></i> Chỉnh sửa
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('forum.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?');">
                                @csrf @method('DELETE')
                                <button class="dropdown-item fw-bold text-danger">
                                    <i class="fa-solid fa-trash me-2"></i> Xóa bài
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endif
        </div>

        <div class="content-body text-dark" style="font-size: 1rem; line-height: 1.6;">
            {!! nl2br(e($post->content)) !!}
        </div>

        @if($post->code_snippet)
            <div class="code-wrapper">
                <button class="btn-copy" onclick="copyCode(this)"><i class="fa-regular fa-copy"></i> Sao chép</button>
                <pre><code class="language-pascal" id="codeBlock">{{ $post->code_snippet }}</code></pre>
            </div>
        @endif
    </div>

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold text-muted m-0">{{ $post->replies->count() }} Câu trả lời</h5>
    </div>

    @forelse($post->replies as $reply)
        <div class="answer-box {{ $reply->is_accepted ? 'answer-accepted' : '' }}" id="reply-{{ $reply->id }}">
            
            @if($reply->is_accepted)
                <div class="accepted-badge"><i class="fa-solid fa-check me-1"></i> ĐÁP ÁN ĐÚNG</div>
            @endif

            <div class="d-flex justify-content-between mb-2">
                <div class="d-flex align-items-center">
                    <img src="https://ui-avatars.com/api/?name={{ $reply->user->name }}&background=random" class="rounded-circle me-2" width="32" height="32">
                    <div>
                        <span class="fw-bold text-dark small">{{ $reply->user->name }}</span>
                        @if($reply->user->role == 'teacher') 
                            <span class="badge-teacher">GV</span> 
                        @elseif($reply->user_id == $post->user_id)
                            <span class="badge-author">Tác giả</span>
                        @endif
                        <span class="text-muted small ms-1">&bull; {{ $reply->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                @if(Auth::user()->role == 'teacher' || Auth::user()->role == 'admin')
                    <div class="dropdown">
                        <button class="btn btn-sm text-muted" data-bs-toggle="dropdown"><i class="fa-solid fa-ellipsis"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            @if(Auth::user()->role == 'teacher')
                            <li>
                                <form action="{{ route('teacher.forum.approve_reply', $reply->id) }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item text-success fw-bold">
                                        <i class="fa-solid fa-check-circle me-2"></i> Chọn là đáp án đúng
                                    </button>
                                </form>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            @endif
                            <li><a class="dropdown-item text-danger small" href="#">Xóa bình luận</a></li>
                        </ul>
                    </div>
                @endif
            </div>

            <div class="text-dark" style="white-space: pre-line;">{{ $reply->content }}</div>
        </div>
    @empty
        <div class="text-center py-5 mb-4 text-muted bg-white rounded-3 border border-dashed">
            <i class="fa-regular fa-comments fs-2 mb-2 opacity-50"></i>
            <p class="m-0">Chưa có câu trả lời nào. Hãy là người đầu tiên giúp đỡ!</p>
        </div>
    @endforelse

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body p-4 bg-light rounded-3">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-reply me-2 text-primary"></i>Viết câu trả lời của bạn</h6>
            <form action="{{ route('forum.reply', $post->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <textarea name="content" class="form-control" rows="4" placeholder="Nhập nội dung thảo luận chi tiết..." required style="resize: none;"></textarea>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary fw-bold px-4">
                        <i class="fa-solid fa-paper-plane me-2"></i> Gửi câu trả lời
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-pascal.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-c.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-cpp.min.js"></script>

    <script>
        function copyCode(btn) {
            var code = document.getElementById('codeBlock').innerText;
            navigator.clipboard.writeText(code).then(function() {
                var originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Đã chép';
                setTimeout(function() {
                    btn.innerHTML = originalText;
                }, 2000);
            });
        }
    </script>
@endpush