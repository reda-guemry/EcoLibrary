<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class StatistiqueController extends Controller
{
    

    /**
     * @OA\Get(
     *     path="/api/books/statistics/most/viewed",
     *     tags={"Statistiques"},
     *     summary="Livres les plus consultés",
     *     description="Retourne le top 5 des livres les plus consultés dans la bibliothèque, classés par nombre de vues décroissant.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="most_viewed_books",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Book")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié - Veuillez vous connecter d'abord"
     *     )
     * )
     */
    public function mostViewedBooks()
    {
        $books = Book::orderBy('views', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'most_viewed_books' => $books
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/books/statistics/most/borrowed",
     *     tags={"Statistiques"},
     *     summary="Livres les plus empruntés",
     *     description="Retourne le top 5 des livres les plus empruntés dans la bibliothèque, classés par nombre d'emprunts décroissant.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="most_borrowed_books",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Book")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié - Veuillez vous connecter d'abord"
     *     )
     * )
     */
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
