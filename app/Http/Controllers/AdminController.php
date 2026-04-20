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
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:user,admin',
            'password' => 'nullable|string|min:8', // 'nullable' permite que sea opcional
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        // Solo encriptamos y añadimos la contraseña si el admin escribió algo
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return response()->json(['message' => 'Usuario actualizado correctamente']);
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


}
