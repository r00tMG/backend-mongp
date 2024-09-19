<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\AnnonceResource;
use App\Models\Annonce;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $annonces = Annonce::where('date_depart', '>=', Carbon::now())->where('kilos_disponibles', '>=', 1)->orderBy('created_at', 'DESC')->get();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'La liste de vos annonces',
            'annonces' => AnnonceResource::collection($annonces)
        ]);
    }
}
