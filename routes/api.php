<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//RUTAS PÚBLICAS
Route::post('/register', [AuthController::class , 'register']);
Route::post('/login', [AuthController::class , 'login']);

Route::get('/recipes', [RecipeController::class , 'index']);
Route::get('/recipes/{id}', [RecipeController::class , 'show']);
Route::get('/ingredients', [IngredientController::class , 'index']);
Route::get('/ingredients/{id}', [IngredientController::class , 'show']);
Route::get('/categories', [CategoryController::class , 'index']);
Route::get('/categories/{category}/recipes', [RecipeController::class , 'getByCategory']);

//RUTAS PRIVADAS
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class , 'logout']);
    // CRUD de Recetas protegido
    Route::get('/my-recipes', [RecipeController::class , 'myRecipes']);
    Route::post('/recipes', [RecipeController::class , 'store']);
    Route::post('/recipes/{id}', [RecipeController::class , 'update']); // Usamos POST para que funcione bien form-data
    Route::delete('/recipes/{id}', [RecipeController::class , 'destroy']);
    Route::post('/ingredients', [IngredientController::class , 'store']);
    // Usamos POST + _method: PUT para evitar errores de carga
    Route::post('/ingredients/{id}', [IngredientController::class , 'update']);
    Route::delete('/ingredients/{id}', [IngredientController::class , 'destroy']);
    Route::post('/recipes/{id}/favorite', [RecipeController::class, 'toggleFavorite']);
    Route::get('/favorites', [RecipeController::class, 'getFavorites']);
    Route::post('/recipes/{id}/rate', [RecipeController::class, 'rate']);
    Route::post('/recipes/{id}/comments', [RecipeController::class, 'addComment']);
});
//Panel de admin
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/stats', [AdminController::class, 'getDashboardStats']);
    Route::get('/users', [AdminController::class, 'getAllUsers']);
    Route::post('/users/{id}', [AdminController::class, 'updateUser']);
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser']);
});
