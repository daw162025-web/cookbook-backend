<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Recipe extends Model
{
    protected $table = 'recipes';
    protected $fillable = [
        'user_id', 'category_id', 'title', 'description', 'instructions',
        'duration', 'difficulty', 'status', 'image_url'
    ];

    protected $casts = [];

    /**
     * Accesor para imageUrl: Convierte el string de la BD en un array
     * para Angular y asegura que las URLs de Cloudinary estén optimizadas (metodo para mejorar calidad).
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) return [];
                // decodificar si parece un JSON array
                if ($value[0] === '[') {
                    $decoded = json_decode($value, true);
                    json_encode(null);
                    if (is_array($decoded)) {
                        return array_map([$this, 'optimizeCloudinaryUrl'], $decoded);
                    }
                }
                return [$this->optimizeCloudinaryUrl($value)];
            },
            set: function ($value) {
                return is_array($value) ? json_encode($value) : $value;
            }
        );
    }

    /**
     * Modifica la URL de Cloudinary para aplicar
     * compresión automática (q_auto) y formato inteligente (f_auto).
     */
    private function optimizeCloudinaryUrl(string $url): string
    {
        if (str_contains($url, 'res.cloudinary.com')) {
            if (!str_contains($url, '/q_auto')) {
                return str_replace('/upload/', '/upload/q_auto:best,f_auto,w_1200/', $url);
            }
        }
        return $url;
    }

    /**
     * Accesor para instrucciones: Permite guardar los pasos de la receta
     * como un JSON y recuperarlos como una lista para Angular.
     */
    protected function instructions(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) return [];
                // Solo intentar decodificar si parece un JSON array
                if ($value[0] === '[') {
                    $decoded = json_decode($value, true);
                    // Limpiar estado global de json_last_error para no contaminar JsonResponse
                    json_encode(null);
                    if (is_array($decoded)) return $decoded;
                }
                return [$value]; // String plano: lo envolvemos
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    //Usuarios que han guardado esta receta como favorita
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class , 'favorites')->withTimestamps();
    }
}
