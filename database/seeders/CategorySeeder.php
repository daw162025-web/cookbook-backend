<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar tabla (Opcional, pero util para no duplicar en tests)
        \App\Models\Category::query()->delete();

        // 1. Platos Fuertes y Principales
        $principales = \App\Models\Category::create(['name' => 'Platos Principales']);
        \App\Models\Category::create(['name' => 'Arroz', 'parent_id' => $principales->id]);
        \App\Models\Category::create(['name' => 'Pasta', 'parent_id' => $principales->id]);
        \App\Models\Category::create(['name' => 'Legumbres y guisos', 'parent_id' => $principales->id]);
        \App\Models\Category::create(['name' => 'Carnes y aves', 'parent_id' => $principales->id]);
        \App\Models\Category::create(['name' => 'Pescado y marisco', 'parent_id' => $principales->id]);

        // 2. Ligeros y Acompañantes
        $ligeros = \App\Models\Category::create(['name' => 'Ligeros y Acompañantes']);
        \App\Models\Category::create(['name' => 'Verduras y hortalizas', 'parent_id' => $ligeros->id]);
        \App\Models\Category::create(['name' => 'Sopas, caldos y cremas', 'parent_id' => $ligeros->id]);
        \App\Models\Category::create(['name' => 'Ensaladas', 'parent_id' => $ligeros->id]);
        \App\Models\Category::create(['name' => 'Salsas y guarniciones', 'parent_id' => $ligeros->id]);

        // 3. Entrantes y Snacks
        $entrantes = \App\Models\Category::create(['name' => 'Entrantes y Snacks']);
        \App\Models\Category::create(['name' => 'Tapas y aperitivos', 'parent_id' => $entrantes->id]);
        \App\Models\Category::create(['name' => 'Huevos y tortillas', 'parent_id' => $entrantes->id]);
        \App\Models\Category::create(['name' => 'Pan, masas y rebozados', 'parent_id' => $entrantes->id]);

        // 4. Postres y Bebidas
        $dulces = \App\Models\Category::create(['name' => 'Dulces y Bebidas']);
        \App\Models\Category::create(['name' => 'Postres y dulces', 'parent_id' => $dulces->id]);
        \App\Models\Category::create(['name' => 'Bebidas y cócteles', 'parent_id' => $dulces->id]);
        \App\Models\Category::create(['name' => 'Cócteles Cafés Batidos y smoothies', 'parent_id' => $dulces->id]);
    }
}
