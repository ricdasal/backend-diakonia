<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contacto_correo extends Model
{
    use HasFactory;
    protected $table = 'contacto_correo';
    protected $fillable = ['correo_contacto', 'contacto_id'];
    protected $hidden = ['created_at', 'updated_at'];


    public function contacto()
    {
        return $this->belongsTo(Contacto::class, "id", "contacto_id");
    }
}
