<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - SPNC Edutech</title>
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
                    <div class="card-body p-4 text-center">
                        
                        <div class="mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 70px; height: 70px;">
                                <i class="fa-solid fa-key fs-2"></i>
                            </div>
                        </div>

                        <h4 class="fw-bold text-dark mb-2">Quên mật khẩu?</h4>
                        <p class="text-muted small mb-4">
                            Đừng lo lắng! Hãy nhập email đăng ký của bạn, chúng tôi sẽ gửi liên kết để đặt lại mật khẩu mới.
                        </p>

                        @if (session('status'))
                            <div class="alert alert-success small text-start">
                                <i class="fa-solid fa-check-circle me-1"></i> {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger small text-start">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="mb-4">
                                <div class="input-group text-start">
                                    <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" placeholder="Nhập email của bạn" value="{{ old('email') }}" required autofocus>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary-custom text-white shadow">
                                    Gửi liên kết khôi phục
                                </button>
                            </div>

                            <a href="{{ route('login') }}" class="text-decoration-none text-muted small fw-bold">
                                <i class="fa-solid fa-arrow-left me-1"></i> Quay lại đăng nhập
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>