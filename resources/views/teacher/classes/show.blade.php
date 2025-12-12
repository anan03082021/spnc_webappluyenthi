@extends('layouts.teacher')

@section('title', 'Chi tiết lớp học')

@push('styles')
    <style>
        /* Class Detail Styles */
        body { background-color: #f8fafc; }
        
        /* Info Card */
        .class-header-card {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            border-radius: 16px; padding: 30px; color: white; margin-bottom: 30px;
            position: relative; overflow: hidden;
        }
        .class-header-card::after {
            content: ''; position: absolute; right: -20px; bottom: -50px; width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%); border-radius: 50%;
        }
        .class-code-badge { background: rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 8px; font-family: monospace; letter-spacing: 1px; }

        /* Table */
        .card-box { background: white; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); overflow: hidden; }
        .table-custom th { background: #f8fafc; color: #64748b; font-weight: 700; text-transform: uppercase; font-size: 0.75rem; padding: 15px 20px; }
        .table-custom td { padding: 15px 20px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        
        .avatar-sm { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; margin-right: 10px; }
        .btn-action { width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; transition: 0.2s; border: none; }
        .btn-remove { background: #fee2e2; color: #ef4444; }
        .btn-remove:hover { background: #ef4444; color: white; }
    </style>
@endpush

@section('content')

    <div class="mb-3">
        <a href="{{ route('teacher.classes.index') }}" class="text-decoration-none text-muted fw-bold">
            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại danh sách
        </a>
    </div>

    <div class="class-header-card">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-md-8">
                <span class="class-code-badge mb-2 d-inline-block">
                    <i class="fa-solid fa-qrcode me-2"></i>Mã lớp: <b>{{ $classroom->code }}</b>
                </span>
                <h2 class="fw-bold mb-1">{{ $classroom->name }}</h2>
                <p class="mb-0 opacity-75"><i class="fa-solid fa-calendar me-2"></i>Năm học: {{ $classroom->academic_year }}</p>
            </div>
            
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="d-flex justify-content-md-end gap-2 align-items-center">
    
    <button class="btn btn-success fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#importExcelModal">
        <i class="fa-solid fa-file-excel me-2"></i> Nhập Excel
    </button>

</div>
                <div class="d-flex justify-content-md-end gap-3 align-items-center">
                    <div class="text-white text-center">
                        <div class="fs-2 fw-bold">{{ $students->total() }}</div>
                        <div class="small opacity-75">Học sinh</div>
                    </div>
                    
                    <button class="btn btn-light fw-bold text-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        <i class="fa-solid fa-user-plus me-2"></i> Thêm HS
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Thêm học sinh vào lớp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    
                    <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="code-tab" data-bs-toggle="tab" data-bs-target="#code-pane" type="button">Cách 1: Mã tham gia</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="email-tab" data-bs-toggle="tab" data-bs-target="#email-pane" type="button">Cách 2: Nhập Email</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active text-center py-3" id="code-pane">
                            <p class="text-muted">Gửi mã này cho học sinh để các em tự tham gia:</p>
                            <div class="display-4 fw-bold text-primary ls-2 mb-3 bg-light p-3 rounded border border-dashed">
                                {{ $classroom->code }}
                            </div>
                            <button class="btn btn-outline-primary btn-sm" onclick="navigator.clipboard.writeText('{{ $classroom->code }}'); alert('Đã copy mã!');">
                                <i class="fa-regular fa-copy"></i> Sao chép mã
                            </button>
                        </div>

                        <div class="tab-pane fade" id="email-pane">
                            <form action="{{ route('teacher.classes.add_student', $classroom->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Email học sinh</label>
                                    <input type="email" name="email" class="form-control" placeholder="vidu: hocsinh@gmail.com" required>
                                    <div class="form-text text-muted">Học sinh phải đã đăng ký tài khoản trên hệ thống.</div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 fw-bold">Thêm vào lớp</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="card-box">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
            <h5 class="fw-bold text-dark m-0">Danh sách thành viên</h5>
            
            <div class="input-group" style="width: 250px;">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                <input type="text" class="form-control bg-light border-start-0" placeholder="Tìm học sinh...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-custom w-100 mb-0">
                <thead>
                    <tr>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th>Ngày tham gia</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ $student->name }}&background=random" class="avatar-sm">
                                <span class="fw-bold text-dark">{{ $student->name }}</span>
                            </div>
                        </td>
                        <td class="text-muted">{{ $student->email }}</td>
                        <td class="text-muted small">{{ $student->created_at->format('d/m/Y') }}</td>
                        <td><span class="badge bg-success bg-opacity-10 text-success">Chính thức</span></td>
                        <td class="text-end">
                            <form action="{{ route('teacher.classes.remove_student', ['classId' => $classroom->id, 'studentId' => $student->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn muốn xóa học sinh này khỏi lớp?');">
                                @csrf @method('DELETE')
                                <button class="btn-action btn-remove" title="Xóa khỏi lớp">
                                    <i class="fa-solid fa-user-minus"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-box-4064360-3363921.png" width="80" style="opacity: 0.5">
                            <p class="mt-3">Lớp học này chưa có học sinh nào.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-3 border-top">
            {{ $students->links() }}
        </div>
    </div>
<div class="modal fade" id="importExcelModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('teacher.classes.import', $classroom->id) }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-success">
                    <i class="fa-solid fa-file-csv me-2"></i>Nhập danh sách từ Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-info small border-0 bg-light text-dark">
                    <strong><i class="fa-solid fa-circle-info me-1"></i> Quy định file Excel:</strong><br>
                    1. File cần có dòng tiêu đề (Header) ở dòng 1.<br>
                    2. Cột bắt buộc: <code>ho_va_ten</code> và <code>email</code>.<br>
                    3. Mật khẩu mặc định cho học sinh mới: <b>12345678</b>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Chọn file (.xlsx, .xls)</label>
                    <input type="file" name="file" class="form-control" required accept=".xlsx, .xls, .csv">
                </div>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="submit" class="btn btn-success fw-bold px-4">
                    <i class="fa-solid fa-cloud-arrow-up me-2"></i> Tải lên ngay
                </button>
            </div>

        </form>
    </div>
</div>
@endsection 