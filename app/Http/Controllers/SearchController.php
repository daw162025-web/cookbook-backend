<?php

namespace App\Http\Controllers;

use App\Models\SearchHistory;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index()
    {
        $history = SearchHistory::where('user_id', auth()->id())
            ->select('search_term')
            ->selectRaw('MAX(created_at) as last_search_at')
            ->groupBy('search_term')
            ->orderBy('last_search_at', 'desc')
            ->take(10)
            ->get();

        return response()->json($history);
    }

    public function store(Request $request)
    {
        $request->validate([
            'search_term' => 'required|string|max:255'
        ]);

        $lastSearch = SearchHistory::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastSearch && $lastSearch->search_term === $request->input('search_term')) {
            return response()->json(['message' => 'Búsqueda repetida, no se guarda'], 200);
        }

        $search = SearchHistory::create([
            'user_id' => auth()->id(),
            'search_term' => $request->input('search_term')
        ]);

        return response()->json($search, 201);
    }

    public function destroy($id) {
        $search = SearchHistory::where('user_id', auth()->id())->where('id', $id)->firstOrFail();
        $search->delete();
        return response()->json(['message' => 'Eliminado']);
    }
}
