<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @OA\Schema(
 *     schema="Book",
 *     title="Book",
 *     description="Modèle représentant un livre dans la bibliothèque EcoLibrary",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Le Petit Prince"),
 *     @OA\Property(property="author", type="string", example="Antoine de Saint-Exupéry"),
 *     @OA\Property(property="slug", type="string", example="le-petit-prince"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Un conte poétique et philosophique sous l'apparence d'un livre pour enfants."),
 *     @OA\Property(property="total_copies", type="integer", example=10),
 *     @OA\Property(property="available_copies", type="integer", example=7),
 *     @OA\Property(property="degraded_copies", type="integer", example=1),
 *     @OA\Property(property="views", type="integer", example=152),
 *     @OA\Property(property="category", ref="#/components/schemas/Category")
 * )
 */
class Book extends Model
{

    use HasSlug , HasFactory ; 

    protected $fillable = [
        'title',
        'author',
        'category_id',
        'description',
        'total_copies',
        'available_copies',
        'degraded_copies',
        'views',
    ] ;
    
    public function category() {
        return $this->belongsTo(Category::class) ;
    }

    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }


}
