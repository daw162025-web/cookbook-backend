<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{

    public function index()
    {
        // Devuelve todos los ingredientes
        return response()->json(Ingredient::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:ingredients',
            'type' => 'nullable|string'
        ]);

        $ingredient = Ingredient::create($validated);
        return response()->json($ingredient, 201);
    }

    public function show(Ingredient $ingredient)
    {
        return response()->json($ingredient, 200);
    }

    public function update(Request $request, $id)
    {
        $ingredient = Ingredient::findOrFail($id);

        // Validacion
        $validated = $request->validate([
            'name' => 'required|string|unique:ingredients,name,' . $id,
            'type' => 'nullable|string'
        ]);

        // Actualizamos con los datos validados
        $updated = $ingredient->update($validated);

        return response()->json($ingredient->fresh(), 200);
    }

    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();
        return response()->json(['message' => 'Ingrediente eliminado'], 200);
    }
}
