<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Đề thi - SPNC Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8fafc; color: #334155; }
        
        /* SIDEBAR (Đồng bộ) */
        .sidebar { width: 260px; height: 100vh; position: fixed; top: 0; left: 0; background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); color: #94a3b8; padding: 20px; z-index: 1000; }
        .main-content { margin-left: 260px; padding: 30px; }
        .nav-link { color: #94a3b8; padding: 12px 15px; border-radius: 10px; margin-bottom: 5px; display: flex; align-items: center; font-weight: 600; text-decoration: none; transition: 0.2s; }
        .nav-link:hover, .nav-link.active { background-color: rgba(255,255,255,0.1); color: white; }
        .sidebar-brand { color: white; font-size: 1.5rem; font-weight: 800; display: flex; align-items: center; margin-bottom: 40px; text-decoration: none; }

        /* HEADER & SEARCH */
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .search-box { position: relative; width: 350px; }
        .search-box input { padding-left: 45px; border-radius: 50px; border: 1px solid #e2e8f0; height: 45px; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        .search-box i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #94a3b8; }

        /* TABLE CARD */
        .exam-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.05); overflow: hidden; border: none; }
        .table thead th { background-color: #f1f5f9; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; color: #64748b; font-weight: 700; padding: 18px 24px; border: none; }
        .table tbody td { padding: 18px 24px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        .table tbody tr:hover { background-color: #f8fafc; }

        /* SPECIFIC ELEMENTS */
        .teacher-info img { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0; margin-right: 10px; }
        
        /* Badges for Stats */
        .meta-badge {
            font-size: 0.75rem; font-weight: 700; padding: 5px 10px; border-radius: 8px; margin-right: 5px;
            background-color: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; display: inline-flex; align-items: center;
        }
        .meta-badge i { margin-right: 5px; color: #94a3b8; }

        /* Difficulty Dots */
        .diff-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        .diff-easy { background-color: #22c55e; }
        .diff-medium { background-color: #f59e0b; }
        .diff-hard { background-color: #ef4444; }

        /* Action Buttons */
        .btn-action { width: 35px; height: 35px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; border: none; transition: 0.2s; color: #64748b; background: white; border: 1px solid #e2e8f0; }
        .btn-action:hover { transform: translateY(-2px); box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        
        .btn-approve { color: #22c55e; background-color: #f0fdf4; border-color: #dcfce7; }
        .btn-approve:hover { background-color: #22c55e; color: white; }
        
        .btn-reject { color: #f59e0b; background-color: #fffbeb; border-color: #fef3c7; }
        .btn-reject:hover { background-color: #f59e0b; color: white; }

        .btn-delete:hover { background-color: #fee2e2; color: #ef4444; border-color: #fecaca; }

        /* Status Badge */
        .status-badge { padding: 6px 12px; border-radius: 30px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; }
        .status-published { background-color: #dcfce7; color: #166534; }
        .status-pending { background-color: #fff7ed; color: #9a3412; }
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="#" class="sidebar-brand"><i class="fa-solid fa-shield-halved text-primary me-2"></i> SPNC Admin</a>
        <nav class="nav flex-column">
            <a href="{{ route('admin.dashboard') }}" class="nav-link"><i class="fa-solid fa-gauge-high me-3"></i> Dashboard</a>
            <p class="text-uppercase small fw-bold mt-4 mb-2 ps-3" style="opacity: 0.5; font-size: 0.75rem;">Quản lý</p>
            <a href="{{ route('admin.users.index') }}" class="nav-link"><i class="fa-solid fa-users me-3"></i> Thành viên</a>
            <a href="{{ route('admin.exams.index') }}" class="nav-link active"><i class="fa-solid fa-file-circle-check me-3"></i> Ngân hàng đề</a>
        </nav>
    </div>

    <div class="main-content">
        
        <div class="page-header">
            <div>
                <h3 class="fw-bold m-0 text-dark">Kiểm duyệt Đề thi</h3>
                <p class="text-muted small m-0">Duyệt đề thi từ giáo viên trước khi công bố.</p>
            </div>
            
            <div class="d-flex gap-3">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" class="form-control" placeholder="Tìm tên đề, tên giáo viên...">
                </div>
                <button class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
                    <i class="fa-solid fa-filter me-2"></i> Bộ lọc
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
                <i class="fa-solid fa-circle-check fs-5 me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="card exam-card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Thông tin Đề thi</th>
                            <th>Giáo viên soạn</th>
                            <th>Thông số</th>
                            <th>Trạng thái</th>
                            <th>Ngày gửi</th>
                            <th class="text-end pe-4">Duyệt / Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $exam)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark mb-1" style="font-size: 1rem;">{{ $exam->title }}</div>
                                <div class="d-flex align-items-center">
                                    @php
                                        $diffColor = match($exam->difficulty) {
                                            'easy' => 'diff-easy', 'medium' => 'diff-medium', 'hard' => 'diff-hard', default => 'diff-medium'
                                        };
                                        $diffText = match($exam->difficulty) {
                                            'easy' => 'Cơ bản', 'medium' => 'Vận dụng', 'hard' => 'Nâng cao', default => 'Trung bình'
                                        };
                                    @endphp
                                    <span class="diff-dot {{ $diffColor }}"></span>
                                    <span class="small text-muted">{{ $diffText }}</span>
                                </div>
                            </td>

                            <td>
                                <div class="teacher-info d-flex align-items-center">
                                    <img src="{{ $exam->creator->avatar ? asset('storage/'.$exam->creator->avatar) : 'https://ui-avatars.com/api/?name='.$exam->creator->name }}" alt="Avatar">
                                    <div>
                                        <div class="fw-bold text-dark small">{{ $exam->creator->name }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">Giáo viên</div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="meta-badge" title="Thời gian">
                                    <i class="fa-regular fa-clock"></i> {{ $exam->duration }}'
                                </span>
                                <span class="meta-badge" title="Số câu hỏi">
                                    <i class="fa-solid fa-list-ol"></i> {{ $exam->total_questions }}
                                </span>
                            </td>

                            <td>
                                @if($exam->is_published)
                                    <span class="status-badge status-published">
                                        <i class="fa-solid fa-check me-1"></i> Public
                                    </span>
                                @else
                                    <span class="status-badge status-pending">
                                        <i class="fa-solid fa-hourglass-half me-1"></i> Pending
                                    </span>
                                @endif
                            </td>

                            <td class="text-muted small fw-bold">
                                {{ $exam->created_at->format('d/m/Y') }}
                            </td>

                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <form action="{{ route('admin.exams.toggle', $exam->id) }}" method="POST">
                                        @csrf
                                        @if($exam->is_published)
                                            <button class="btn-action btn-reject" title="Gỡ bài (Hủy duyệt)">
                                                <i class="fa-solid fa-eye-slash"></i>
                                            </button>
                                        @else
                                            <button class="btn-action btn-approve" title="Duyệt bài ngay">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        @endif
                                    </form>

                                    <form action="{{ route('admin.exams.delete', $exam->id) }}" method="POST" onsubmit="return confirm('CẢNH BÁO: Xóa đề thi này sẽ mất toàn bộ dữ liệu câu hỏi. Tiếp tục?')">
                                        @csrf @method('DELETE')
                                        <button class="btn-action btn-delete" title="Xóa đề thi">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-box-4064358-3363919.png" width="100" style="opacity: 0.5;">
                                <h6 class="mt-3 text-muted">Chưa có đề thi nào trong hệ thống.</h6>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-top">
                {{ $exams->links() }}
            </div>
        </div>
    </div>
</body>
</html>