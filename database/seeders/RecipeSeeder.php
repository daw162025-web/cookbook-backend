<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buscamos un usuario para asignar las recetas
        $user = User::updateOrCreate(
            ['email' => 'telma@gmail.com'],
            [
                'name' => 'Telma',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
            ]
        );

        // 2. Definimos una receta de ejemplo: Tortilla de Patatas
        $recipe1 = Recipe::create([
            'user_id' => $user->id,
            'title' => 'Tortilla de Patatas Española',
            'description' => 'La clásica receta de tortilla de patatas, jugosa y tradicional.',
            'instructions' => json_encode([
                'Pelar y cortar las patatas en láminas finas.',
                'Freír las patatas con cebolla en abundante aceite de oliva.',
                'Batir los huevos y mezclar con las patatas escurridas.',
                'Cuajar la tortilla en la sartén por ambos lados.'
            ]),
            'duration' => 45,
            'difficulty' => 'media', // Ya en español
            'status' => 'published',
            'image_url' => 'https://res.cloudinary.com/dd48hro4d/image/upload/v1775468798/cookbook_recetas/g4zs02puskt3k6umf6kz.jpg',        ]);

        // 3. Asignar categorías (Muchos a Muchos)
        // Buscamos subcategorías específicas por nombre
        $catIds = Category::whereIn('name', ['Huevos y tortillas', 'Tapas y aperitivos'])->pluck('id');
        $recipe1->categories()->attach($catIds);

        // 4. Asignar ingredientes con pivote (Cantidad)
        $huevo = Ingredient::where('name', 'Huevo')->first();
        $aceite = Ingredient::where('name', 'Aceite')->first();

        if($huevo) $recipe1->ingredients()->attach($huevo->id, ['quantity' => '6 unidades', 'unit' => '-']);
        if($aceite) $recipe1->ingredients()->attach($aceite->id, ['quantity' => 'al gusto', 'unit' => '-']);


        // --- Segunda Receta: Arroz con Pollo ---
        $recipe2 = Recipe::create([
            'user_id' => $user->id,
            'title' => 'Arroz con Pollo Casero',
            'description' => 'Un plato único, completo y muy fácil de preparar para toda la familia.',
            'instructions' => json_encode([
                'Dorar el pollo troceado en una cazuela.',
                'Añadir las verduras y sofreír.',
                'Echar el arroz y el caldo caliente.',
                'Cocinar 18 minutos hasta que el grano esté en su punto.'
            ]),
            'duration' => 30,
            'difficulty' => 'facil',
            'status' => 'published',
            'image_url' => 'https://res.cloudinary.com/dd48hro4d/image/upload/v1775468903/cookbook_recetas/lpbja9x7mmvkjb0fhecc.jpg',        ]);

        $catIds2 = Category::whereIn('name', ['Arroz', 'Carnes y aves'])->pluck('id');
        $recipe2->categories()->attach($catIds2);
    }
}
