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
        $ingredients = [
            'Aceite de Oliva Virgen Extra', 'Sal', 'Pimienta Negra', 'Ajo', 'Cebolla',
            'Tomate Maduro', 'Pimiento Rojo', 'Pimiento Verde', 'Pepino', 'Vinagre de Jerez',
            'Pan del día anterior', 'Jamón Serrano', 'Huevo', 'Harina de Trigo', 'Azúcar',
            'Pollo troceado', 'Conejo', 'Arroz Bomba', 'Azafrán', 'Garbanzos',
            'Chorizo Asturiano', 'Morcilla de Burgos', 'Panceta', 'Lacón', 'Fabulosas (Alubias blancas)',
            'Pulpo cocido', 'Pimentón dulce', 'Pimentón picante', 'Patatas', 'Leche entera',
            'Canela en rama', 'Cáscara de limón', 'Cáscara de naranja', 'Mantequilla', 'Levadura',
            'Gambas frescas', 'Calamares', 'Bacalao desalado', 'Merluza', 'Mejillones',
            'Vino Blanco', 'Vino Tinto', 'Laurel', 'Perejil fresco', 'Romero',
            'Tomillo', 'Almendras', 'Piñones', 'Miel', 'Chocolate para fundir',
            'Nata para montar', 'Vainilla', 'Queso Manchego', 'Lomo de cerdo', 'Chuletón de ternera',
            'Cordero lechal', 'Cochinillo', 'Fideos finos', 'Caldo de pollo', 'Caldo de pescado'
        ];

        foreach ($ingredients as $ing) {
            Ingredient::updateOrCreate(['name' => $ing]);
        }
    }
}
