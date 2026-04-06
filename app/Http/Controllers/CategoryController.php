<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // Devolvemos las categorías principales con sus subcategorías
        return response()->json(Category::whereNull('parent_id')->with('children')->get(), 200);
    }
}
