<?php

namespace App\Models;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    // Para obtener las subcategorías
    public function children() {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Para obtener la categoría "padre"
    public function parent() {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Relación con recetas (n:m)
    public function recipes() {
        return $this->belongsToMany(Recipe::class);
    }
}
