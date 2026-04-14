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
            ->with(['user', 'categories', 'ingredients']);

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
        $recipe = Recipe::with(['user', 'categories', 'ingredients', 'comments.user'])
            ->withCount('ratings')
            ->findOrFail($id);

        if (!$recipe) {
            return response()->json(['message' => 'Receta no encontrada'], 404);
        }

        // Buscamos si el usuario actual tiene una valoración para esta receta
        $userRating = 0;
        if (auth('sanctum')->check()) {
            $rating = \App\Models\Rating::where('user_id', auth('sanctum')->id())
                ->where('recipe_id', $id)
                ->first();
            $userRating = $rating ? $rating->score : 0;
        }

        // Añadimos el campo al JSON de respuesta
        $recipe->user_rating = $userRating;

        return response()->json($recipe);
    }

    public function store(Request $request)
    {
        //Validacion de datos
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'duration' => 'required|integer|min:1',
            'difficulty' => 'required|in:facil,media,dificil',
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
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'instructions' => json_encode(json_decode($validatedData['steps'])),
                'duration' => $validatedData['duration'],
                'difficulty' => $validatedData['difficulty'],
                'image_url' => json_encode($imageUrls),
                'status' => 'published'
            ]);

            //Sincronizar las multiples categorías
            $recipe->categories()->sync($validatedData['category_ids']);

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

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al subir la receta',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }

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
            'steps' => 'sometimes|required|string', // Equivalente a las intructions en JSON
            'category_ids' => 'sometimes|required|array',
            'category_ids.*' => 'exists:categories,id',
            'duration' => 'sometimes|required|integer|min:1',
            'difficulty' => 'sometimes|required|in:facil,media,dificil',
            'ingredients' => 'sometimes|required|string', // Espera JSON string con nombre y cantidad
            'images' => 'sometimes|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $finalImages = [];

            // Recoger imagenes
            if ($request->has('existing_images')) {
                $existing = json_decode($request->input('existing_images'), true);
                if (is_array($existing)) {
                    $finalImages = $existing;
                }
            }

            // Subir nuevas y adjuntarlas
            if ($request->hasFile('images')) {
                $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));

                foreach ($request->file('images') as $file) {
                    $uploadResult = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                        'folder' => 'cookbook_recetas'
                    ]);
                    $finalImages[] = $uploadResult['secure_url'];
                }
            }

            // Si mandó existing_images o fotos nuevas, actualizamos.
            if ($request->has('existing_images') || $request->hasFile('images')) {
                $recipe->image_url = json_encode($finalImages);
            }

            // Actualizamos los campos directos si existen en la request
            if ($request->has('title'))
                $recipe->title = $validatedData['title'];
            if ($request->has('description'))
                $recipe->description = $validatedData['description'];
            if ($request->has('steps'))
                $recipe->instructions = json_encode(json_decode($validatedData['steps']));
            if ($request->has('duration'))
                $recipe->duration = $validatedData['duration'];
            if ($request->has('difficulty'))
                $recipe->difficulty = $validatedData['difficulty'];

            $recipe->save();

            // Sincronizamos las categorías
            if ($request->has('category_ids')) {
                $recipe->categories()->sync($validatedData['category_ids']);
            }

            // Sincronizamos los ingredientes
            if ($request->has('ingredients')) {
                $ingredientsArray = json_decode($validatedData['ingredients'], true);
                if (is_array($ingredientsArray)) {
                    $ingredientesSincronizar = [];
                    foreach ($ingredientsArray as $ingrediente) {
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
            }

            return response()->json([
                'message' => 'Receta actualizada con éxito',
                'recipe' => $recipe->load(['ingredients', 'categories'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar',
                'error' => $e->getMessage(),
                'linea' => $e->getLine()
            ], 500);
        }
    }

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

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function myRecipes()
    {
        return Recipe::where('user_id', Auth::id())
            ->with(['categories', 'ingredients'])
            ->withCount([
                'ratings as avg_rating' => function ($query) {
                    $query->select(DB::raw('coalesce(avg(score), 0)'));
                }
            ])
            ->latest()
            ->get();
    }

    public function getByCategory($id)
    {
        return Recipe::whereHas('categories', function ($q) use ($id) {
            $q->where('categories.id', $id);
        })
            ->where('status', 'published')
            ->with(['user:id,name', 'categories'])
            ->withCount([
                'ratings as avg_rating' => function ($query) {
                    $query->select(DB::raw('coalesce(avg(score), 0)'));
                }
            ])
            ->get();
    }

    public function toggleFavorite($id)
    {
        $user = auth()->user();

        // 1. Buscamos si ya existe la relación en la tabla favoritos
        $isFavorite = $user->favoriteRecipes()->where('recipe_id', $id)->exists();

        if ($isFavorite) {
            // 2. Si ya era favorito, lo quitamos
            $user->favoriteRecipes()->detach($id);
            $newStatus = false;
            $message = 'Eliminado de favoritos';
        } else {
            // 3. Si no era favorito, lo añadimos
            $user->favoriteRecipes()->attach($id);
            $newStatus = true;
            $message = 'Añadido a favoritos';
        }

        // 4. Devolvemos el estado REAL después del cambio
        return response()->json([
            'is_favorite' => $newStatus,
            'message' => $message
        ]);
    }

    public function getFavorites()
    {
        $user = auth()->user();
        // Cargamos las recetas favoritas del usuario con sus relaciones
        $favorites = $user->favoriteRecipes()->with(['user', 'categories'])->get();

        return response()->json($favorites);
    }

    public function rate(Request $request, $id)
    {
        $request->validate([
            'score' => 'required|integer|min:1|max:5'
        ]);

        $user = auth()->user();

        // updateOrCreate: si existe lo actualiza, si no lo crea
        $rating = \App\Models\Rating::updateOrCreate(
            ['user_id' => $user->id, 'recipe_id' => $id],
            ['score' => $request->score]
        );

        // Opcional: Recalcular la media para devolverla al front
        $recipe = Recipe::withCount([
            'ratings as avg_rating' => function ($query) {
                $query->select(DB::raw('coalesce(avg(score), 0)'));
            }
        ])->find($id);

        return response()->json([
            'message' => 'Valoración guardada',
            'avg_rating' => number_format($recipe->avg_rating, 1)
        ]);
    }
}
