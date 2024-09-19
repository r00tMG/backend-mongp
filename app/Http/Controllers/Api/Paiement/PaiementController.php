<?php

namespace App\Http\Controllers\Api\Paiement;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\OrderResource;
use App\Mail\SendOrderMail;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Response;

class PaiementController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $paymentIntent = PaymentIntent::create([
            'amount' => $request->amount * 100,
            'currency' => 'MAD',
        ]);

        return response()->json([
            'clientSecret' => $paymentIntent->client_secret,
        ]);
    }

    public function storeOrder(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'payment_intent_id' => ['required','string'],
            'total' => ['required','integer'],
            'demande_id' => ['required', 'integer'],
            'email' => ['required', 'email']
        ]);
        logger($validator->getData());
        if ($validator->fails())
        {
            return \response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'errors' => $validator->errors()
            ]);
        }
        $order = new Order();
        $order->payment_intent_id = $request->payment_intent_id;
        $order->demande_id = $request->demande_id;
        $order->total = $request->total;
        $order->user_id = Auth::id();
        $order->status = 'paid';
        $order->payment_status = 'succeeded';
        $order->paid_at = now();
        $order->email=$request->email;
        $order->save();
       /* Mail::send(new SendOrderMail(
            $order,
            $validator->getData()
        ));*/
        logger('email', ['email' => $order['demande']['user']['email']]);

        return response()->json([
           'status' => Response::HTTP_CREATED,
           'order' => new OrderResource($order)
        ]);

    }
    public function generateInvoice($orderId)
    {
        $order = Order::with('demande')->findOrFail($orderId);
            logger('order',['order'=>$order]);
        $pdf = PDF::loadView('invoice.invoice', ['order'=>new OrderResource($order)]);
        logger('pdf',['pdf'=>$pdf]);

        //return $pdf->download('facture_' . $order->id . '.pdf');
        $filePath = storage_path("app/public/invoices/facture_{$order->id}.pdf");
        $pdf->save($filePath);

        return response()->json([
            'invoice_url' => asset("storage/invoices/facture_{$order->id}.pdf")
        ]);
    }
}
