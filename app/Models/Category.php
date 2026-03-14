<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;


/**
 * @OA\Schema(
 *     title="Category",
 *     description="Model for book categories",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the category",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the category",
 *         example="Science Fiction"
 *     ),
 *     @OA\Property(
 *         property="slug",
 *         type="string",
 *         description="URL-friendly slug of the category",
 *         example="science-fiction"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the category was created",
 *         example="2024-06-01T12:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the category was last updated",
 *         example="2024-06-01T12:00:00Z"
 *     )
 * )
 */


class Category extends Model
{
    use HasSlug ;

    protected $fillable = [
        'name',
        'slug',
    ] ;
    
    
    public function books() {
        return $this->hasMany(Book::class) ;
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }


}
