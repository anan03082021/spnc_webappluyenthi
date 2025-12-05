<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hồ sơ cá nhân - SPNC Edutech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f0f2f5; color: #4a5568; }
        .profile-card { background: white; border-radius: 15px; border: none; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; }
        .profile-header-bg { height: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .avatar-container { position: relative; margin-top: -50px; display: inline-block; }
        .profile-avatar { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid white; box-shadow: 0 2px 10px rgba(0,0,0,0.1); background-color: white; }
        .avatar-placeholder { width: 100px; height: 100px; border-radius: 50%; border: 4px solid white; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 40px; color: #718096; font-weight: bold; margin: 0 auto; }
        .btn-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white; font-weight: 700; padding: 10px 25px; border-radius: 10px; transition: 0.3s; }
        .btn-gradient:hover { opacity: 0.9; transform: translateY(-2px); color: white; }
    </style>
</head>
<body class="pb-5">

    <nav class="navbar navbar-expand-lg sticky-top mb-5 bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/dashboard') }}">
                <i class="fa-solid fa-graduation-cap text-primary me-2"></i>SPNC<span class="text-primary">Edu</span>
            </a>
            <a href="{{ url('/dashboard') }}" class="btn btn-light btn-sm fw-bold border rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </nav>

    <div class="container">
        @if (session('status') === 'profile-updated')
            <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
                <i class="fa-solid fa-check-circle me-2"></i> Đã cập nhật hồ sơ thành công!
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="profile-card pb-4">
                    <div class="profile-header-bg"></div>
                    <div class="avatar-container">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" id="avatarPreviewSidebar" class="profile-avatar">
                        @else
                            <div class="avatar-placeholder" id="avatarPlaceholderSidebar">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <img src="" id="avatarPreviewSidebarHidden" class="profile-avatar d-none">
                        @endif
                    </div>
                    
                    <h5 class="fw-bold mt-3 mb-1 text-dark">{{ $user->name }}</h5>
                    <p class="text-muted small mb-2">{{ $user->email }}</p>
                    <p class="badge bg-primary rounded-pill">{{ strtoupper($user->role) }}</p>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-user-pen me-2 text-primary"></i>Thông tin cá nhân</h5>
                    
                    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch') <div class="row g-3">
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold small text-secondary">Ảnh đại diện</label>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <img id="avatarPreviewInput" src="https://ui-avatars.com/api/?name=Up" class="rounded-3" style="width: 50px; height: 50px; object-fit: cover; opacity: 0.5">
                                    </div>
                                    <div class="flex-grow-1">
                                        <input class="form-control" type="file" name="avatar" id="avatarInput" accept="image/*" onchange="previewImage(event)">
                                    </div>
                                </div>
                                @error('avatar') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-secondary">Họ và Tên</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-secondary">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-secondary">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                                @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-secondary">Lớp / Đơn vị</label>
                                <input type="text" name="class_name" class="form-control" value="{{ old('class_name', $user->class_name) }}">
                                @error('class_name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-gradient shadow-sm">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-lock me-2 text-primary"></i>Đổi mật khẩu</h5>
                    
                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Mật khẩu hiện tại</label>
                            <input type="password" name="current_password" class="form-control" autocomplete="current-password">
                            @error('current_password', 'updatePassword') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-secondary">Mật khẩu mới</label>
                                <input type="password" name="password" class="form-control" autocomplete="new-password">
                                @error('password', 'updatePassword') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-secondary">Xác nhận mật khẩu</label>
                                <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-secondary fw-bold text-white">Cập nhật mật khẩu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function(){
                // Cập nhật ảnh nhỏ cạnh ô input
                const output = document.getElementById('avatarPreviewInput');
                output.src = reader.result;
                output.style.opacity = 1;

                // Cập nhật ảnh to bên cột trái
                const sidebarImg = document.getElementById('avatarPreviewSidebar');
                const sidebarPlaceholder = document.getElementById('avatarPlaceholderSidebar');
                const sidebarHidden = document.getElementById('avatarPreviewSidebarHidden');

                if (sidebarImg) {
                    sidebarImg.src = reader.result;
                } else {
                    if(sidebarPlaceholder) sidebarPlaceholder.classList.add('d-none');
                    if(sidebarHidden) {
                        sidebarHidden.classList.remove('d-none');
                        sidebarHidden.src = reader.result;
                    }
                }
            }
            if(event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>
</body>
</html>