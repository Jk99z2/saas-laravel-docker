<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    protected $fillable = [
        'nombre', 'unidad_medida',
        'costo_actual', 'stock_actual'
    ];

    protected $casts = [
        'costo_actual'  => 'decimal:2',
        'stock_actual'  => 'decimal:3',
    ];

    public function recetas()
    {
        return $this->hasMany(Receta::class);
    }

    public function compras()
    {
        return $this->hasMany(CompraInsumo::class);
    }

    // Cuántas unidades de producto se pueden hacer con el stock actual
    public function unidadesProducibles(Producto $producto)
    {
        $receta = $this->recetas->where('producto_id', $producto->id)->first();
        if (!$receta || $receta->cantidad_necesaria == 0) return 0;
        return floor($this->stock_actual / $receta->cantidad_necesaria);
    }
}
