<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Recipe extends Model
{
    protected $table = 'recipes';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'instructions',
        'duration',
        'difficulty',
        'status',
        'image_url'
    ];

    protected $casts = [
        // 'image_url' => 'array', // Gestionado por el accesor Attribute
        // 'instructions' => 'array', // Gestionado por el accesor Attribute
    ];

    /**
     * Accesor para image_url: Asegura que siempre sea un array de URLs limpias.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) return [];

                // Si ya es un array (porque Laravel lo decodificó), lo limpiamos
                if (is_array($value)) {
                    return array_values(array_filter($value));
                }

                // Caso especial: si es un string JSON que no fue decodificado
                if (is_string($value) && str_starts_with($value, '[')) {
                    $decoded = json_decode($value, true);
                    return is_array($decoded) ? array_values($decoded) : [];
                }

                // Si es un string simple, lo envolvemos en un array
                return [$value];
            },
            set: function ($value) {
                // Para evitar el error de mapeo de columnas numéricas (0, 1, 2...)
                // devolvemos un array con el nombre de la columna explícito.
                $array = is_array($value) ? $value : [$value];
                return ['image_url' => json_encode($array)];
            }
        );
    }

    /**
     * Optimiza la URL de Cloudinary
     */
//    private function optimizeCloudinaryUrl($url): string
//    {
//        if (!is_string($url) || empty($url))
//            return '';
//
//        // Solo optimizamos si es una URL de Cloudinary real
//        if (str_contains($url, 'res.cloudinary.com')) {
//            // Evitamos duplicar la optimización si ya la tiene
//            if (!str_contains($url, '/q_auto')) {
//                return str_replace('/upload/', '/upload/q_auto:best,f_auto,w_1200/', $url);
//            }
//        }
//
//        return $url;
//    }

    /**
     * Accesor para instrucciones: Maneja los pasos como lista JSON.
     */
    protected function instructions(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) return [];
                
                // Si ya es array (por el cast), lo devolvemos
                if (is_array($value)) return $value;

                // Si es string JSON
                if (is_string($value) && str_starts_with($value, '[')) {
                    $decoded = json_decode($value, true);
                    return is_array($decoded) ? $decoded : [$value];
                }

                return [$value];
            },
            set: function ($value) {
                // Devolvemos el nombre de la columna para evitar el error 'Column 0'
                return ['instructions' => json_encode($value)];
            }
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación Muchos a Muchos con Categorías.
     * Esto permite que una receta esté en "Postres", "Chocolate" y "Fácil" a la vez.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Relación Muchos a Muchos con Ingredientes.
     * Recuerda que usamos ->withPivot('quantity', 'unit') si quieres esos datos.
     */
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class)->withPivot('quantity', 'unit');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // Esto hace que 'avg_rating' aparezca siempre en el JSON
    protected $appends = ['is_favorite', 'avg_rating', 'user_rating'];

    public function getAvgRatingAttribute()
    {
        // Retorna la media de la relación ratings redondeada a 2 decimales
        $avg = $this->ratings()->avg('score') ?? 0;
        return round((float) $avg, 2);
    }

    public function getUserRatingAttribute()
    {
        // Si el usuario no está logueado, la nota es 0
        if (!auth('sanctum')->check()) {
            return 0;
        }

        // Buscamos la nota en la tabla de ratings
        $rating = \App\Models\Rating::where('user_id', auth('sanctum')->id())
            ->where('recipe_id', $this->id)
            ->first();

        return $rating ? $rating->score : 0;
    }
    public function favoritedBy()
    {
        // Mantenemos esta relación igual
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function getIsFavoriteAttribute()
    {
        $userId = auth('sanctum')->id();

        if (!$userId) {
            return false;
        }

        // Usamos una consulta directa a la tabla pivot para mayor rapidez y fiabilidad
        return \Illuminate\Support\Facades\DB::table('favorites')
            ->where('recipe_id', $this->id)
            ->where('user_id', $userId)
            ->exists();
    }
}
