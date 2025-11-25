<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang quản trị</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap 5 --}}
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">Web Project</a>

        <div class="d-flex">
            <span class="navbar-text text-light me-3">
                Xin chào, {{ auth()->user()->name ?? 'Admin' }}
            </span>

            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">
                    Đăng xuất
                </button>
            </form>
        </div>
    </div>
</nav>

<div class="container py-5">
    <h2 class="mb-4">Trang quản trị hệ thống</h2>
    <p class="text-muted mb-4">
        Đây là trang quản lý tổng. Sau này bạn sẽ triển khai chi tiết cho từng mục bên dưới.
    </p>

    {{-- Thông báo nếu có --}}
    @if(session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <div class="row g-3">

        {{-- Quản lý người dùng --}}
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Người dùng</h5>
                    <p class="card-text small text-muted">
                        Quản lý tài khoản người dùng, phân quyền <code>admin</code> / <code>customer</code>.
                    </p>
                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <a href="{{ route('showIndex') }}" class="btn btn-sm btn-primary">
                            Mở trang quản lý
                        </a>
                    @else
                        <a class="btn btn-sm btn-primary disabled" aria-disabled="true">
                            Mở trang quản lý (chưa làm)
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Danh mục --}}
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Danh mục sản phẩm</h5>
                    <p class="card-text small text-muted">
                        Quản lý bảng <code>categories</code>: tên danh mục, slug, mô tả.
                    </p>
                    <a href="#"
                       class="btn btn-sm btn-primary disabled">
                        Mở trang quản lý (chưa làm)
                    </a>
                </div>
            </div>
        </div>

        {{-- Bộ sưu tập --}}
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Bộ sưu tập</h5>
                    <p class="card-text small text-muted">
                        Quản lý bảng <code>collections</code>: banner, mô tả, nhóm sản phẩm đặc biệt.
                    </p>
                    <a href="#"
                       class="btn btn-sm btn-primary disabled">
                        Mở trang quản lý (chưa làm)
                    </a>
                </div>
            </div>
        </div>

        {{-- Sản phẩm & biến thể --}}
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Sản phẩm & biến thể</h5>
                    <p class="card-text small text-muted">
                        Quản lý các bảng:
                        <code>products</code>, <code>product_colors</code>, <code>product_images</code>,
                        <code>sizes</code>, <code>product_variants</code>.
                    </p>
                    <a href="#"
                       class="btn btn-sm btn-primary disabled">
                        Mở trang quản lý (chưa làm)
                    </a>
                </div>
            </div>
        </div>

        {{-- Đơn hàng --}}
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Đơn hàng</h5>
                    <p class="card-text small text-muted">
                        Quản lý bảng <code>orders</code> và <code>order_items</code>: trạng thái đơn, tổng tiền, chi tiết sản phẩm.
                    </p>
                    <a href="#"
                       class="btn btn-sm btn-primary disabled">
                        Mở trang quản lý (chưa làm)
                    </a>
                </div>
            </div>
        </div>

        {{-- Mã giảm giá --}}
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Mã giảm giá</h5>
                    <p class="card-text small text-muted">
                        Quản lý bảng <code>discounts</code>: mã code, phần trăm giảm, giá trị tối đa, ngày hết hạn.
                    </p>
                    <a href="#"
                       class="btn btn-sm btn-primary disabled">
                        Mở trang quản lý (chưa làm)
                    </a>
                </div>
            </div>
        </div>

        {{-- Đánh giá sản phẩm --}}
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Đánh giá sản phẩm</h5>
                    <p class="card-text small text-muted">
                        Quản lý bảng <code>product_reviews</code>: rating, bình luận của khách hàng.
                    </p>
                    <a href="#"
                       class="btn btn-sm btn-primary disabled">
                        Mở trang quản lý (chưa làm)
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
