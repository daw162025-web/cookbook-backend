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
        // Crear algunos usuarios para tener variedad en las valoraciones
        User::factory(10)->create();

        $this->call([
            CategorySeeder::class,
            IngredientSeeder::class,
            RecipeSeeder::class,
            RatingSeeder::class,
        ]);
    }
}
