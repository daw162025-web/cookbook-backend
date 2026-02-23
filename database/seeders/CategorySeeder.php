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
        // Creamos categorias
        \App\Models\Category::create(['name' => 'Desayunos']);
        \App\Models\Category::create(['name' => 'Platos Principales']);
        \App\Models\Category::create(['name' => 'Postres']);
        \App\Models\Category::create(['name' => 'Vegano']);
        \App\Models\Category::create(['name' => 'Sin Gluten']);
    }
}
