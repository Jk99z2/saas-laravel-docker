<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompraInsumo extends Model
{
    protected $fillable = [
        'insumo_id', 'user_id', 'cantidad',
        'costo_unitario', 'costo_total', 'fecha', 'notas'
    ];

    protected $casts = [
        'cantidad'      => 'decimal:3',
        'costo_unitario'=> 'decimal:2',
        'costo_total'   => 'decimal:2',
        'fecha'         => 'date',
    ];

    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
