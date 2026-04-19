<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Producto extends Model
{
    protected $fillable = [
        'categoria_id', 'nombre', 'slug',
        'descripcion', 'precio_venta', 'activo'
    ];

    protected $casts = [
        'precio_venta' => 'decimal:2',
        'activo'       => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($producto) {
            $producto->slug = Str::slug($producto->nombre);
        });
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function fotos()
    {
        return $this->hasMany(ProductoFoto::class)->orderBy('orden');
    }

    public function recetas()
    {
        return $this->hasMany(Receta::class);
    }

    public function ventaItems()
    {
        return $this->hasMany(VentaItem::class);
    }

    // Costo de producción calculado desde la receta
    public function getCostoProduccionAttribute()
    {
        return $this->recetas->sum(function ($receta) {
            return $receta->cantidad_necesaria * $receta->insumo->costo_actual;
        });
    }

    // Margen de ganancia en porcentaje
    public function getMargenGananciaAttribute()
    {
        if ($this->precio_venta == 0) return 0;
        return round((($this->precio_venta - $this->costo_produccion) / $this->precio_venta) * 100, 2);
    }

    // Ganancia neta por unidad
    public function getGananciaNetaAttribute()
    {
        return $this->precio_venta - $this->costo_produccion;
    }
}
