<?php

namespace App\Http\Controllers\Api\Paiement;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        $payload = @file_get_contents('php://input');
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Traiter les différents types d'événements
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentSuccess($paymentIntent);
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentFailure($paymentIntent);
                break;

            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                $this->handleInvoicePaymentSuccess($invoice);
                break;

            case 'invoice.payment_failed':
                $invoice = $event->data->object;
                $this->handleInvoicePaymentFailure($invoice);
                break;
            case 'charge.updated':
                $charge = $event->data->object;
                $this->handleChargeUpdated($charge);
                break;
            default:
                logger('Webhook non traité : ' . $event->type);
        }

        return response()->json(['status' => 'success'], 200);
    }

    protected function handlePaymentSuccess($paymentIntent)
    {
        $order = Order::where('payment_intent_id', $paymentIntent->id)->first();
        logger('Paiement réussi pour la commande : ' . $order->id);

        if (!$order) {
            Log::error("Commande non trouvée pour le PaymentIntent : " . $paymentIntent->id);
            return;
        }
        $order->status = 'paid';
        $order->payment_status = 'succeeded';
        $order->paid_at = now();
        $order->save();

        $this->sendPaymentConfirmationEmail($order);

        logger('Paiement réussi pour la commande : ' . $order->id);
    }
    protected function sendPaymentConfirmationEmail($order)
    {
        $user = $order->user;

        $user->notify(new \App\Notifications\PaymentConfirmation($order));
        logger('Email de confirmation envoyé à l’utilisateur : ' . $user->email);
    }

    protected function handlePaymentFailure($paymentIntent)
    {
        // Logique lorsque le paiement échoue
        Log::error('Échec du paiement : ' . $paymentIntent->id);
        // Mettre à jour la commande en base de données, notifier l'utilisateur, etc.
    }

    protected function handleInvoicePaymentSuccess($invoice)
    {
        // Logique lorsque le paiement de la facture réussit
        Log::info('Paiement de facture réussi : ' . $invoice->id);
        $order = Order::where('payment_id', $invoice['data']['object']['id'])->firstOrFail();

        // Mettre à jour le statut de la commande
        $order->update([
            'payment_status' => 'succeeded',
            'status' => 'completed',
        ]);

        // Générer et envoyer la facture par email
        $invoiceUrl = route('invoice.download', ['id' => $order->id]);

        Mail::to($order->user->email)->send(new InvoiceMail($order, $invoiceUrl));
    }

    protected function handleInvoicePaymentFailure($invoice)
    {
        // Logique lorsque le paiement de la facture échoue
        Log::error('Échec du paiement de facture : ' . $invoice->id);
        // Suspendre l'abonnement, notifier l'utilisateur, etc.
    }
    protected function handleChargeUpdated($charge)
    {
        Log::info('Mise à jour de la charge : ' . $charge->id);

        // Exemple : Mettre à jour une commande ou un paiement dans la base de données
        // Logique de traitement de la mise à jour du paiement
        // Tu peux récupérer et traiter des informations supplémentaires sur la charge ici
        // $charge->amount, $charge->status, etc.

        // Exemple d'accès aux données :
        Log::info('Montant de la charge : ' . $charge->amount);
        Log::info('Statut de la charge : ' . $charge->status);

        // Ajoute ici les actions à réaliser en fonction de la mise à jour
        // Par exemple : mettre à jour la commande liée à ce paiement
    }
}
