@extends(Auth::user()->role == 'teacher' ? 'layouts.teacher' : 'layouts.student')

@section('title', 'Chỉnh sửa bài viết')

@section('content')
<div class="container mt-4" style="max-width: 800px;">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-pen-to-square me-2"></i> Chỉnh sửa bài viết</h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('forum.update', $post->id) }}" method="POST">
                @csrf
                @method('PUT') <div class="mb-3">
                    <label class="fw-bold form-label">Tiêu đề</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $post->title) }}" required>
                </div>

                <div class="mb-3">
                    <label class="fw-bold form-label">Đoạn mã (Code Snippet)</label>
                    <textarea name="code_snippet" class="form-control font-monospace bg-dark text-white small" rows="4">{{ old('code_snippet', $post->code_snippet) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="fw-bold form-label">Nội dung</label>
                    <textarea name="content" class="form-control" rows="6" required>{{ old('content', $post->content) }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('forum.show', $post->id) }}" class="btn btn-light fw-bold">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection