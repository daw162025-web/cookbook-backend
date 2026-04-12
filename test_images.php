<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Recipe;

$r = new Recipe();
$values = [
    '["https://res.cloudinary.com/demo/image/upload/sample.jpg"]',
    'https://res.cloudinary.com/demo/image/upload/sample.jpg',
    null,
    ''
];

foreach ($values as $v) {
    echo "Input: $v\n";
    try {
        $result = $r->getAttributes(); // Esto no activará el accesor así directamente si no está en la BD.
        // Simulamos el accesor manualmente:
        $accessor = (fn() => $this->imageUrl())->call($r);
        $get = $accessor->get;
        $output = $get($v);
        echo "Output: " . json_encode($output) . "\n\n";
    } catch (\Throwable $e) {
        echo "Error: " . $e->getMessage() . " at " . $e->getLine() . "\n\n";
    }
}
