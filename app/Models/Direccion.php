<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;
    protected $table = 'direccion';
    protected $fillable = ['direccion_nombre', 'url_direccion', 'latitud', 'longitud', 'institucion_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, "id", "institucion_id");
    }
}
