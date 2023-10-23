<?php

namespace App\Http\Controllers;

use App\Models\Genre;

class GenreController extends Controller
{
    public function getAll()
    {

        $hidden =
            [
                'created_at',
                'updated_at',
            ];

        $genres = Genre::all()
            ->makeHidden($hidden);

        if (!count($genres)) {
            return response()->json([
                'message' => "Unexpected error occurred"
            ], 500);
        }

        return response()->json([
            'data' => $genres,
            'message' => 'Genres retrieved'
        ], 200);
    }
}
