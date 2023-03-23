<?php

namespace App\Http\Controllers;

use App\Models\Directorios;
use Illuminate\Http\Request;

class DirectoriosController extends Controller
{
    public function ObtenerDirectorios()
    {
        return Directorios::all();
    }

    public function ObtenerPersonaPorId($id)
    {
        return Directorios::find($id);
    }

    public function Insertar(Request $request)
    {
        return Directorios::create([
            'nombre_completo' => $request->nombre_completo,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono,
            'email' => $request->email,
        ]);
    }

    public function Eliminar($id){
        return Directorios::where('id', $id)->delete();
    }
}
