<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caracterizacion_institucion extends Model
{
    use HasFactory;
    protected $table = 'caracterizacion_institucion';
    protected $fillable = ['caracterizacion_id', 'institucion_id'];
    protected $hidden = ['created_at', 'updated_at'];
}
