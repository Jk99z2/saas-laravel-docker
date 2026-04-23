<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['categoria', 'fotos'])
            ->orderBy('nombre')
            ->get()
            ->map(function ($producto) {
                $foto = $producto->fotos->first();

                return [
                    'id'               => $producto->id,
                    'nombre'           => $producto->nombre,
                    'slug'             => $producto->slug,
                    'descripcion'      => $producto->descripcion,
                    'precio_venta'     => $producto->precio_venta,
                    'activo'           => $producto->activo,
                    'categoria'        => $producto->categoria,
                    'foto_principal'   => $foto ? Storage::url($foto->url_r2) : null,
                    'costo_produccion' => $producto->costo_produccion,
                    'margen_ganancia'  => $producto->margen_ganancia,
                    'ganancia_neta'    => $producto->ganancia_neta,
                ];
            });

        $categorias = Categoria::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Admin/Productos/Index', [
            'productos'  => $productos,
            'categorias' => $categorias,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'       => 'required|string|max:150|unique:productos,nombre',
            'categoria_id' => 'required|exists:categorias,id',
            'descripcion'  => 'nullable|string',
            'precio_venta' => 'required|numeric|min:0',
            'fotos'        => 'nullable|array',
            'fotos.*'      => 'image|max:4096',
        ]);

        $validated['slug'] = Str::slug($validated['nombre']);

        $producto = Producto::create($validated);

        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $index => $foto) {

                $path = $foto->store("productos/{$producto->id}");

                $producto->fotos()->create([
                    'url_r2' => $path,
                    'orden'  => $index,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Producto creado correctamente.');
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'nombre'       => 'required|string|max:150|unique:productos,nombre,' . $producto->id,
            'categoria_id' => 'required|exists:categorias,id',
            'descripcion'  => 'nullable|string',
            'precio_venta' => 'required|numeric|min:0',
            'activo'       => 'boolean',
            'fotos'        => 'nullable|array',
            'fotos.*'      => 'image|max:4096',
        ]);

        $validated['slug'] = Str::slug($validated['nombre']);

        $producto->update($validated);

        if ($request->hasFile('fotos')) {
            $ultimoOrden = $producto->fotos()->max('orden') ?? -1;

            foreach ($request->file('fotos') as $index => $foto) {

                $path = $foto->store("productos/{$producto->id}");

                $producto->fotos()->create([
                    'url_r2' => $path,
                    'orden'  => $ultimoOrden + $index + 1,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        foreach ($producto->fotos as $foto) {
            Storage::delete($foto->url_r2);
            $foto->delete();
        }

        $producto->delete();

        return redirect()->back()->with('success', 'Producto eliminado correctamente.');
    }

    public function destroyFoto(Request $request, Producto $producto)
    {
        $foto = $producto->fotos()->findOrFail($request->foto_id);

        Storage::delete($foto->url_r2);
        $foto->delete();

        return redirect()->back()->with('success', 'Foto eliminada.');
    }
}
