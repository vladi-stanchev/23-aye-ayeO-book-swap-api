<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function getAll()
    {
        return response()->json([
            'data' => Book::with('genre:id,name')->get()->makeHidden(['genre_id', 'created_at', 'updated_at', 'blurb', 'page_count', 'year']),
            'message' => 'Books successfully retrieved'
        ]);
    }

    public function getById(int $id)
    {
        $book = Book::with('genre:id,name')->find($id);

        if ($book) {
            return response()->json([
                'data' => $book->makeHidden(['genre_id', 'created_at', 'updated_at']),
                'message' => 'Book successfully retrieved'
            ]);
        }

        return response()->json([
            'message' => "Book with id $id not found"
        ], 404);
    }
}
