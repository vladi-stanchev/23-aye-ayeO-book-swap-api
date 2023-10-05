<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function getAll()
    {
        return response()->json([
            'data' => Book::with('genre:id,name')->get()->makeHidden(['genre_id', 'created_at', 'updated_at', 'blurb', 'page_count', 'year', 'claimed_by_name', 'claimed_by_email']),
            'message' => 'Books successfully retrieved'
        ]);
    }

    public function getById(int|string $id)
    {
        $book = Book::with('genre:id,name')->find($id);

        if ($book) {
            return response()->json([
                'data' => $book->makeHidden(['genre_id', 'created_at', 'updated_at', 'claimed_by_email']),
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
        $book->save();

        return response()->json([
            'message' => "Book $id was claimed"
        ]);
    }
}
