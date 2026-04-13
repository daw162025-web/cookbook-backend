<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Desactivar claves foráneas para poder limpiar y resetear IDs
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Platos Principales
        Category::create(['id' => 1, 'name' => 'Platos Principales', 'parent_id' => null]);
        Category::create(['id' => 2, 'name' => 'Arroz', 'parent_id' => 1]);
        Category::create(['id' => 3, 'name' => 'Pasta', 'parent_id' => 1]);
        Category::create(['id' => 4, 'name' => 'Legumbres y guisos', 'parent_id' => 1]);
        Category::create(['id' => 5, 'name' => 'Carnes y aves', 'parent_id' => 1]);
        Category::create(['id' => 6, 'name' => 'Pescado y marisco', 'parent_id' => 1]);

        // 7. Ligeros y Acompañantes
        Category::create(['id' => 7, 'name' => 'Ligeros y Acompañantes', 'parent_id' => null]);
        Category::create(['id' => 8, 'name' => 'Verduras y hortalizas', 'parent_id' => 7]);
        Category::create(['id' => 9, 'name' => 'Sopas, caldos y cremas', 'parent_id' => 7]);
        Category::create(['id' => 10, 'name' => 'Ensaladas', 'parent_id' => 7]);
        Category::create(['id' => 11, 'name' => 'Salsas y guarniciones', 'parent_id' => 7]);

        // 12. Entrantes y Snacks
        Category::create(['id' => 12, 'name' => 'Entrantes y Snacks', 'parent_id' => null]);
        Category::create(['id' => 13, 'name' => 'Tapas y aperitivos', 'parent_id' => 12]);
        Category::create(['id' => 14, 'name' => 'Huevos y tortillas', 'parent_id' => 12]);
        Category::create(['id' => 15, 'name' => 'Pan, masas y rebozados', 'parent_id' => 12]);

        // 16. Dulces y Bebidas
        Category::create(['id' => 16, 'name' => 'Dulces y Bebidas', 'parent_id' => null]);
        Category::create(['id' => 17, 'name' => 'Postres y dulces', 'parent_id' => 16]);
        Category::create(['id' => 18, 'name' => 'Bebidas y cócteles', 'parent_id' => 16]);
        Category::create(['id' => 19, 'name' => 'Cócteles Cafés Batidos y smoothies', 'parent_id' => 16]);
    }
}
