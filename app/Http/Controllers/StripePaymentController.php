<?php

namespace App\Http\Controllers;

use App\Models\Cards;
use App\Models\Planes;
use App\Models\Suscripcion;
use Exception;
use Illuminate\Http\Request;
use Stripe;
use Stripe\Charge;
use App\Models\User;
use Dotenv\Repository\Adapter\WriterInterface;
use Stripe\Customer;
use Stripe\Plan;
use Stripe\Token;
use Stripe\Source;

class StripePaymentController extends Controller
{
    //Write code for API integration
    public function stripePost(Request $request)
    {
        try {
            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET')
            );
            $response = $stripe->tokens->create([
                'card' => [
                    'number' => $request->number,
                    'exp_month' => $request->exp_month,
                    'exp_year' => $request->exp_year,
                    'cvc' => $request->cvc,
                ],
            ]);

            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET')
            );
            $charge =  $stripe->charges->create([
                'amount' => $request->amount,
                'currency' => 'mxn',
                'source' => $response->id,
                'description' => $request->description,
            ]);

            return response()->json(['status' => $charge->status], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function addMethodPaymentStripe(Request $request)
    {
        //Acá se agrega el metodo de pago a stripe y se guarda el id del metodo de pago en la base de datos
        //El front es el que debe enviar el id de la card al back
        try {
            // Obtener los datos del usuario a partir de los $request
            $user = User::where('email', $request->email)->first();

            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET')
            );
            // $response = $stripe->tokens->create([
            //     'card' => [
            //         'number' => $request->number,
            //         'exp_month' => $request->exp_month,
            //         'exp_year' => $request->exp_year,
            //         'cvc' => $request->cvc,
            //     ],
            // ]);

            // $response = $stripe->paymentMethods->create([
            //     'type' => 'card',
            //     'card' => [
            //         'number' => $request->number,
            //         'exp_month' => $request->exp_month,
            //         'exp_year' => $request->exp_year,
            //         'cvc' => $request->cvc,
            //     ],
            // ]);
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            //se recupera el id del cliente de stripe
            // $stripe_customer_id = $user->stripe_customer_id;
            // $retrieveCustomer = $stripe->customers->retrieve(
            //     $user->stripe_customer_id
            // );
            $retrieveCustomer =   $this->retrieveCustomer($request);

            //se crea el método de pago
            $attach = $stripe->paymentMethods->attach(
                $request->id,
                ['customer' =>  $retrieveCustomer->id]
            );

            //se recupera el método de pago
            $retrivePm = $this->retrieveMethodPaymentStripe($request);

            //guarda pocos datos de la tarjeta en la base de datos
            $cards = Cards::create([
                'metodo_pago_id' => $retrivePm->id,
                'description_payment' => $retrivePm->card->brand,
                'tipo_card' => $retrivePm->card->funding,
                'country_stripe' => $retrivePm->card->country,
                'exp_month' => $retrivePm->card->exp_month,
                'exp_year' => $retrivePm->card->exp_year,
                'cuatro_digitos' => $retrivePm->card->last4,
                'fecha_creacion' => $retrivePm->created,
                'customer_id_object' =>  $user->objectId,
            ]);

            //se lista los métodos de pago
            $listar = $stripe->customers->allSources(
                $retrieveCustomer->id,
                ['object' => 'card']
            );
            // $customer->sources->create(['source' => $response->id]);
            return response()->json([
                'attach' => $attach,
                'listar' => $listar,
                'user' => $retrieveCustomer->id,
                'retrivePm' => $retrivePm,
                // 'response' => $response, no se necesita
                'status' => 'success'
            ], 200);
        } catch (Exception $e) {
            // Manejar la excepción
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function retrieveMethodPaymentStripe(Request $request)
    {
        try {
            // Obtener los datos del usuario a partir de los $request

            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET')
            );

            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            return $stripe->paymentMethods->retrieve(
                $request->id
            );
        } catch (Exception $e) {
            // Manejar la excepción
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function retrieveCustomer(Request $request)
    {
        try {

            $user = User::where('email', $request->email)->first();

            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET')
            );

            return $stripe->customers->retrieve(
                $user->stripe_customer_id
            );
        } catch (\Throwable $th) {
            //throw $th;
        }
    }


    public function TestPaymentStripe(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET')
            );


            $paymentMethod = $stripe->paymentMethods->retrieve(
                $request->cardId,
                []
            );
            $price = 100;

            $intent = $stripe->paymentIntents->create([
                'amount' => $price * 100,
                'currency' => 'mxn',
                'payment_method_types' => ['card'],
                'customer' => $user->stripe_customer_id,
                'payment_method' => $paymentMethod->id,
                // 'metadata' => [
                //     'data' => json_encode($metadata)
                // ],
                // 'description' => $service->name
            ]);

            $paymentIntent = $stripe->paymentIntents->retrieve(
                $intent->id
            );


            if ($intent->status === 'requires_confirmation') {
                $intent->confirm();
            }



            if ($intent->status == 'succeeded') {
                return response()->json([
                    'intent' => $intent,
                    'status' => 'success'
                ], 200);
            }

            return response()->json([
                'intent' => $intent,
                'status' => 'success'
            ], 200);
        } catch (Exception $e) {
            // Manejar la excepción
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function createPlanStripe(Request $request)
    {

        try {
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET')
            );
            // Stripe\Stripe::setApiKey(config('stripe.secret'));


            $plan = $stripe->plans->create([
                'amount' => $request->amount * 100,
                'currency' => 'mxn',
                'interval' => $request->interval,
                'product' => [
                    'name' => $request->name,
                ],
            ]);

            $planId = $stripe->plans->retrieve(
                $plan->id,
                []
            );


            $guardarPlan = Planes::create([
                'name' => $request->name,
                'stripe_id' => $plan->id,
                'currency' => 'mxn',
                'amount' => $request->amount,
                'interval' => $request->interval,
                'product' => $request->name,
                'status' => 1,
            ]);


            return response()->json([
                'plan' => $plan,
                // 'SavePlan' => $guardarPlan,
                'status' => 'success'
            ], 200);
        } catch (Exception $e) {
            // Manejar la excepción
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    function retrievePlanBD(Request $request)
    {
        try {
            $plan = Planes::where('interval', $request->interval)->first();

            $planId = $plan->stripe_id;
            if ($plan == null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El plan no existe'
                ], 400);
            }
            return response()->json([
                'plan' => $plan,
                'planId' => $planId,
                'status' => 'success'
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }



    public function createSubscriptionStripe(Request $request)
    {
        try {
            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET')
            );
            $user = User::where('email', $request->email)->first();
            $plan = $stripe->plans->retrieve(
                $request->id,
                []
            );

            $plan = Planes::where('stripe_id', $request->id)->first();
            if ($plan == null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El plan no existe'
                ], 400);
            }

            $subscription = $stripe->subscriptions->create([
                'customer' => $user->stripe_customer_id,
                'items' => [
                    ['price' => $plan->stripe_id],
                ],
                'default_payment_method' => $request->cardId,
            ]);

            $MySubscription = Suscripcion::create([
                'id_suscripcion' => $subscription->id,
                'title_suscripcion' => $subscription->collection_method,
                'customer' => $subscription->customer,
                'status_factura' => false,
                'customer_email' => $user->email,
                'customer_id_object_subs' => $user->objectId,
            ]);



            // $subscription = $stripe->subscriptions->create([
            //     'customer' => 'cus_NR8bVqaYcSfGP2',
            //     'items' => [
            //         ['price' => $plan->stripe_id],
            //     ],
            //     'default_payment_method' => 'pm_1MgH6FIYEen6sDjxBxN6zE45',
            // ]);
            //     $subscription->latest_invoice,
            //     []
            // );

            // if($invoice->status == 'open'){
            //     $invoice->pay();
            // }
            return response()->json([
                'Mi plan' => $plan,
                'Suscripcion' => $subscription,
                'MySubscription' => $MySubscription,
                'status' => 'success'
            ], 200);
        } catch (Exception $e) {
            // Manejar la excepción
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }



    function addCards(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET')
            );

            //Recupera card de stripe
            $paymentMethod = $stripe->paymentMethods->retrieve(
                $request->cardId,
                []
            );
            $price = 100;

            //se crea el intento de pago con la tarjeta de stripe
            $intent = $stripe->paymentIntents->create([
                'amount' => $price * 100,
                'currency' => 'mxn',
                'payment_method_types' => ['card'],
                'customer' => $user->stripe_customer_id,
                'payment_method' => $paymentMethod->id,
                // 'metadata' => [
                //     'data' => json_encode($metadata)
                // ],
                // 'description' => $service->name
            ]);

            //se recupera el intento de pago
            $paymentIntent = $stripe->paymentIntents->retrieve(
                $intent->id
            );
        } catch (Exception $e) {
            // Manejar la excepción
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function retrieveCards(Request $request)
    {


        $user = User::where('email', $request->email)->first();

        $stripe = new \Stripe\StripeClient(
            env('STRIPE_SECRET')
        );

        $cards = $stripe->paymentMethods->all([
            'customer' => $user->stripe_customer_id,
            'type' => 'card',
        ]);

        return response()->json([
            'cards' => $cards,
            'status' => 'success'
        ], 200);
    }

    public function retrieveSubscription(REQUEST $request)
    {

        // $user = User::where('email', $request->email)->first();
        $subscription = Suscripcion::where('customer_email', $request->customer_email)->first();
        $user = User::where('email', $request->customer_email)->first();

        $stripe = new \Stripe\StripeClient(
            env('STRIPE_SECRET')
        );

        $subscription = $stripe->subscriptions->retrieve(
            $subscription->id_suscripcion,
            []
        );

        $allSubscription = $stripe->subscriptions->all([
            'customer' => $user->stripe_customer_id,
            'status' => 'active',
            'limit' => 1,
        ]);
        // if ($allSubscription->total_count > 0) {
        //     $subscription = $allSubscription->data[0];
        //     return response()->json($subscription);
        // } else {
        //     return response()->json(['error' => 'No active subscription found'], 404);
        // }

        return response()->json([
            'subscription' => $subscription,
            'allSubscription' => $allSubscription,
            'status' => 'success'
        ], 200);
    }

    public function updateSubscription(Request $request){
        try {
            $user = User::where('email', $request->email)->first();

            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET')
            );

            $subscription = $stripe->subscriptions->retrieve(
                $request->id,
                []
            );

            $subscription = $stripe->subscriptions->update(
                $request->id,
                [
                    'cancel_at_period_end' => false,
                ]
            );

            return response()->json([
                'subscription' => $subscription,
                'status' => 'success'
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
        }

    }

    public function getSubscription(Request $request){


        // $user = User::where('email', $request->email)->first();


        $suscripcion = Suscripcion::all();

        $stripe = new \Stripe\StripeClient(
            env('STRIPE_SECRET')
        );





        return response()->json([
            'suscripcion' => $suscripcion,
            'status' => 'success'
        ], 200);

    }

    public function cancelSubscription(Request $request){
        try {
            $user = User::where('email', $request->email)->first();

            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET')
            );

            $subscription = $stripe->subscriptions->cancel(
                $request->id,
                []
            );

            $subscription = $stripe->subscriptions->update(
                $request->id,
                [
                    'cancel_at_period_end' => true,
                ]
            );

            return response()->json([
                'subscription' => $subscription,
                'status' => 'success'
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
        }

    }
}
