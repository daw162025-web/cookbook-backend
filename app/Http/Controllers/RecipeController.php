<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    /*
     * Lista y filtra las recetas
     **/
    public function index(Request $request)
    {
        $query = Recipe::where('status', 'published')
            ->with(['user', 'category', 'ingredients']);

        if ($request->has('search')) {
            $searchTerm = $request->query('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        $recipes = $query->latest()->get();

        return response()->json($recipes);
    }

    public function show($id)
    {
        // Buscamos la receta, si no existe devuelve error
        $recipe = Recipe::with(['user', 'category', 'ingredients', 'comments.user'])
            ->findOrFail($id);

        return response()->json($recipe);
    }

    // POST /api/recipes
    public function store(Request $request)
    {
        //Validacion de datos
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'duration' => 'required|integer|min:1',
            'difficulty' => 'required|in:easy,medium,hard',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'ingredients' => 'required|string',
            'steps' => 'required|string',
        ]);

        try {
            //Multiples fotos a Cloudinary
            $imageUrls = [];
            $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $uploadResult = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                        'folder' => 'cookbook_recetas'
                    ]);
                    $imageUrls[] = $uploadResult['secure_url'];
                }
            }

            // Crear la receta en la bd
            $recipe = Recipe::create([
                'user_id' => Auth::id(),
                'category_id' => $validatedData['category_id'],
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'instructions' => json_encode(json_decode($validatedData['steps'])), // Pasos en JSON listos
                'duration' => $validatedData['duration'],
                'difficulty' => $validatedData['difficulty'],
                'image_url' => json_encode($imageUrls), // las imagenes a json
                'status' => 'published'
            ]);

            // Sincronizamos los ingredientes
            $ingredientsArray = json_decode($validatedData['ingredients'], true);

            if (is_array($ingredientsArray)) {
                $ingredientesSincronizar = [];

                foreach ($ingredientsArray as $ingrediente) {
                    // Si no existe el ingrediente se crea
                    $dbIngredient = \App\Models\Ingredient::firstOrCreate(
                        ['name' => $ingrediente['nombre']],
                        ['type' => 'other']
                    );

                    $ingredientesSincronizar[$dbIngredient->id] = [
                        'quantity' => $ingrediente['cantidad'] ?? '',
                        'unit' => '-'
                    ];
                }

                $recipe->ingredients()->sync($ingredientesSincronizar);
            }

            //respuesta
            return response()->json([
                'message' => 'Receta creada con éxito con ' . count($imageUrls) . ' fotos adjuntas.',
                'recipe' => $recipe->load('ingredients')
            ], 201);

        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al subir la receta',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }

    // PUT/PATCH /api/recipes/{id}
    public function update(Request $request, $id)
    {
        // Buscamos la receta
        $recipe = Recipe::findOrFail($id);

        //Comprobamos que el usuario que intenta editar es el dueño
        if ($recipe->user_id !== Auth::id()) {
            return response()->json(['message' => 'No tienes permiso para editar esta receta'], 403);
        }

        //Validamos los datos
        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'instructions' => 'sometimes|required|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'duration' => 'sometimes|required|integer|min:1',
            'difficulty' => 'sometimes|required|in:easy,medium,hard',
            'ingredients' => 'sometimes|array',
            'ingredients.*' => 'exists:ingredients,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // nullable = opcional
        ]);

        try {
            //Comprobamos si se ha subido nueva imagen
            if ($request->hasFile('image')) {
                $cloudinary = new \Cloudinary\Cloudinary(env('CLOUDINARY_URL'));
                $uploadResult = $cloudinary->uploadApi()->upload($request->file('image')->getRealPath(), [
                    'folder' => 'cookbook_recetas'
                ]);
                $recipe->image_url = $uploadResult['secure_url']; // Actualizamos la URL
            }

            // Actualizamos el resto de campos
            $recipe->update($request->except(['image', 'ingredients']));

            // Actualizamos ingredientes
            if ($request->has('ingredients')) {
                $recipe->ingredients()->sync($request->ingredients);
            }

            return response()->json([
                'message' => 'Receta actualizada con éxito',
                'recipe' => $recipe->load('ingredients')
            ]);

        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // DELETE /api/recipes/{id}
    public function destroy($id)
    {
        $recipe = Recipe::findOrFail($id);

        // Comprobamos que es el dueño
        if ($recipe->user_id !== Auth::id()) {
            return response()->json(['message' => 'No tienes permiso para borrar esta receta'], 403);
        }

        try {
            // Borramos la receta
            $recipe->delete();

            return response()->json([
                'message' => 'Receta eliminada correctamente'
            ]);

        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function myRecipes()
    {
        return Recipe::where('user_id', Auth::id())
            ->with(['category', 'ingredients'])
            ->withCount(['ratings as avg_rating' => function ($query) {
                $query->select(DB::raw('coalesce(avg(score), 0)'));
            }])
            ->latest()
            ->get();
    }

    public function getByCategory($id)
    {
        return Recipe::where('category_id', $id)
            ->where('status', 'published')
            ->with('user:id,name') // nombre del autor
            ->withCount(['ratings as avg_rating' => function ($query) {
            $query->select(DB::raw('coalesce(avg(score), 0)')); // Calcula el promedio de las puntuaciones
        }])
            ->get();
    }
}
