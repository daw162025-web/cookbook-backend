<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Recipe;

$recipe = Recipe::whereNotNull('image_url')->first();
if (!$recipe) {
    echo "No recipes with image_url found.\n";
    return;
}

echo "RECIPE ID: " . $recipe->id . "\n";
$rawValue = $recipe->getAttributes()['image_url'];
echo "RAW DB Type: " . gettype($rawValue) . "\n";
echo "RAW DB Value: " . $rawValue . "\n";

$accessorValue = $recipe->image_url;
echo "ACCESSOR Type: " . gettype($accessorValue) . "\n";
if (is_object($accessorValue))
    echo "ACCESSOR Class: " . get_class($accessorValue) . "\n";
echo "ACCESSOR Value JSON: " . json_encode($accessorValue) . "\n";
