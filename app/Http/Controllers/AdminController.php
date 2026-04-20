<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Recipe;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Obtener estadísticas generales para el Dashboard
     */
    public function getDashboardStats()
    {
        return response()->json([
            'total_users' => User::count(),
            'total_recipes' => Recipe::count(),
            // Contamos comentarios que aún no han sido moderados
            'pending_comments_count' => Comment::where('is_moderated', false)->count(),
            // Recetas publicadas vs borradores (si usas el campo status)
            'published_recipes' => Recipe::where('status', 'publicado')->count(),
            // Usuarios registrados hoy
            'new_users_today' => User::whereDate('created_at', today())->count(),
            // Últimos usuarios registrados para la tabla del dashboard
            'latest_users' => User::latest()->take(5)->get(['id', 'name', 'email', 'role', 'created_at'])
        ]);
    }

    /**
     * Gestión de Usuarios
     */
    public function getAllUsers()
    {
        // Devolvemos todos los usuarios ordenados por los más recientes
        return response()->json(User::latest()->get());
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:user,admin',
            'password' => 'nullable|min:8'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        // Log para depuración (puedes verlo en los logs de Railway)
        if ($request->has('password') && !empty($request->password)) {
            $user->password = bcrypt($request->password);
        }

        $user->save(); // Usar save() es más seguro que update() en algunos casos

        return response()->json(['message' => 'Actualizado', 'user' => $user]);
    }

    /**
     * Eliminar usuario
     */
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        // Seguridad: No permitir que el admin se borre a sí mismo
        if (auth()->id() == $user->id) {
            return response()->json(['message' => 'No puedes eliminar tu propia cuenta administrativa'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }

    /**
     * Moderación de Comentarios
     */
    public function getPendingComments()
    {
        try {
            // Buscamos los que NO han sido moderados (is_moderated = false)
            return Comment::with(['user', 'recipe:id,title'])
                ->where('is_moderated', false)
                ->whereColumn('created_at', 'updated_at') // Filtro mágico
                ->latest()
                ->get();
        } catch (\Exception $e) {
            // Esto te dirá el error exacto en la respuesta si algo falla
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // app/Http/Controllers/AdminController.php

    public function approveComment(Request $request, $id) // Agregamos Request aunque no se use
    {
        // Usamos find() y una respuesta manual para evitar el 404 automático de findOrFail
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comentario no encontrado en la base de datos'], 404);
        }

        $comment->is_moderated = true;
        $comment->save();

        return response()->json(['message' => 'Comentario aprobado correctamente']);
    }

    public function deleteComment(Request $request, $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['message' => 'El comentario ya no existe'], 404);
        }

        $comment->delete();

        return response()->json(['message' => 'Comentario eliminado correctamente']);
    }

    public function getAllComments() {
        return Comment::with(['user', 'recipe:id,title'])->latest()->get();
    }

    public function updateComment(Request $request, $id)
    {
        try {
            $comment = Comment::findOrFail($id);

            // Validamos que los datos lleguen (is_moderated puede ser 0 o 1)
            $comment->is_moderated = $request->input('is_moderated');

            // Solo actualizamos el contenido si viene en la petición
            if ($request->has('content')) {
                $comment->content = $request->input('content');
            }

            $comment->save();

            return response()->json([
                'message' => 'Comentario actualizado con éxito',
                'comment' => $comment
            ]);
        } catch (\Exception $e) {
            // Esto enviará el error real a la consola de Angular para que lo veas
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function rejectComment($id) {
        $comment = Comment::findOrFail($id);
        $comment->is_moderated = 2; // 2 significa Rechazado
        $comment->save();
        return response()->json(['message' => 'Rechazado']);
    }

    public function getAllRecipes()
    {
        $recipes = Recipe::with(['user', 'categories', 'ingredients'])
            ->withCount('comments')
            ->latest()
            ->get();

        return response()->json($recipes);
    }

    public function destroyRecipe($id)
    {
        $recipe = Recipe::findOrFail($id);
        // Opcional: Borrar la imagen de storage si la tienes
        $recipe->delete();

        return response()->json(['message' => 'Receta eliminada']);
    }

    public function updateRecipe(Request $request, $id)
    {
        $recipe = Recipe::findOrFail($id);

        // 1. Manejo de Imágenes
        // Si envías FormData desde Angular, existing_images llega como String JSON
        $currentImages = is_string($request->existing_images)
            ? json_decode($request->existing_images, true)
            : ($request->existing_images ?? []);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $url = $file->storeOnCloudinary('recipes')->getSecurePath();
                $currentImages[] = $url;
            }
        }
        $recipe->image_url = $currentImages;

        // 2. Datos básicos
        $recipe->title = $request->title;
        $recipe->description = $request->description;
        $recipe->difficulty = $request->difficulty; // Aquí llegará 'facil', 'media', etc.

        // 3. Manejo de Instrucciones (Si las editas como array en Angular)
        if ($request->has('instructions')) {
            $recipe->instructions = is_string($request->instructions)
                ? json_decode($request->instructions, true)
                : $request->instructions;
        }

        $recipe->save();

        // 4. Sincronizar Categorías
        if ($request->has('category_ids')) {
            $categoryIds = is_string($request->category_ids)
                ? json_decode($request->category_ids, true)
                : $request->category_ids;
            $recipe->categories()->sync($categoryIds);
        }

        // 5. Sincronizar Ingredientes (¡Esto suele ser lo que da el 500!)
        if ($request->has('ingredients')) {
            $ingredientsData = is_string($request->ingredients)
                ? json_decode($request->ingredients, true)
                : $request->ingredients;

            $syncData = [];
            foreach ($ingredientsData as $ing) {
                if (isset($ing['id'])) {
                    $syncData[$ing['id']] = [
                        'quantity' => $ing['pivot']['quantity'] ?? '',
                        'unit' => $ing['pivot']['unit'] ?? ''
                    ];
                }
            }
            $recipe->ingredients()->sync($syncData);
        }

        return response()->json([
            'message' => 'Receta actualizada con éxito',
            'recipe' => $recipe->load('categories', 'ingredients')
        ]);
    }
    public function getAllCategories() {
        return response()->json(\App\Models\Category::all());
    }
}
