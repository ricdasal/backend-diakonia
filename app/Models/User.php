<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    protected $table = 'users';

    protected $fillable = [
        'name',
        'apellido',
        'email',
        'telefono',
        'cargo_institucional',
        'password',
    ];

    protected $hidden = [
        'password',
        //'remember_token',
        'created_at',
        'updated_at',
        'email_verified_at'
    ];
}
