<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrow;
use DB;
use Illuminate\Http\Request;

class BorrowController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/books/{bookId}/borrow",
     *     tags={"Emprunts"},
     *     summary="Emprunter un livre",
     *     description="Permet à un utilisateur authentifié d'emprunter un exemplaire disponible d'un livre. Un utilisateur ne peut pas emprunter le même livre deux fois simultanément.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="bookId",
     *         in="path",
     *         description="Identifiant unique du livre à emprunter",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Livre emprunté avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Book borrowed successfully."),
     *             @OA\Property(property="borrow", ref="#/components/schemas/Borrow")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Impossible d'effectuer l'emprunt (aucun exemplaire disponible ou déjà emprunté)"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié - Veuillez vous connecter d'abord"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Livre introuvable"
     *     )
     * )
     */
    public function emprunter(Request $request, $bookId)
    {
        $book = Book::find($bookId);

        if (!$book) {
            return response()->json([
                'message' => 'Book not found.'
            ], 404);
        }

        if ($book->available_copies < 1) {
            return response()->json([
                'message' => 'No copies available for borrowing.'
            ], 400);
        }

        $dejaEmprunte = $book->borrows()->where('user_id', $request->user()->id)->where('status', 'en cours')->exists();

        if ($dejaEmprunte) {
            return response()->json([
                'message' => 'You have already borrowed this book.'
            ], 400);
        }

        $brrow = DB::transaction(function () use ($book, $request) {
            $book->decrement('available_copies');
            $book->increment('views');

            return $book->borrows()->create([
                'user_id' => $request->user()->id,
                'borrowed_at' => now(),
                'status' => 'en cours',
            ]);
        });

        return response()->json([
            'message' => 'Book borrowed successfully.',
            'borrow' => $brrow
        ], 201);

    }

    /**
     * @OA\Post(
     *     path="/api/borrows/{borrowId}/return",
     *     tags={"Emprunts"},
     *     summary="Retourner un livre emprunté",
     *     description="Permet à un utilisateur de restituer un livre qu'il a précédemment emprunté. Seul le propriétaire de l'emprunt peut effectuer cette action.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="borrowId",
     *         in="path",
     *         description="Identifiant unique de l'emprunt à clôturer",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Livre restitué avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Book returned successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ce livre a déjà été restitué"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié - Veuillez vous connecter d'abord"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Action non autorisée - Cet emprunt ne vous appartient pas"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Emprunt introuvable"
     *     )
     * )
     */
    public function retourner(Request $request, $borrowId)
    {
        $borrow = Borrow::findOrFail($borrowId);

        if ($borrow->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized.'
            ], 403);
        }

        if ($borrow->status === 'returned') {
            return response()->json([
                'message' => 'This book has already been returned.'
            ], 400);
        }

        $borrow->update([
            'returned_at' => now(),
            'status' => 'returned',
        ]);

        $borrow->book()->increment('available_copies');

        return response()->json([
            'message' => 'Book returned successfully.'
        ], 200);
    }

}
