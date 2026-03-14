<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    

    /**
     * @OA\Get(
     *     path="/api/books",
     *     tags={"Books"},
     *     summary="Liste de tous les livres",
     *     description="Récupère la liste complète de tous les livres disponibles dans la bibliothèque, avec leur catégorie.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste récupérée avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Book")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié - Veuillez vous connecter d'abord"
     *     )
     * )
     */
    public function index()
    {
        $books = Book::with('category')->get() ;
        return response()->json([
            'books' => BookResource::collection($books)
        ], 200) ;
    }

    /**
     * @OA\Post(
     *     path="/api/books",
     *     tags={"Books"},
     *     summary="⚠️ Admin Only - Ajouter un nouveau livre",
     *     description="Permet à un administrateur d'ajouter un nouveau livre dans la bibliothèque.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "author", "category_id", "total_copies", "available_copies", "degraded_copies"},
     *             @OA\Property(property="title", type="string", example="Le Seigneur des Anneaux"),
     *             @OA\Property(property="author", type="string", example="J.R.R. Tolkien"),
     *             @OA\Property(property="category_id", type="integer", example=2),
     *             @OA\Property(property="description", type="string", nullable=true, example="Une épopée fantastique se déroulant en Terre du Milieu."),
     *             @OA\Property(property="total_copies", type="integer", example=5),
     *             @OA\Property(property="available_copies", type="integer", example=5),
     *             @OA\Property(property="degraded_copies", type="integer", example=0)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Livre créé avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Book")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié - Veuillez vous connecter d'abord"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - Réservé aux administrateurs"
     *     )
     * )
     */
    public function store(StoreBookRequest $request)
    {
        // return response()->json([
        //     'book' => $request->validated()
        // ], 201) ;
        
        $book = Book::create($request->validated()) ;
        return response()->json([
            'message' => 'Book created successfully',
            'book' => new BookResource($book)
        ], 201) ;
    }

    /**
     * @OA\Get(
     *     path="/api/books/{id}",
     *     tags={"Books"},
     *     summary="Afficher les détails d'un livre",
     *     description="Récupère les informations détaillées d'un livre spécifique via son identifiant.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Identifiant unique du livre",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Livre récupéré avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Book")
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
    public function show($id)
    {
        $book = Book::with('category')->find($id) ;
        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        return response()->json([
            'book' => new BookResource($book)
        ], 200) ;
    }

    /**
     * @OA\Put(
     *     path="/api/books/{id}",
     *     tags={"Books"},
     *     summary="⚠️ Admin Only - Mettre à jour un livre",
     *     description="Permet à un administrateur de modifier les informations d'un livre existant.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Identifiant unique du livre à mettre à jour",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Les Misérables - Édition Révisée"),
     *             @OA\Property(property="author", type="string", example="Victor Hugo"),
     *             @OA\Property(property="categorie_id", type="integer", example=3),
     *             @OA\Property(property="description", type="string", nullable=true, example="Un chef-d'œuvre de la littérature française."),
     *             @OA\Property(property="total_copies", type="integer", example=8),
     *             @OA\Property(property="available_copies", type="integer", example=6),
     *             @OA\Property(property="degraded_copies", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Livre mis à jour avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Book")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié - Veuillez vous connecter d'abord"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - Réservé aux administrateurs"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Livre introuvable"
     *     )
     * )
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        $book->update($request->validated());
        return response()->json([
            'message' => 'Book updated successfully',
            'book' => new BookResource($book)
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/books/{id}",
     *     tags={"Books"},
     *     summary="⚠️ Admin Only - Supprimer un livre",
     *     description="Permet à un administrateur de supprimer définitivement un livre de la bibliothèque.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Identifiant unique du livre à supprimer",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Livre supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Book deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié - Veuillez vous connecter d'abord"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - Réservé aux administrateurs"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Livre introuvable"
     *     )
     * )
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json([
            'message' => 'Book deleted successfully'
        ], 200);
    }
}
