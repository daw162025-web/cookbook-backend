<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
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
//    public function store(Request $request)
//    {
//        // Validacion de datos
//        $validatedData = $request->validate([
//            'title'        => 'required|string|max:255',
//            'description'  => 'required|string',
//            'instructions' => 'required|string',
//            'category_id'  => 'required|exists:categories,id', // Que la categoría exista
//            'duration'     => 'required|integer|min:1',       // Minutos
//            'difficulty'   => 'required|in:easy,medium,hard', // Solo permitimos estos valores
//            'image'        => 'required|image|mimes:jpeg,png,jpg|max:2048', // Imagen real, máx 2MB
//        ]);
//
//        try {
//            // subir imagen a cloudinary
//            $imageUrl = Cloudinary::upload($request->file('image')->getRealPath(), [
//                'folder' => 'cookbook_recetas', // Nombre de la carpeta en tu nube
//                'transformation' => [
//                    'width' => 1000,
//                    'quality' => 'auto',
//                    'fetch_format' => 'auto'
//                ]
//            ])->getSecurePath();
//
//            // Crear la receta en la bd
//            $recipe = Recipe::create([
//                'user_id'      => Auth::id(), // El ID del usuario conectado, gracias al token
//                'category_id'  => $validatedData['category_id'],
//                'title'        => $validatedData['title'],
//                'description'  => $validatedData['description'],
//                'instructions' => $validatedData['instructions'],
//                'duration'     => $validatedData['duration'],
//                'difficulty'   => $validatedData['difficulty'],
//                'image_url'    => $imageUrl, // Guardamos la URL de Cloudinary
//                'status'       => 'published' // La publicamos directamente, por ahora
//            ]);
//
//            // respuesta
//            return response()->json([
//                'message' => 'Receta creada con éxito',
//                'recipe'  => $recipe
//            ], 201);
//
//        } catch (\Exception $e) {
//            return response()->json([
//                'message' => 'Error al subir la receta',
//                'error'   => $e->getMessage()
//            ], 500);
//        }
//    }
    // POST /api/recipes

    // POST /api/recipes
    public function store(Request $request)
    {
        // 1. Validacion de datos
        // Nota: steps e ingredients vienen stringificados porque viajan por FormData
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
            // --- LA BALA DE PLATA: Múltiples fotos a Cloudinary ---
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

            // 3. Crear la receta en la bd
            // Guardamos todo de forma segura
            $recipe = Recipe::create([
                'user_id' => Auth::id(),
                'category_id' => $validatedData['category_id'],
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'instructions' => json_encode(json_decode($validatedData['steps'])), // Pasos en JSON listos
                'duration' => $validatedData['duration'],
                'difficulty' => $validatedData['difficulty'],
                'image_url' => json_encode($imageUrls), // Las N fotos guardadas como Array stringificado en DB
                'status' => 'published'
            ]);

            // Sincronizamos los ingredientes inteligentemente
            $ingredientsArray = json_decode($validatedData['ingredients'], true);

            if (is_array($ingredientsArray)) {
                $ingredientesSincronizar = [];

                foreach ($ingredientsArray as $ingrediente) {
                    // ¿Existe este ingrediente? Si no, ¡lo creo al vuelo! (Reciclaje 100%)
                    $dbIngredient = \App\Models\Ingredient::firstOrCreate(
                        ['name' => $ingrediente['nombre']],
                        ['type' => 'other']
                    );

                    $ingredientesSincronizar[$dbIngredient->id] = [
                        'quantity' => $ingrediente['cantidad'] ?? '',
                        'unit' => '-' // Ya no usamos unidades separadas visualmente
                    ];
                }

                $recipe->ingredients()->sync($ingredientesSincronizar);
            }

            // 4. respuesta
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
        // 1. Buscamos la receta
        $recipe = Recipe::findOrFail($id);

        // 2. Comprobamos que el usuario que intenta editar es el dueño
        if ($recipe->user_id !== Auth::id()) {
            return response()->json(['message' => 'No tienes permiso para editar esta receta'], 403);
        }

        // 3. Validamos los datos (la imagen ahora es opcional)
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
            // 4. ¿Ha subido una imagen nueva?
            if ($request->hasFile('image')) {
                $cloudinary = new \Cloudinary\Cloudinary(env('CLOUDINARY_URL'));
                $uploadResult = $cloudinary->uploadApi()->upload($request->file('image')->getRealPath(), [
                    'folder' => 'cookbook_recetas'
                ]);
                $recipe->image_url = $uploadResult['secure_url']; // Actualizamos la URL
            }

            // 5. Actualizamos el resto de campos si vienen en la petición
            $recipe->update($request->except(['image', 'ingredients']));

            // 6. Actualizamos los ingredientes si los ha enviado
            if ($request->has('ingredients')) {
                $recipe->ingredients()->sync($request->ingredients);
            }

            return response()->json([
                'message' => 'Receta actualizada con éxito',
                'recipe' => $recipe->load('ingredients') // Devolvemos la receta con sus ingredientes
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
            // Borramos la receta (Laravel borrará automáticamente la relación con los ingredientes en la tabla pivote)
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
            ->with('user:id,name') // Carga solo el nombre del autor
            ->withCount(['ratings as avg_rating' => function ($query) {
            $query->select(DB::raw('coalesce(avg(score), 0)')); // Calcula el promedio
        }])
            ->get();
    }
}
