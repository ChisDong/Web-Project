<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$rows = App\Models\ProductColor::orderByDesc('id')->take(10)->get(['id','product_id','color_name','main_image','created_at']);
echo json_encode($rows->toArray(), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
