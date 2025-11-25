<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Shop - Thời trang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .product-card img { object-fit: cover; height: 240px; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">ClothesShop</a>

        <div>
            @guest
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm me-2">Đăng nhập</a>
                <a href="{{ route('register') }}" class="btn btn-warning btn-sm">Đăng ký</a>
            @endguest

            @auth
                <div class="dropdown d-inline">
                    <a class="btn btn-outline-light btn-sm dropdown-toggle" href="#" role="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ auth()->user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Sửa thông tin</a></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">Đăng xuất</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>
    </div>
</nav>

<header class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold">Bộ sưu tập mùa mới</h1>
                <p class="lead text-muted">Phong cách hiện đại — Chất liệu tuyệt vời. Khám phá bộ sưu tập thời trang nữ và nam của chúng tôi.</p>
                <div class="mt-4">
                    <a href="#products" class="btn btn-primary btn-lg me-2">Mua sắm ngay</a>
                    <a href="#categories" class="btn btn-outline-secondary btn-lg">Xem danh mục</a>
                </div>
            </div>
            <div class="col-lg-6 mt-4 mt-lg-0">
                <div class="mockup mx-auto">
                    <div class="product-box">
                        <div class="product-thumb"></div>
                        <div class="product-meta">
                            <h3>Áo khoác Bomber</h3>
                            <p class="text-muted">Thiết kế trẻ trung, ấm áp cho ngày se lạnh.</p>
                            <div class="mt-3 d-flex gap-2">
                                <span class="badge bg-warning text-dark">Sản phẩm mới</span>
                                <span class="fw-bold ms-auto">1.200.000₫</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<main class="container my-5">
    <section id="categories" class="mb-5">
        <h3 class="mb-4">Danh mục</h3>
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="category-card" style="background:linear-gradient(135deg,#ff8a8a,#ff6b6b);">Nam</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="category-card" style="background:linear-gradient(135deg,#ffd6a5,#ffb86c);">Nữ</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="category-card" style="background:linear-gradient(135deg,#c7f9cc,#7ae582);">Trẻ em</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="category-card" style="background:linear-gradient(135deg,#d7d8ff,#b7b9ff);">Phụ kiện</div>
            </div>
        </div>
    </section>

    <section id="products">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Sản phẩm nổi bật</h3>
            <a href="#" class="text-decoration-none">Xem tất cả</a>
        </div>

        <div class="row g-4">
            @php
                $sampleProducts = [
                    ['title' => 'Áo khoác bomber', 'price' => '1.200.000₫'],
                    ['title' => 'Đầm nữ xinh', 'price' => '850.000₫'],
                    ['title' => 'Áo thun basic', 'price' => '250.000₫'],
                    ['title' => 'Quần jean rách', 'price' => '690.000₫'],
                ];
            @endphp

            @foreach($sampleProducts as $p)
                <div class="col-6 col-md-3">
                    <div class="card product-card h-100">
                        <div class="product-media d-flex align-items-center justify-content-center">
                            <div style="width:70%;height:70%;border-radius:8px;background:linear-gradient(180deg,#fff,#fff6f6);display:flex;align-items:center;justify-content:center;flex-direction:column;">
                                <div style="width:56%;height:56%;background:linear-gradient(180deg,#ffdede,#ffcfcf);border-radius:6px;margin-bottom:12px"></div>
                                <small class="text-muted">{{ $p['title'] }}</small>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">{{ $p['title'] }}</h6>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <div class="text-muted">{{ $p['price'] }}</div>
                                <a href="#" class="btn btn-sm btn-primary">Mua</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</main>

<footer class="bg-white border-top py-4">
    <div class="container text-center small text-muted">
        &copy; {{ date('Y') }} ClothesShop • Thiết kế mô phỏng giao diện
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
