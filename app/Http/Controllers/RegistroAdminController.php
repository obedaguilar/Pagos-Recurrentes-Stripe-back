<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Registros;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegistroAdminController extends Controller
{
    public function InsertarRegistroClienteAdmin(Request $request)
    {
        Registros::create([
            'nombre' => $request->nombre,
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'is_admin' => '1'

        ]);

        return response()-> json([
            'success' => true,
            'message' => 'Registro exitoso'
        ], 201);
    }

    public function ObtenerRegistros(){
        return Registros::all();
    }

    public function login(Request $request){
        $user = Registros::where('email', $request->email)->first();
        if(!is_null($user) && Hash::check($request->password, $user->password)){
            $user->api_token = Str::random (60);
            $user->save();
            return response()->json([
                'success' => true,
                'message' => 'Bienvenido al sistema',
                'api_token' => $user->api_token
            ], 200);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Correo o contraseña incorrectos'
            ], 401);
        }
    } 
}
