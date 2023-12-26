<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clasificacion extends Model
{
    use HasFactory;
    protected $table = 'clasificacion';
    protected $fillable = ['nombre_clasificacion'];
    protected $hidden = ['created_at', 'updated_at'];

    public function instituciones()
    {
        return $this->belongsToMany(Institucion::class, 'clasificacion_institucion', 'clasificacion_id', 'institucion_id');
    }
}
