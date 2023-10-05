<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function getAll(Request $request)
    {
        // Validate and throw 422 errors
        $request->validate([
            'claimed' => 'nullable|numeric',
        ]);

        $hidden =
            [
                'genre_id',
                'created_at',
                'updated_at',
                'blurb',
                'page_count',
                'year',
                'claimed_by_name',
                'claimed_by_email',
                'claimed'
            ];

        // If no param provided, get Unclaimed books
        $claimed = $request->query('claimed', 0);

        // If param is a number that is different from 0 or 1, set to 0 (unclaimed)
        $claimed = ($claimed == 1) ? $claimed : 0;


        $books = Book::where('claimed', $claimed)
            ->with('genre:id,name')
            ->get()
            ->makeHidden($hidden);

        // Not found
        if (!count($books)) {
            return response()->json([
                'message' => "No books found"
            ], 404);
        }

        // Success
        return response()->json([
            'data' => $books,
            'message' => 'Books successfully retrieved'
        ]);
    }

    public function getById(int|string $id)
    {
        $book = Book::with('genre:id,name')->find($id);

        if ($book) {
            return response()->json([
                'data' => $book->makeHidden(['genre_id', 'created_at', 'updated_at', 'claimed_by_email', 'claimed']),
                'message' => 'Book successfully retrieved'
            ]);
        }

        return response()->json([
            'message' => "Book with id $id not found"
        ], 404);
    }

    public function claimById(int|string $id, Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email'
        ]);

        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'message' => "Book $id was not found"
            ], 404);
        }

        if ($book->claimed_by_name) {
            return response()->json([
                'message' => "Book $id is already claimed"
            ], 400);
        }

        $book->claimed_by_name = $request->name;
        $book->claimed_by_email = $request->email;
        $book->claimed = 1;
        $book->save();

        return response()->json([
            'message' => "Book $id was claimed"
        ]);
    }
}
