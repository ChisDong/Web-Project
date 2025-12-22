<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$pc = App\Models\ProductColor::find(27);
if ($pc) echo json_encode($pc->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
else echo 'null';
