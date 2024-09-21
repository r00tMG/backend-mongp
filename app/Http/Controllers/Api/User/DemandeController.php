<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\AnnonceResource;
use App\Http\Resources\User\DemandeResource;
use App\Models\Annonce;
use App\Models\Demande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class DemandeController extends Controller
{
    public function index()
    {
        $demandes = Demande::where('user_id', auth()->id())->orWhereHas('annonce', function ($query) {
            $query->where('gp_id', auth()->id());
        })->with('annonce')->orderBy('created_at','DESC')->get();
            logger('demandes',['demandes'=>$demandes]);

        return response()->json(DemandeResource::collection($demandes));
    }

    public function show(string $id)
    {
        $demande = Demande::find($id);
        return \response()->json(new DemandeResource($demande));
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'annonce_id' => 'required|exists:annonces,id',
            'kilos_demandes' => 'required|integer|min:1',
        ]);
        if ($validator->fails())
        {
            return response()->json([
               'status' => Response::HTTP_BAD_REQUEST,
               'errors' => $validator->errors()
            ]);
        }
        $annonce = Annonce::find($request->annonce_id);

        if ($request->kilos_demandes > $annonce->kilos_disponibles) {
            return response()->json([
                'message' => 'Le kilo demandé dépasse le kilo disponible.',
            ], 400);
        }

        $demande = new Demande();
        $demande->annonce_id = $request->annonce_id;
        $demande->user_id = auth()->id();
        $demande->kilos_demandes = $request->kilos_demandes;
        $demande->prix_de_la_demande = $request->kilos_demandes * $annonce->prix_du_kilo;
        $demande->statut = 'en_attente';
        $demande->save();

        return response()->json([
            'message' => 'Demande créée avec succès.',
            'demande' => new DemandeResource($demande),
        ]);
    }
    public function update(Request $request, $id)
    {
        $demande = Demande::findOrFail($id);
        $request->validate(['statut' => 'required|in:confirmé,refusé']);

        if (auth()->id() !== $demande->annonce->gp_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $demande->statut = $request->statut;
        $demande->save();

        return response()->json($demande);
    }
    public function destroy($id)
    {
        $demande = Demande::findOrFail($id);
        if (auth()->id() !== $demande->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $demande->delete();
        return response()->json(['message' => 'Demande supprimée'], 200);
    }
}
