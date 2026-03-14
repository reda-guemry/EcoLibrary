<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Borrow",
 *     title="Borrow",
 *     description="Modèle représentant un emprunt de livre",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=3),
 *     @OA\Property(property="book_id", type="integer", example=5),
 *     @OA\Property(property="borrowed_at", type="string", format="date-time", example="2026-03-14T10:00:00Z"),
 *     @OA\Property(property="returned_at", type="string", format="date-time", nullable=true, example=null),
 *     @OA\Property(property="status", type="string", enum={"en cours", "returned"}, example="en cours")
 * )
 */
class Borrow extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'borrowed_at',
        'returned_at',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
