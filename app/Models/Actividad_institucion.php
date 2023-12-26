<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividad_institucion extends Model
{
    use HasFactory;
    protected $table = 'actividad_institucion';
    protected $fillable = ['actividad_id', 'institucion_id'];
    protected $hidden = ['created_at', 'updated_at'];
}
