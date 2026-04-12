<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Recipe;

$recipes = Recipe::where('status', 'published')->with('categories')->get();
echo "Total Recipes: " . $recipes->count() . "\n";
foreach ($recipes as $r) {
    echo "- ID: {$r->id}, Title: {$r->title}, Categories: " . $r->categories->pluck('name')->implode(', ') . "\n";
}
