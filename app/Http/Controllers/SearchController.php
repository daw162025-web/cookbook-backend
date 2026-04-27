<?php

namespace App\Http\Controllers;

use App\Models\SearchHistory;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function store(Request $request) {
        $request->validate(['query' => 'required|string|max:255']);

        return SearchHistory::create([
            'user_id' => auth()->id(), //se identifica al usuario por el token
            'query' => $request->query('query')
        ]);
    }
}
