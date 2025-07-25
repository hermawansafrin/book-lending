<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *  schema="CreateBook",
 *  required={"title", "author", "available_copies"},
 *
 *  @OA\Property(property="title", type="string", example="Some Book Title"),
 *  @OA\Property(property="author", type="string", example="Some Author"),
 *  @OA\Property(property="available_copies", type="integer", example=20),
 * )
 */
class Book extends Model
{
    /**
     * Prepare a date for array / JSON serialization.
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
