<?php

namespace App\Http\Controllers;

use App\Models\Facturas;
use App\Models\Pagos;
use App\Models\User;
use App\Models\Roles;
use App\Models\Suscripcion;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\TryCatch;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Storage;
use Stripe\Stripe;
use Stripe\Customer;

use Throwable;

class UserController extends Controller
{
    public function InsertarUser(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = new User();
            //    Storage::disk('local')->put('public', $pdfName);
            $uuid = Uuid::uuid6()->toString();
            $user = new User([
                'objectId' => $uuid,
                'nombre' => $request->nombre,
                'apellidoP' => $request->apellidoP,
                'apellidoM' => $request->apellidoM,
                'password' => Hash::make($request->password),
                'email' => $request->email,
                'telefono' => $request->telefono,
                'api_token' => Str::random(60),
                'user_roles_objectId' => config('app.rolesCall.cliente'),
                'documento' => $this->guardarArchivo($request, 'documento'),
            ]);
            $user->save();
            $rol = Roles::where('objectId', $user->user_roles_objectId)->first();
            //Crear el cliente en stripe y guardar el stripe_id en la tabla de usuarios
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->nombre . ' ' . $user->apellidoP . ' ' . $user->apellidoM,
            ]);
            $user->stripe_customer_id = $customer->id;
            $user->save();

            DB::commit();
            return response()->json([
                'documento' => $this->guardarArchivo($request, 'documento'),
                'api_token' => $user->api_token,
                'nombre' => $user->nombre,
                'apellidoP' => $user->apellidoP,
                'apellidoM' => $user->apellidoM,
                'email' => $user->email,
                'telefono' => $user->telefono,
                'user_roles_objectId' => $user->user_roles_objectId,
                'objectId' => $user->objectId,
                'stripe_customer_id' => $user->stripe_customer_id,
                'rol' => $rol,
                'success' => true,
                'message' => 'Registro exitoso',
            ], 201);
        } catch (Exception $e) {
            DB::rollback();
        }
    }

    public function ObtenerUsers()
    {
        try {
            if ($user = User::where('isDeleted', false)->get()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lista de usuarios',
                    'data' => $user,
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron usuarios',
            ], 404);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron usuarios',
            ], 404);
        }
    }

    public function ObtenerUserPorId(Request $request)
    {

        try {
            // $user = User::where('objectId', $request->objectId)->first();
            $user = User::where('email', $request->email)->first();
            return response()->json([
                'success' => true,
                'message' => 'Usuario encontrado',
                'data' => $user,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado',
            ], 404);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado',
            ], 404);
        }
    }

    public function EliminarUserCompleto($objectId)
    {
        try {
            $user = User::where('objectId', $objectId)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no valido',
                ], 404);
            }
            if ($user->user_roles_objectId == config('app.rolesCall.superAdmin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar al super admin',
                ], 404);
            }
            if ($user->user_roles_objectId == config('app.rolesCall.admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar al admin',
                ], 404);
            }
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado',
            ], 404);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado',
            ], 404);
        } catch (exception $e) {
            $e = $e->getMessage();
        }
    }

    public function EliminarUserUpdate(Request $request)
    {
        try {
            //aca te dice que busca isDeleted en la tabla users y find es para buscar por id y no por nombre
            $user = User::where('isDeleted', false)->find($request->objectId);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no valido',
                ], 404);
            }
            if ($user->user_roles_objectId == config('app.rolesCall.superAdmin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar al super admin',
                ], 404);
            }
            if ($user->user_roles_objectId == config('app.rolesCall.admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar al admin',
                ], 404);
            }
            if ($user->user_roles_objectId == config('app.rolesCall.cliente')) {
                $user->isDeleted = true;
                $user->api_token = null;
                $user->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Usuario eliminado',
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado',
            ], 404);
        }
    }

    public function ActualizarUsers(Request $request)
    {
        try {
            $user = User::where('objectId', $request->objectId)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no valido',
                ], 404);
            }
            if ($user->user_roles_objectId == config('app.rolesCall.superAdmin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede actualizar al super admin',
                ], 404);
            }
            if ($user->user_roles_objectId == config('app.rolesCall.admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede actualizar al admin',
                ], 404);
            }

            if ($request->hasFile('documento')) {
                $user->documento = $this->guardarArchivo($request, 'documento');
            }

            //se actualizan los datos solamente los que se desean actualizar
            if ($request->has('nombre')) {
                $user->nombre = $request->nombre;
            }

            if ($request->has('apellidoP')) {
                $user->apellidoP = $request->apellidoP;
            }

            if ($request->has('apellidoM')) {
                $user->apellidoM = $request->apellidoM;
            }

            if ($request->has('isActive')) {
                $user->isActive = $request->isActive;
            }

            // $user->documento = $url;

            $user->save();
            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado',
            ], 404);
        }
    }

    public function updatePassword(Request $request){

        $user = User::where('objectId', $request->objectId)->first();


            $user->password = Hash::make($request->newPassword);
            $user->save();
            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada',
            ], 200);

}

    public function login(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!is_null($user) && Hash::check($request->password, $user->password)) {

                $rol = Roles::where('objectId', $user->user_roles_objectId)->first();
                $user->api_token = Str::random(60);
                $user->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Bienvenido al sistema',
                    'objectId' => $user->objectId,
                    'api_token' => $user->api_token,
                    'nombre' => $user->nombre,
                    'email' => $user->email,
                    'file' => $user->documento,
                    'isActive' => $user->isActive,
                    'isDeleted' => $user->isDeleted,
                    'user_roles_objectId' => $user->user_roles_objectId,
                    'rol' => $rol,

                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Correo o contraseña incorrectos',
                ], 401);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado',
            ], 404);
        }
    }


    public function loginAdmin(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!is_null($user) && Hash::check($request->password, $user->password) && ($user->user_roles_objectId == config('app.rolesCall.superAdmin'))) {
                $user->api_token = Str::random(60);
                $user->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Bienvenido al sistema  Super Admin',
                    'api_token' => $user->api_token,
                ], 200);
            } else if ($user->user_roles_objectId != config('app.rolesCall.superAdmin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usted no cuenta con los permisos correspondientes para ingresar a esta sección',
                ], 401);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Correo o contraseña incorrectos',
                ], 401);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado',
            ], 404);
        }
    }
    public function loginSuperAdmin(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!is_null($user) && Hash::check($request->password, $user->password) && ($user->user_roles_objectId == config('app.rolesCall.superAdmin'))) {
                $user->api_token = Str::random(60);
                $user->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Bienvenido al sistema  Super Admin',
                    'api_token' => $user->api_token,
                ], 200);
            } else if ($user->user_roles_objectId != config('app.rolesCall.superAdmin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usted no cuenta con los permisos correspondientes para ingresar a esta sección',
                ], 401);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Correo o contraseña incorrectos',
                ], 401);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado',
            ], 404);
        }
    }

    public function ActualizarUsuarioUnico(Request $request)
    {
        try {
            // $user = User::where('objectId', $request->objectId)->first();
            $user = User::where('objectId', $request->objectId)->first();


            if ($request->hasFile('documento')) {
                $user->documento = $this->guardarArchivo($request, 'documento');
            }

            // se actualizan los datos solamente los que se desean actualizar
            if ($request->has('nombre')) {
                $user->nombre = $request->nombre;
            }
            if ($request->has('apellidoP')) {
                $user->apellidoP = $request->apellidoP;
            }
            if ($request->has('apellidoM')) {
                $user->apellidoM = $request->apellidoM;
            }
            // if ($request->has('email')) {
            //     $user->email = $request->email;
            // }
            // if ($request->has('password')) {
            //     $user->password = Hash::make($request->password);
            // }
            // if ($request->has('isActive')) {
            //     $user->isActive = $request->isActive;
            // }
            // if ($request->has('isDeleted')) {
            //     $user->isDeleted = $request->isDeleted;
            // }
            // if ($request->has('user_roles_objectId')) {
            //     $user->user_roles_objectId = $request->user_roles_objectId;
            // }
            $user->save();
            return response()->json([
                'success' => true,
                'message' => 'Usuario encontrado',
                'data' => $user,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado',
            ], 404);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado',
            ], 404);
        }
    }


    //vamos a crear un metodo para guardar y validar archivos


    public function guardarArchivo(Request $request, $fieldName)
    {
        $maxSize = 1024 * 1024 * 5;
        $file = $request->file('documento');
        if ($file) {
            $fileSize = $file->getSize();
            if ($fileSize > $maxSize) {
                return response()->json([
                    'success' => false,
                    'message' => 'El archivo es muy grande',
                ], 400);
            } else {
                $fileSize = null;
            }

            $fileType = $file->getMimeType();
            if ($fileType != 'application/pdf') {
                return response()->json([
                    'success' => false,
                    'message' => 'El archivo no es un pdf',
                ], 400);
            } else {
                $fileType = null;
            }
        }

        if (!$file = $request->file('documento')) {
            return null;
        }
        $file = $request->file('documento');
        $fileName = $file->getClientOriginalName();
        $file->storeAs('public', $fileName);
        $url = Storage::url('public/' . $fileName);
        return $url;
    }

    public function guardarFacturaUsuario(Request $request)
    {
        $maxSize = 1024 * 1024 * 5;
        $file = $request->file('url_factura');
        if ($file) {
            $fileSize = $file->getSize();
            if ($fileSize > $maxSize) {
                return response()->json([
                    'success' => false,
                    'message' => 'El archivo es muy grande',
                ], 400);
            } else {
                $fileSize = null;
            }

            $fileType = $file->getMimeType();
            if ($fileType != 'application/pdf') {
                return response()->json([
                    'success' => false,
                    'message' => 'El archivo no es un pdf',
                ], 400);
            } else {
                $fileType = null;
            }
        }

        if (!$file = $request->file('url_factura')) {
            return null;
        }
        $file = $request->file('url_factura');
        $fileName = $file->getClientOriginalName();
        $file->storeAs('public', $fileName);
        $url = Storage::url('public/' . $fileName);
        return $url;
    }



    public function CurrentToken(Request $request)
    {
        $user = User::where('objectId', $request->objectId)->first();
        $currentToken = $user->api_token;

        $storedToken = DB::table('users')
            ->where('objectId', $user->objectId)
            ->value('api_token');

        if ($currentToken == $storedToken) {
            return response()->json([
                'success' => true,
                'message' => 'Token actual',
                'api_token' => $user->api_token,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Token no actual',
            ], 401);
        }
    }

    public function InsertarFacturaUsuario(Request $request)
    {
        try {
            $user = User::where('objectId', $request->objectId)->first();
            $facturas = new Facturas();
            $subscripcion = new Suscripcion();



            DB::table('facturas_clientes')->insert([
                'fecha_factura' => date('Y-m-d H:i:s'),
                'mes_factura' => $request->mes_factura,
                'nombre_cliente' => $request->nombre_cliente,
                'user_invoice_objectId' =>  $request->user_invoice_objectId,
                'email_cliente' => $request->email_cliente,
                'nombre_factura' => $request->nombre_factura,
                'url_factura' => $this->guardarFacturaUsuario($request),

            ]);

            $subscripcion = Suscripcion::where('customer_id_object_subs', $request->user_invoice_objectId)->first();


            $subscripciones = DB::table('subscripciones')->select('id_suscripcion')->where('customer_id_object_subs', $request->user_invoice_objectId)->where('status_factura', 0)->pluck('id_suscripcion');
            if($subscripciones){
                foreach ($subscripciones as $subscripcion) {
                    DB::table('subscripciones')->where('id_suscripcion', $subscripcion)->update(['status_factura' => 1]);
                }
            }
            // $subscripcion->status_factura = 1;
            // $subscripcion->save();






            return response()->json([
                'success' => true,
                'message' => 'Factura agregada',
                'data' => $facturas,
                'datas' => $subscripcion,

            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Factura no agregada',
            ], 404);
        }
    }

    public function ObtenerFacturasUsuario(Request $request)
    {
        $user = User::where('objectId', $request->objectId)->first();
        $facturas = Facturas::where('user_invoice_objectId', $request->user_invoice_objectId)->get();
        return response()->json([
            'success' => true,
            'message' => 'Facturas encontradas',
            'data' => $facturas,
        ], 200);
    }

    public function ObtenerPagosUsuarios(Request $request)
    {
        $user = User::where('objectId', $request->objectId)->first();
        $pagos = Pagos::where('user_payment_objectId', $request->user_payment_objectId)->get();
        return response()->json([
            'success' => true,
            'message' => 'Pagos encontrados',
            'data' => $pagos,
        ], 200);
    }
}
