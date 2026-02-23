<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $table = 'recipes';
    protected $fillable = [
        'user_id', 'category_id', 'title', 'description', 'instructions',
        'duration', 'difficulty', 'status', 'image_url'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function ingredients(){
        return $this->belongsToMany(Ingredient::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    //Usuarios que han guardado esta receta como favorita
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }
}
