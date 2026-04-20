<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validar lo que envía el usuario
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // Crear el usuario en la BD
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']), //Contraseña encriptada
            'role' => 'user', // Rol por defecto
        ]);

        // Crear el Token
        $token = $user->createToken('auth_token')->plainTextToken;

        //Devolver el token y el usuario
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        // Validar
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'El email no está registrado'
            ], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'La contraseña es incorrecta'
            ], 401);
        }

        // Crear un nuevo token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        // Borrar todos los tokens de este usuario
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        // Manejo de la foto de perfil (Cloudinary)
        if ($request->hasFile('avatar')) {
            try {
                $cloudinary = new \Cloudinary\Cloudinary(env('CLOUDINARY_URL'));
                $uploadResult = $cloudinary->uploadApi()->upload($request->file('avatar')->getRealPath(), [
                    'folder' => 'cookbook_profiles'
                ]);
                $user->profile_image_url = $uploadResult['secure_url'];
            } catch (\Exception $e) {
                // Loguear error o manejarlo
            }
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
        }

        $user->save();

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'user' => $user
        ]);
    }
}
