<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function getAll(Request $request)
    {
        $request->validate([
            'claimed' => 'nullable|numeric|min:0|max:1',
            'genre' => 'nullable|numeric|min:1|exists:genres,id'
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

        $claimed = $request->query('claimed');
        $genre = $request->query('genre');

        $books = Book::with('genre:id,name')
            ->when(
                $claimed !== null,
                function ($query) use ($claimed) {
                    return $query
                        ->where('claimed', $claimed);
                }
            )
            ->when(
                $genre !== null,
                function ($query) use ($genre) {
                    return $query
                        ->where('genre_id', $genre);
                }

            )
            ->get()
            ->makeHidden($hidden);

        if (!count($books)) {
            return response()->json([
                'message' => "No books found"
            ], 404);
        }

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
}
