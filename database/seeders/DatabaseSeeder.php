<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            IngredientSeeder::class,
            RecipeSeeder::class, // Debe ir el último porque necesita IDs de los otros
        ]);
    }
}
