<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bookmark;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    public function save(Request $request)
    {
        $data = $request->validate([
            'book_url' => 'required|string',
            'page' => 'required|integer',
            'scroll_y' => 'nullable|integer',
        ]);

        $bookmark = Bookmark::updateOrCreate(
            ['user_id' => Auth::id(), 'book_url' => $data['book_url']],
            ['page' => $data['page'], 'scroll_y' => $data['scroll_y'] ?? 0]
        );

        return response()->json(['success' => true, 'bookmark' => $bookmark]);
    }

public function load(Request $request)
{
    $bookUrl = $request->query('book_url');

    $bookmark = Bookmark::where('user_id', Auth::id())
        ->where('book_url', $bookUrl)
        ->first();

    return response()->json($bookmark);
}

public function saveLine(Request $request)
{
    $userId = auth()->id();
    if (!$userId) {
        return response()->json(['error' => 'User not authenticated'], 401);
    }

    $data = $request->validate([
        'book_url'  => 'required|string',
        'scroll_x'  => 'nullable|integer',
        'scroll_y'  => 'nullable|integer',
    ]);

    $bookmark = Bookmark::updateOrCreate(
        [
            'user_id' => $userId,
            'book_url' => $data['book_url'],
        ],
        [
            // ne pas écraser si null
            'scroll_x' => $data['scroll_x'] ?? 0,
            'scroll_y' => $data['scroll_y'] ?? 0,
        ]
    );

    return response()->json([
        'success' => true,
        'bookmark' => $bookmark
    ]);
}


}
