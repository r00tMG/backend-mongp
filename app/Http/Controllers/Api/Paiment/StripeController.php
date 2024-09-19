<?php

namespace App\Http\Controllers\Api\Paiment;

use App\Http\Controllers\Controller;
use App\Models\Demande;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripeController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        $demande = Demande::find($request->demande_id);

        if (!$demande) {
            logger('Demande non trouvée', ['demande_id' => $request->demande_id]); // Correct: le contexte est un tableau
            return response()->json(['error' => 'Demande non trouvée'], 404);
        }

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $checkoutSession = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Paiement pour la demande #' . $demande->id,
                        ],
                        'unit_amount' => $demande->montant * 100, // En cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => url('/success'),
                'cancel_url' => url('/cancel'),
            ]);

            return response()->json(['id' => $checkoutSession->id]);

        } catch (\Exception $e) {
            logger('Erreur lors de la création de la session Stripe', [
                'error' => $e->getMessage(),
                'demande' => $demande->toArray(),
            ]);
            return response()->json(['error' => 'Erreur lors de la création de la session'], 500);
        }
    }
}
