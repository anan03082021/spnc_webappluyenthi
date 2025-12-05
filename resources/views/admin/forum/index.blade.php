<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Diễn đàn - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8fafc; color: #334155; }
        
        /* SIDEBAR */
        .sidebar { width: 260px; height: 100vh; position: fixed; top: 0; left: 0; background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); color: #94a3b8; padding: 20px; z-index: 1000; }
        .main-content { margin-left: 260px; padding: 30px; }
        .nav-link { color: #94a3b8; padding: 12px 15px; border-radius: 10px; margin-bottom: 5px; display: flex; align-items: center; font-weight: 600; text-decoration: none; transition: 0.2s; }
        .nav-link:hover, .nav-link.active { background-color: rgba(255,255,255,0.1); color: white; }
        .sidebar-brand { color: white; font-size: 1.5rem; font-weight: 800; display: flex; align-items: center; margin-bottom: 40px; text-decoration: none; }

        /* TABLE CARD */
        .card-table { background: white; border-radius: 20px; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.05); overflow: hidden; border: none; }
        .table thead th { background-color: #f1f5f9; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; color: #64748b; font-weight: 700; padding: 18px 24px; border: none; }
        .table tbody td { padding: 18px 24px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        .table tbody tr:hover { background-color: #f8fafc; }

        .avatar-sm { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0; margin-right: 10px; }
        
        /* Action Button */
        .btn-action { width: 35px; height: 35px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; border: none; transition: 0.2s; color: #64748b; background: white; border: 1px solid #e2e8f0; }
        .btn-action:hover { transform: translateY(-2px); box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .btn-delete:hover { background-color: #fee2e2; color: #ef4444; border-color: #fecaca; }
        .btn-view:hover { background-color: #e0f2fe; color: #0284c7; border-color: #bae6fd; }

        /* Badge */
        .badge-replies { background-color: #f1f5f9; color: #64748b; padding: 5px 12px; border-radius: 20px; font-weight: 700; font-size: 0.75rem; }
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
            <div>
                <h3 class="fw-bold m-0 text-dark">Kiểm duyệt Diễn đàn</h3>
                <p class="text-muted small m-0">Quản lý các chủ đề thảo luận của học sinh và giáo viên.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4"><i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}</div>
        @endif

        <div class="card card-table">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 40%;">Chủ đề</th>
                            <th>Người đăng</th>
                            <th>Thống kê</th>
                            <th>Ngày đăng</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $post)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark mb-1">{{ Str::limit($post->title, 50) }}</div>
                                <div class="text-muted small text-truncate" style="max-width: 350px;">
                                    {{ Str::limit($post->content, 60) }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $post->user->avatar ? asset('storage/'.$post->user->avatar) : 'https://ui-avatars.com/api/?name='.$post->user->name }}" class="avatar-sm">
                                    <div>
                                        <div class="fw-bold text-dark small">{{ $post->user->name }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">
                                            {{ $post->user->role == 'teacher' ? 'Giáo viên' : 'Học sinh' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge-replies">
                                    <i class="fa-regular fa-comments me-1"></i> {{ $post->replies_count }} phản hồi
                                </span>
                            </td>
                            <td class="text-muted small fw-bold">
                                {{ $post->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.forum.show', $post->id) }}" class="btn-action btn-view" title="Xem chi tiết & Quản lý bình luận">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    
                                    <form action="{{ route('admin.forum.delete', $post->id) }}" method="POST" onsubmit="return confirm('CẢNH BÁO: Xóa bài này sẽ mất toàn bộ bình luận bên trong. Tiếp tục?')">
                                        @csrf @method('DELETE')
                                        <button class="btn-action btn-delete" title="Xóa bài viết">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-chat-2646698-2207374.png" width="100" style="opacity: 0.5;">
                                <p class="mt-3">Chưa có bài thảo luận nào.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-top">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</body>
</html>