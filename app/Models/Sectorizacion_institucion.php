<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sectorizacion_institucion extends Model
{
    use HasFactory;
    protected $table = 'sectorizacion_institucion';
    protected $fillable=['sector_id','institucion_id'];

}
