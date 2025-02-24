<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // ✅ Add this!

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // ✅ Ensure this includes HasApiTokens

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'is_first_login',
        'company_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}

