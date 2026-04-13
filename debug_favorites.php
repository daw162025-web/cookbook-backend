<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Recipe;
use Illuminate\Support\Facades\DB;

$favorites = DB::table('favorites')->get();
echo "Total favorites: " . $favorites->count() . "\n";
foreach ($favorites as $f) {
    echo "User ID: {$f->user_id}, Recipe ID: {$f->recipe_id}, Created at: {$f->created_at}\n";
}

$users = User::all();
foreach ($users as $user) {
    echo "User [{$user->id}]: {$user->name} has " . $user->favoriteRecipes()->count() . " favorites.\n";
}
