<?php

namespace App\Http\Controllers;

use App\Models\SearchHistory;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index()
    {
        $history = SearchHistory::where('user_id', auth()->id())
            ->select('query')
            ->distinct()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json($history);
    }

    public function store(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:255'
        ]);

        $lastSearch = SearchHistory::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastSearch && $lastSearch->query === $request->input('query')) {
            return response()->json(['message' => 'Búsqueda repetida, no se guarda'], 200);
        }

        $search = SearchHistory::create([
            'user_id' => auth()->id(),
            'query' => $request->input('query')
        ]);

        return response()->json($search, 201);
    }
}
