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
        // Ejecutar los seeders de catálogos primero
        $this->call([
            CategorySeeder::class,
            IngredientSeeder::class,
        ]);

        // Crear usuario admin
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@cookbook.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Crear usuario normal
        $user = User::create([
            'name' => 'Telma Cocinera',
            'email' => 'telma@cookbook.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        // Crear una receta
        $receta = Recipe::create([
            'user_id' => $user->id,
            'category_id' => 2, // Platos Principales
            'title' => 'Pollo al Curry',
            'description' => 'Una receta deliciosa y fácil.',
            'instructions' => '1. Cortar el pollo. 2. Cocinar con especias.',
            'duration' => 30,
            'difficulty' => 'easy',
            'status' => 'published', // Ya aprobada
            'image_url' => 'https://via.placeholder.com/640x480.png/00cc77?text=Pollo', // Imagen falsa
        ]);

        // Crear ingredientes
        $pollo = Ingredient::where('name', 'Pollo')->first();
        $arroz = Ingredient::where('name', 'Arroz')->first();

        //añadir ingredientes a la receta
        $receta->ingredients()->attach([
            $pollo->id => ['quantity' => '500', 'unit' => 'gramos'],
            $arroz->id => ['quantity' => '200', 'unit' => 'gramos'],
        ]);

        // admin añade una receta a favoritos
        $admin->favorites()->attach($receta->id);

        // añadir comentario del admin a la receta
        Comment::create([
            'user_id' => $admin->id,
            'recipe_id' => $receta->id,
            'content' => '¡Tiene muy buena pinta!',
            'is_moderated' => true
        ]);
    }
}
