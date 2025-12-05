<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - SPNC Edutech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .btn-primary-custom {
            background: linear-gradient(to right, #667eea, #764ba2);
            border: none;
            padding: 12px;
            font-weight: bold;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .btn-primary-custom:hover { opacity: 0.9; transform: translateY(-2px); }
        .form-control { border-radius: 0 10px 10px 0; padding: 12px; background-color: #f8f9fa; border: 1px solid #e2e8f0; }
        .input-group-text { border-radius: 10px 0 0 10px; border: 1px solid #e2e8f0; background-color: #f8f9fa; color: #718096; }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card login-card">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h4 class="fw-bold text-dark">Đặt lại mật khẩu</h4>
                            <p class="text-muted small">Nhập mật khẩu mới cho tài khoản của bạn</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger small">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.store') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $request->email) }}" required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Mật khẩu mới</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Xác nhận mật khẩu</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-check-double"></i></span>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary-custom text-white shadow">
                                    Đổi mật khẩu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>