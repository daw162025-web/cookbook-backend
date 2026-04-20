<?php

namespace Database\Seeders;

use App\Models\Rating;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        $recipes = Recipe::all();
        $users = User::all();

        if ($users->isEmpty()) {
            $users = collect([User::factory()->create()]);
        }

        foreach ($recipes as $recipe) {
            // Cada receta tendrá entre 3 y 10 valoraciones aleatorias
            $numRatings = rand(3, 10);
            $randomUsers = $users->random(min($numRatings, $users->count()));

            foreach ($randomUsers as $user) {
                Rating::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'recipe_id' => $recipe->id,
                    ],
                    [
                        'score' => rand(3, 5), // Mayoría de votos positivos para que luzca bien
                    ]
                );
            }
        }
    }
}
