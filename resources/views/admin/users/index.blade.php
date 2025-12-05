<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Thành viên - SPNC Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8fafc; color: #334155; }
        
        /* SIDEBAR (Giữ nguyên style cũ để đồng bộ) */
        .sidebar { width: 260px; height: 100vh; position: fixed; top: 0; left: 0; background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); color: #94a3b8; padding: 20px; z-index: 1000; }
        .main-content { margin-left: 260px; padding: 30px; }
        .nav-link { color: #94a3b8; padding: 12px 15px; border-radius: 10px; margin-bottom: 5px; display: flex; align-items: center; font-weight: 600; text-decoration: none; transition: 0.2s; }
        .nav-link:hover, .nav-link.active { background-color: rgba(255,255,255,0.1); color: white; }
        .sidebar-brand { color: white; font-size: 1.5rem; font-weight: 800; display: flex; align-items: center; margin-bottom: 40px; text-decoration: none; }

        /* USER TABLE STYLES */
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        /* Search Bar */
        .search-box { position: relative; width: 350px; }
        .search-box input { padding-left: 45px; border-radius: 50px; border: 1px solid #e2e8f0; height: 45px; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        .search-box i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #94a3b8; }

        /* Table Card */
        .user-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.05); overflow: hidden; border: none; }
        .table thead th { background-color: #f1f5f9; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; color: #64748b; font-weight: 700; padding: 18px 24px; border: none; }
        .table tbody td { padding: 18px 24px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        .table tbody tr:last-child td { border-bottom: none; }
        .table tbody tr:hover { background-color: #f8fafc; }

        /* Avatar Info */
        .avatar-wrapper { position: relative; width: 45px; height: 45px; }
        .avatar-img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .status-dot { width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; position: absolute; bottom: 0; right: 0; }
        .bg-online { background-color: #22c55e; }
        .bg-offline { background-color: #ef4444; }

        /* Role Badges (Custom Select) */
        .role-wrapper { position: relative; display: inline-block; }
        .role-select {
            appearance: none; -webkit-appearance: none;
            padding: 6px 30px 6px 15px; border-radius: 30px; font-size: 0.8rem; font-weight: 700;
            border: none; cursor: pointer; text-align: center; width: 100%;
        }
        .role-wrapper::after {
            content: '\f078'; font-family: 'Font Awesome 6 Free'; font-weight: 900;
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            font-size: 10px; pointer-events: none;
        }
        /* Role Colors */
        .role-admin { background-color: #fee2e2; color: #b91c1c; }
        .role-admin:hover { background-color: #fecaca; }
        .role-wrapper.admin::after { color: #b91c1c; }

        .role-teacher { background-color: #fef3c7; color: #b45309; }
        .role-teacher:hover { background-color: #fde68a; }
        .role-wrapper.teacher::after { color: #b45309; }

        .role-student { background-color: #dbeafe; color: #1d4ed8; }
        .role-student:hover { background-color: #bfdbfe; }
        .role-wrapper.student::after { color: #1d4ed8; }

        /* Action Buttons */
        .btn-action { width: 35px; height: 35px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; border: none; transition: 0.2s; color: #64748b; background: transparent; }
        .btn-action:hover { background-color: #f1f5f9; color: #3b82f6; transform: translateY(-2px); }
        .btn-action.delete:hover { color: #ef4444; background-color: #fee2e2; }

    </style>
</head>
<body>

    <div class="sidebar">
        <a href="#" class="sidebar-brand"><i class="fa-solid fa-shield-halved text-primary me-2"></i> SPNC Admin</a>
        <nav class="nav flex-column">
            <a href="{{ route('admin.dashboard') }}" class="nav-link"><i class="fa-solid fa-gauge-high me-3"></i> Dashboard</a>
            <p class="text-uppercase small fw-bold mt-4 mb-2 ps-3" style="opacity: 0.5; font-size: 0.75rem;">Quản lý</p>
            <a href="{{ route('admin.users.index') }}" class="nav-link active"><i class="fa-solid fa-users me-3"></i> Thành viên</a>
            <a href="{{ route('admin.exams.index') }}" class="nav-link"><i class="fa-solid fa-file-circle-check me-3"></i> Ngân hàng đề</a>
            <a href="{{ route('admin.forum.index') }}" class="nav-link {{ request()->routeIs('admin.forum.*') ? 'active' : '' }}">
                <i class="fa-solid fa-comments me-3"></i> Diễn đàn
            </a>
        </nav>
    </div>

    <div class="main-content">
        
        <div class="page-header">
            <div>
                <h3 class="fw-bold m-0 text-dark">Thành viên hệ thống</h3>
                <p class="text-muted small m-0">Quản lý phân quyền và trạng thái hoạt động.</p>
            </div>
            
            <div class="d-flex gap-3">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" class="form-control" placeholder="Tìm kiếm theo tên, email...">
                </div>
                <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    <i class="fa-solid fa-plus me-2"></i> Thêm mới
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
                <i class="fa-solid fa-circle-check fs-5 me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="card user-card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Thông tin User</th>
                            <th>Vai trò (Role)</th>
                            <th>Trạng thái</th>
                            <th>Đơn vị / Lớp</th>
                            <th>Ngày tham gia</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-wrapper me-3">
                                        <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.$user->name.'&background=random' }}" class="avatar-img">
                                        <div class="status-dot {{ $user->is_active ? 'bg-online' : 'bg-offline' }}" 
                                             title="{{ $user->is_active ? 'Đang hoạt động' : 'Đã khóa' }}"></div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark">{{ $user->name }}</h6>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <form action="{{ route('admin.users.role', $user->id) }}" method="POST">
                                    @csrf
                                    <div class="role-wrapper {{ $user->role }}">
                                        <select name="role" class="role-select role-{{ $user->role }}" onchange="if(confirm('Bạn muốn thay đổi quyền hạn của người này?')) this.form.submit()">
                                            <option value="student" {{ $user->role == 'student' ? 'selected' : '' }}>Học sinh</option>
                                            <option value="teacher" {{ $user->role == 'teacher' ? 'selected' : '' }}>Giáo viên</option>
                                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Quản trị</option>
                                        </select>
                                    </div>
                                </form>
                            </td>

                            <td>
                                <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST">
                                    @csrf
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" 
                                               onchange="this.form.submit()" 
                                               {{ $user->is_active ? 'checked' : '' }} 
                                               {{ $user->id == Auth::id() ? 'disabled' : '' }}
                                               style="cursor: pointer;">
                                        <label class="form-check-label small text-muted ms-1">{{ $user->is_active ? 'Active' : 'Locked' }}</label>
                                    </div>
                                </form>
                            </td>

                            <td>
                                @if($user->class_name)
                                    <span class="badge bg-light text-secondary border fw-bold px-3 py-2 rounded-pill">
                                        {{ $user->class_name }}
                                    </span>
                                @else
                                    <span class="text-muted small italic">--</span>
                                @endif
                            </td>

                            <td>
                                <span class="text-secondary fw-semibold small">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </span>
                            </td>

                            <td class="text-end pe-4">
                                <button class="btn-action" title="Xem chi tiết">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                                <button class="btn-action" title="Gửi thông báo">
                                    <i class="fa-regular fa-envelope"></i>
                                </button>
                                <button class="btn-action delete" title="Xóa tài khoản" onclick="alert('Tính năng xóa vĩnh viễn cần thêm Route Delete!')">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-top">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</body>
</html>