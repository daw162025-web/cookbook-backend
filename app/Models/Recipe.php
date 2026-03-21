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

    // Accessor seguro para image_url: convierte a array sin importar qué haya en BD
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) return [];
                // Solo intentar decodificar si parece un JSON array
                if ($value[0] === '[') {
                    $decoded = json_decode($value, true);
                    // Limpiar estado global de json_last_error para no contaminar JsonResponse
                    json_encode(null);
                    if (is_array($decoded)) {
                        return array_map([$this, 'optimizeCloudinaryUrl'], $decoded);
                    }
                }
                return [$this->optimizeCloudinaryUrl($value)]; // String plano: lo envolvemos
            },
            set: function ($value) {
                return is_array($value) ? json_encode($value) : $value;
            }
        );
    }

    // Inserta transformaciones de calidad en URLs de Cloudinary
    private function optimizeCloudinaryUrl(string $url): string
    {
        if (str_contains($url, 'res.cloudinary.com')) {
            // Buscamos /upload/ y nos aseguramos de no añadir duplicados
            if (!str_contains($url, '/q_auto')) {
                // Insertamos q_auto,f_auto y un ancho razonable (1200px para detalle, las cards lo escalan con CSS)
                return str_replace('/upload/', '/upload/q_auto:best,f_auto,w_1200/', $url);
            }
        }
        return $url;
    }

    // Accessor seguro para instructions: convierte a array sin importar qué haya en BD
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
