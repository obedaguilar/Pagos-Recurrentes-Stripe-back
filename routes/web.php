<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
 */

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('/users', ['uses' => 'UserController@InsertarUser']);
$router->post('/upload', ['uses' => 'UserController@InsertarArchivos']);
//LOGIN Y LOGOUT DE USUARIO
$router->post('/login', ['uses' => 'UserController@login']);
$router->post('/logout', ['uses' => 'LogoutUserController@logout']);

//LOGIN Y LOGOUT DE ADMIN
$router->post('/loginAdmin', ['uses' => 'UserController@loginAdmin']);

//aca ponemos todo acerca del middleware
// $router->group(['middleware' => 'auth'], function () use ($router) {
$router->group(['middleware' => ['auth', 'cliente']], function () use ($router) {
    //obtenemos el usuario por id
    $router->get('/obtener/cliente/', ['uses' => 'UserController@ObtenerUserPorId']);
    //actualizar solamente el usuario
    $router->post('/user/actualizar/', ['uses' => 'UserController@ActualizarUsuarioUnico']);

    //obtener las facturas del usuario
    $router->get('/obtener/facturas/cliente/', ['uses' => 'UserController@ObtenerFacturasUsuario']);
});
// });
//RUTAS PARA ADMIN
$router->group(['middleware' => ['auth', 'admin']], function () use ($router) {
    //obtenemos el usuario por id
    $router->get('/obtener/admin/', ['uses' => 'UserController@ObtenerUserPorId']);
    // obtener todos los usuarios
    $router->get('/obtener/registros/clientes', ['uses' => 'UserController@ObtenerUsers']);

    //eliminar el usuario por id
    $router->post('/eliminar/usuario/', ['uses' => 'UserController@EliminarUserUpdate']);
    //  ['uses' => 'AdminController@index']

    //actualizar el usuario por id
    $router->post('/editar/usuarios/', ['uses' => 'UserController@ActualizarUsers']);

    //insertar facturas de los clientes
    $router->post('/insertar/facturas', ['uses' => 'UserController@InsertarFacturaUsuario']);


//recuperar suscripcion de base de datos
$router->get( '/stripe/obtener/subscriptionBD/', 'StripePaymentController@getSubscription');

});
$router->delete('/eliminar/usuario/total/{objectId}', ['uses' => 'UserController@EliminarUserCompleto']);




$router->post('/obtener/token', ['uses' => 'UserController@CurrentToken']);

$router->post('stripe', 'StripePaymentController@stripePost');

// $router->post('stripe/create/customer', ['uses' => 'UserController@createCustomerStripe']);

$router->post('stripe/add/payment', 'StripePaymentController@addMethodPaymentStripe');

$router->post('stripe/test/payment', 'StripePaymentController@TestPaymentStripe');

$router->post('stripe/charge/plan/', 'StripePaymentController@createPlanStripe');

$router->post('stripe/charge/subscription/', 'StripePaymentController@createSubscriptionStripe');


$router->get( '/stripe/obtener/customer/', 'StripePaymentController@retrieveCustomer');

$router->get( '/stripe/obtener/card/', 'StripePaymentController@retrieveMethodPaymentStripe');

//recuperar plan
$router->get( '/stripe/obtener/plan/', 'StripePaymentController@retrievePlanBD');

//recuperar metodos de pago
$router->get( '/stripe/obtener/payment/', 'StripePaymentController@retrieveCards');


//recuperar suscripcion
$router->get( '/stripe/obtener/subscription/', 'StripePaymentController@retrieveSubscription');


//actualizar password
$router->post('/actualizar/password', ['uses' => 'UserController@updatePassword']);


//cancelar subscription usuario
$router->post('/stripe/cancel/subscription/', 'StripePaymentController@cancelSubscription');

