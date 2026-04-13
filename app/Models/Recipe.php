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

    protected $casts = [];

    // Esto limpia la URL antes de enviarla a Angular
    public function getImageUrlAttribute($value)
    {
        if (is_string($value)) {
            // Quitamos los corchetes y comillas extras si existen
            $clean = trim($value, '[]"');
            return stripslashes($clean);
        }
        return $value;
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
        return $this->hasMany(Comment::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }
    protected $appends = ['is_favorite'];

    public function getIsFavoriteAttribute()
    {
        $userId = auth('api')->id();
        if (!$userId) return false;
        return $this->favoritedBy()->where('user_id', $userId)->exists();
    }
}
