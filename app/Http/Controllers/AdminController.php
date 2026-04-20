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
        // Traemos comentarios con su usuario y receta para saber qué moderar
        return response()->json(
            Comment::with(['user', 'recipe'])
                ->where('is_moderated', false)
                ->latest()
                ->get()
        );
    }

    public function approveComment($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->is_moderated = true;
        $comment->save();

        return response()->json(['message' => 'Comentario aprobado']);
    }

    public function deleteComment($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json(['message' => 'Comentario eliminado por el administrador']);
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

        $currentImages = json_decode($request->existing_images, true) ?? [];

        // Procesar imágenes nuevas
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $url = $file->storeOnCloudinary('recipes')->getSecurePath();
                $currentImages[] = $url;
            }
        }

        // Guardamos todas las URLs como un JSON o en tu tabla de imágenes
        $recipe->image_url = $currentImages;

        $recipe->title = $request->title;
        $recipe->description = $request->description;
        $recipe->difficulty = $request->difficulty;
        $recipe->save();

        // Sincronizar categorías (decodificando el JSON que envía FormData)
        if ($request->has('category_ids')) {
            $ids = json_decode($request->category_ids);
            $recipe->categories()->sync($ids);
        }

        return response()->json(['message' => 'Receta e imagen actualizadas']);
    }

    public function getAllCategories() {
        return response()->json(\App\Models\Category::all());
    }
}
