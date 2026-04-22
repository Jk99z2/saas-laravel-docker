<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductoFoto extends Model
{
    protected $fillable = ['producto_id', 'url_r2', 'orden'];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    // URL pública de la foto desde R2

    public function getUrlPublicaAttribute()
    {
        return Storage::disk('public')->url($this->url_r2);
    }

}
