<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;

$user = User::first();
if (!$user) {
    echo "No users found!\n";
    exit;
}

$recipe = Recipe::first();
if (!$recipe) {
    echo "No recipes found!\n";
    exit;
}

echo "Testing favorite toggle for User [{$user->id}] and Recipe [{$recipe->id}]...\n";

Auth::login($user);
$controller = app(\App\Http\Controllers\RecipeController::class);

// Simulate the call. We pass the recipe object as if it were bound.
// If it fails here, the controller method has an issue.
try {
    // We need to check if we should pass $recipe or $id
    // Current controller expects Recipe $recipe
    $response = $controller->toggleFavorite($recipe);
    echo "Toggle Response: " . json_encode($response->getData()) . "\n";
    
    $exists = \Illuminate\Support\Facades\DB::table('favorites')
        ->where('user_id', $user->id)
        ->where('recipe_id', $recipe->id)
        ->exists();
    echo "Saved in DB? " . ($exists ? "YES" : "NO") . "\n";

    echo "Testing getFavorites...\n";
    $getFavoritesResponse = $controller->getFavorites();
    $favoritesData = $getFavoritesResponse->getData();
    echo "Favorites returned: " . count($favoritesData) . "\n";
    if (count($favoritesData) > 0) {
        echo "First favorite ID: " . $favoritesData[0]->id . "\n";
        echo "Is Favorite attribute: " . ($favoritesData[0]->is_favorite ? 'true' : 'false') . "\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
