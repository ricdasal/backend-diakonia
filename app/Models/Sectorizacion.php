<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sectorizacion extends Model
{
    use HasFactory;
    protected $table = 'sectorizacion';
    protected $fillable = ['nombre_sectorizacion'];
    protected $hidden = ['created_at', 'updated_at'];

    public function instituciones()
    {
        return $this->belongsToMany(Institucion::class, 'sectorizacion_institucion', 'sector_id', 'institucion_id');
    }
}
