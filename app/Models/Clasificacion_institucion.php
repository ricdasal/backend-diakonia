<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clasificacion_institucion extends Model
{
    use HasFactory;

    protected $table = 'clasificacion_institucion';
    protected $fillable = ['sector_id', 'institucion_id'];
    protected $hidden = ['created_at', 'updated_at'];
}
