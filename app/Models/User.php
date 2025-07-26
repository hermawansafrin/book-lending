<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Schema(
 *  schema="AuthUser",
 *
 *  @OA\Property(property="email", type="string", format="email", example="admin@mail.test"),
 *  @OA\Property(property="password", type="string", format="password", example="123456"),
 * )
 *
 * @OA\Schema(
 *  schema="RegisterUser",
 *
 *  @OA\Property(property="name", type="string", example="Your Name"),
 *  @OA\Property(property="email", type="string", format="email", example="yourmail@mail.test"),
 *  @OA\Property(property="password", type="string", format="password", example="123456"),
 *  @OA\Property(property="password_confirmation", type="string", format="password", example="123456"),
 * )
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Prepare a date for array / JSON serialization.
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
