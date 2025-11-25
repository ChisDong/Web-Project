<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap 5 --}}
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">

            <h3 class="text-center mb-4">Đăng ký</h3>

            {{-- Thông báo thành công --}}
            @if(session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            {{-- Lỗi validate --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('postRegister') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="name">Họ tên</label>
                    <input type="text"
                           id="name"
                           name="name"
                           class="form-control"
                           value="{{ old('name') }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="email">Email</label>
                    <input type="email"
                           id="email"
                           name="email"
                           class="form-control"
                           value="{{ old('email') }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="phone">Số điện thoại</label>
                    <input type="tel"
                           id="phone"
                           name="phone"
                           class="form-control"
                           value="{{ old('phone') }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="address">Địa chỉ</label>
                    <input type="text"
                           id="address"
                           name="address"
                           class="form-control"
                           value="{{ old('address') }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">Mật khẩu</label>
                    <input type="password"
                           id="password"
                           name="password"
                           class="form-control"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password_confirmation">Nhập lại mật khẩu</label>
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           class="form-control"
                           required>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Đăng ký
                </button>

                <div class="text-center mt-3">
                    <small>Đã có tài khoản? <a href="{{ route('showLogin') }}">Đăng nhập</a></small>
                </div>
            </form>

        </div>
    </div>
</div>

</body>
</html>
