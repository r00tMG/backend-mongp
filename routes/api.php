<?php

use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Paiement\WebhookController;
use App\Http\Controllers\Api\User\MessageController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

# Public Endpoint
Route::post('register',[RegisteredUserController::class,'store']);
Route::post('login',[RegisteredUserController::class,'login']);
Route::get('getRoles',[RoleController::class,'getRoles']);
Route::get('annonce_on_home', [\App\Http\Controllers\Api\HomeController::class, 'index']);

# Private Endpoint
#Route::get('getUsers',[UserController::class,'getUsers']);

Route::group(['middleware' => ['auth:sanctum']], function (){
    Route::apiResource('users',UserController::class);

    Route::apiResource('roles',RoleController::class);

    Route::get('permissions',[\App\Http\Controllers\PermissionController::class,'index']);

    Route::apiResource('profiles',\App\Http\Controllers\Api\User\ProfileController::class);

    Route::apiResource('annonces', \App\Http\Controllers\Api\User\AnnonceController::class);

    Route::get('/messages/{id}', [MessageController::class, 'index']);
    Route::post('/messages', [MessageController::class, 'store']);

    Route::apiResource('demandes',\App\Http\Controllers\Api\User\DemandeController::class);

    Route::post('/payment-intent', [\App\Http\Controllers\Api\Paiement\PaiementController::class, 'createPaymentIntent']);
    Route::post('create/orders',[\App\Http\Controllers\Api\Paiement\PaiementController::class,'storeOrder']);

    Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook']);
    Route::post('create-checkout-session', [\App\Http\Controllers\Api\Paiment\StripeController::class, 'createCheckoutSession']);

    Route::get('/invoice/{orderId}', [\App\Http\Controllers\Api\Paiement\PaiementController::class, 'generateInvoice']);

});
Route::post('webhook/payment/succeeded',function (\Illuminate\Http\Request $request){
return 'ok';
} );
/*Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});*/
