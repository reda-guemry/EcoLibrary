<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class StatistiqueController extends Controller
{
    

    public function mostViewedBooks()
    {
        $books = Book::orderBy('views', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'most_viewed_books' => $books
        ], 200);
    }

    public function mostBorrowedBooks()
    {
        $books = Book::withCount('borrows')
            ->orderBy('borrows_count', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'most_borrowed_books' => $books
        ], 200);
    }

}
