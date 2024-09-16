<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\AnnonceResource;
use App\Models\Annonce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AnnonceController extends Controller
{
    public function getAnnonces()
    {
        $annonces = Annonce::all();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'La liste de vos annonces',
            'annonces' => AnnonceResource::collection($annonces)
        ]);
    }
    public function index()
    {
        $annonces = Annonce::where('gp_id',Auth::id())->orderBy('id', 'DESC')->get();
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'La liste de vos annonces',
            'annonces' => AnnonceResource::collection($annonces)
        ]);
    }

    public function store(Request $request)
    {
        logger('user', [auth()->user()]);
        //dd(Auth::id());
        $validated  = Validator::make($request->all(),[
            'kilos_disponibles' => ['required', 'integer'],
            'date_depart' => ['required', 'date_format:Y-m-d\TH:i'],
            'date_arrivee' => ['required', 'date_format:Y-m-d\TH:i', 'after:date_depart'],
            'description' => ['required', 'string'],
            'origin' => ['required', 'string'],
            'destination' => ['required', 'string'],
            'prix_du_kilo' => ['required', 'integer']
        ]);
        logger('Validation', [$validated->fails()]);
        if ($validated->fails())
        {
            return response()->json([
               'status' => Response::HTTP_BAD_REQUEST,
               'message' => 'Bad Request',
               'errors' => $validated->errors()
            ]);
        }
        $annonce = Annonce::create([
            'gp_id' => Auth::id(),
            'kilos_disponibles' => $request->kilos_disponibles,
            'date_depart' => $request->date_depart,
            'date_arrivee' => $request->date_arrivee,
            'description' => $request->description,
            'origin' => $request->origin,
            'destination' => $request->destination,
            'prix_du_kilo' => $request->prix_du_kilo
        ]);

        return response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'Votre annonce a été bien publiée',
            'annonce' => new AnnonceResource($annonce)
         ]);
    }

    public function show($id)
    {
        $annonce = Annonce::findOrFail($id);
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Les détails de votre annonce',
            'annonce' => new AnnonceResource($annonce)
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated  = Validator::make($request->all(),[
            'kilos_disponibles' => ['required', 'integer'],
            'date_depart' => ['required', 'date_format:Y-m-d\TH:i'],
            'date_arrivee' => ['required', 'date_format:Y-m-d\TH:i', 'after:date_depart'],
            'description' => ['required', 'string'],
            'origin' => ['required', 'string'],
            'destination' => ['required', 'string'],
            'prix_du_kilo' => ['required', 'integer']
        ]);
        if ($validated->fails())
        {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Bad Request',
                'errors' => $validated->errors()
            ]);
        }
        $annonce = Annonce::findOrFail($id);
        $annonce->update([
            'gp_id' => Auth::id(),
            'kilos_disponibles' => $request->kilos_disponibles,
            'date_depart' => $request->date_depart,
            'date_arrivee' => $request->date_arrivee,
            'description' => $request->description,
            'origin' => $request->origin,
            'destination' => $request->destination,
            'prix_du_kilo' => $request->prix_du_kilo
        ]);
        return response()->json([
            'status' => Response::HTTP_CREATED,
            'message' => 'Votre annonce a été bien modifié',
            'annonce' => new AnnonceResource($annonce)
        ]);
    }

    public function destroy($id)
    {
        $annonce = Annonce::findOrFail($id);
        $annonce->delete();
        return response()->json([
            'status' => Response::HTTP_NO_CONTENT,
            'message' => 'Votre annonce a été bien supprimée',
        ]);
    }
}
