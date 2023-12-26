<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    use HasFactory;
    protected $table = 'institucion';
    protected $fillable = ['nombre', 'representante_legal', 'ruc', 'numero_beneficiarios'];
    protected $hidden = ['created_at', 'updated_at'];

    public function caracterizaciones()
    {
        return $this->belongsToMany(Caracterizacion::class, 'caracterizacion_institucion', 'institucion_id', 'caracterizacion_id');
    }

    public function actividades()
    {
        return $this->belongsToMany(Actividad::class, 'actividad_institucion', 'institucion_id', 'actividad_id');
    }

    public function sectorizaciones()
    {
        return $this->belongsToMany(Sectorizacion::class, 'sectorizacion_institucion', 'institucion_id', 'sector_id');
    }

    public function clasificaciones()
    {
        return $this->belongsToMany(Clasificacion::class, 'clasificacion_institucion', 'institucion_id', 'clasificacion_id');
    }

    public function tipo_poblacion()
    {
        return $this->hasMany(Tipo_poblacion::class, "institucion_id", "id");
    }

    public function estado()
    {
        return $this->hasMany(Estado::class, "institucion_id", "id");
    }

    public function contacto()
    {
        return $this->hasMany(Contacto::class, "institucion_id", "id");
    }

    public function red_bda()
    {
        return $this->hasMany(Red_bda::class, "institucion_id", "id");
    }

    public function direccion()
    {
        return $this->hasMany(Direccion::class, "institucion_id", "id");
    }
}
