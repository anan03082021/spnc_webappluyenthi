<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - SPNC Edutech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .card-header-custom {
            background-color: white;
            padding: 30px 20px 10px;
            text-align: center;
            border-bottom: none;
        }
        .btn-primary-custom {
            background: #667eea;
            background: linear-gradient(to right, #667eea, #764ba2);
            border: none;
            padding: 12px;
            font-weight: bold;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .btn-primary-custom:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        .form-control {
            border-radius: 10px;
            padding: 12px;
            background-color: #f8f9fa;
            border: 1px solid #e2e8f0;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #667eea;
            background-color: white;
        }
        .input-group-text {
            border-radius: 10px 0 0 10px;
            border: 1px solid #e2e8f0;
            background-color: #f8f9fa;
            color: #718096;
        }
        .form-control {
            border-radius: 0 10px 10px 0;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card login-card">
                    
                    <div class="card-header-custom">
                        <div class="mb-3">
                            <i class="fa-solid fa-graduation-cap text-primary" style="font-size: 40px;"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Chào mừng trở lại!</h4>
                        <p class="text-muted small">Đăng nhập để tiếp tục ôn tập</p>
                    </div>

                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger py-2 small">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Email đăng nhập</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label text-muted small fw-bold">Mật khẩu</label>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-decoration-none small text-primary">Quên mật khẩu?</a>
                                    @endif
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                                </div>
                            </div>

                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                                <label class="form-check-label small text-muted" for="remember_me">Ghi nhớ đăng nhập</label>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary-custom text-white shadow">
                                    Đăng Nhập
                                </button>
                            </div>

                            <div class="text-center mt-4">
                                <p class="small text-muted mb-0">Chưa có tài khoản?</p>
                                <a href="{{ route('register') }}" class="fw-bold text-decoration-none text-primary">Đăng ký miễn phí ngay</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>