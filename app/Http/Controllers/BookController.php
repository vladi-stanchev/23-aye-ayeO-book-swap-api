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
            'claimed' => 'nullable|numeric|min:0|max:1',
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
        $claimed = $request->query('claimed');

        $books = Book::with('genre:id,name')
            ->when(
                $claimed !== null,
                function ($query) use ($claimed) {
                    return $query
                        ->where('claimed', $claimed);
                }
            )
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

    public function returnById(int|string $id, Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'message' => "Book $id was not found"
            ], 404);
        }

        if (!$book->claimed_by_name) {
            return response()->json([
                'message' => "Book $id is not currently claimed"
            ], 400);
        }

        if ($book->claimed_by_email !== $request->email) {
            return response()->json([
                'message' => "Book $id was not returned. $request->email did not claim this book."
            ], 400);
        }

        $book->claimed_by_name = null;
        $book->claimed_by_email = null;
        $book->claimed = 0;
        if ($book->save()) {
            return response()->json([
                'message' => "Book $id was returned"
            ]);
        }

        return response()->json([
            'message' => "Book $id was not able to be returned"
        ]);
    }

    public function add(Request $request) 
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'genre_id' => 'required|integer|min:1',
            'blurb' => 'string|max:255',
            'image' => 'url|max:999',
            'year' => 'integer'
        ]);

        $newBook = new Book();

        $newBook->title = $request->title;
        $newBook->author = $request->author;
        $newBook->genre_id = $request->genre_id;
        $newBook->blurb = $request->blurb;
        $newBook->image = $request->image;
        $newBook->year = $request->year;

        if ($newBook->save()) {
            return response()->json([
                'message' => 'Book created'
            ], 201);
        } 
        
        return response()->json([
            'message' => 'Unexpected error occurred'
        ], 500);
    }
}
