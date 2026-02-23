<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Retorna todos los ingredientes para usarlos, por ejemplo, en un desplegable
        return response()->json(Ingredient::all(), 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:ingredients',
            'type' => 'nullable|string' // Usamos el campo 'type' definido en tu fillable
        ]);

        $ingredient = Ingredient::create($validated);
        return response()->json($ingredient, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ingredient $ingredient)
    {
        return response()->json($ingredient, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $ingredient = Ingredient::findOrFail($id);

        // Validación: los nombres deben coincidir con tus claves en Postman (name, type)
        $validated = $request->validate([
            'name' => 'required|string|unique:ingredients,name,' . $id,
            'type' => 'nullable|string'
        ]);

        // Actualizamos con los datos validados
        $updated = $ingredient->update($validated);

        // Si devuelve el objeto antiguo, es que algo falló en el guardado
        return response()->json($ingredient->fresh(), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();
        return response()->json(['message' => 'Ingrediente eliminado'], 200);
    }
}
