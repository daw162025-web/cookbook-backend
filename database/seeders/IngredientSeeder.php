<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = ['Tomate', 'Huevo', 'Harina', 'Azúcar', 'Pollo', 'Lechuga', 'Arroz', 'Pasta', 'Sal', 'Aceite'];

        foreach ($ingredients as $ing) {
            Ingredient::create(['name' => $ing]);
        }
    }
}
