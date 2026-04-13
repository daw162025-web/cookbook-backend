<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Recipe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

$user = User::first();
$recipe = Recipe::first();

// Add to favorite
DB::table('favorites')->updateOrInsert(['user_id' => $user->id, 'recipe_id' => $recipe->id]);

echo "Initial check:\n";
echo "Recipe ID: {$recipe->id}, User ID: {$user->id}\n";
$existsInDB = DB::table('favorites')->where('user_id', $user->id)->where('recipe_id', $recipe->id)->exists();
echo "Exists in DB? " . ($existsInDB ? "YES" : "NO") . "\n";

// Access the attribute via the model
// We need to act as the user
Auth::guard('sanctum')->setUser($user);

$loadedRecipe = Recipe::find($recipe->id);
echo "Model is_favorite attribute: " . ($loadedRecipe->is_favorite ? "TRUE (RED)" : "FALSE (WHITE)") . "\n";

// Now simulate the toggle
$controller = app(\App\Http\Controllers\RecipeController::class);
Auth::setUser($user); // Set for default auth too
$response = $controller->toggleFavorite($recipe->id);
$data = $response->getData();
echo "Toggle Response Status: " . ($data->is_favorite ? "TRUE" : "FALSE") . "\n";
echo "Toggle Response Message: " . $data->message . "\n";
