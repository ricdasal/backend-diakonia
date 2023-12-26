<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    use HasFactory;
    protected $table = 'estado';
    protected $fillable = ['nombre_estado', 'institucion_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function institucion()
    {
        return $this->belongsTo(Institucion::class, "id", "institucion_id");
    }
}
