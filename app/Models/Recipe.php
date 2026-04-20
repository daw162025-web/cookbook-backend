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
        'image_url' => 'array',
        'instructions' => 'array',
    ];

    // Esto limpia la URL antes de enviarla a Angular
    public function getImageUrlAttribute($value)
    {
        if (empty($value)) return [];

        // Si ya es un array (por el cast), lo devolvemos
        if (is_array($value)) return $value;

        // Si es un string que parece un JSON (ej: ["url1","url2"])
        if (is_string($value) && str_starts_with($value, '[')) {
            return json_decode($value, true) ?: [];
        }

        // Si es un string simple, lo metemos en un array
        return [$value];
    }

//    public function setImageUrlAttribute($value)
//    {
//        $this->attributes['image_url'] = is_array($value) ? json_encode($value) : $value;
//    }

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
                if (empty($value))
                    return [];
                if ($value[0] === '[') {
                    $decoded = json_decode($value, true);
                    if (is_array($decoded))
                        return $decoded;
                }
                return [$value];
            },
            set: function ($value) {
                return is_array($value) ? json_encode($value) : $value;
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
        // Retorna la media de la relación ratings, si no hay votos devuelve 0
        return (float) ($this->ratings()->avg('score') ?? 0);
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
