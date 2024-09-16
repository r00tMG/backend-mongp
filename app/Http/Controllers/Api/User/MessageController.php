<?php

namespace App\Http\Controllers\Api\User;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index($id)
    {
        $messages = Message::where(function($query) use ($id) {
            $query->where('emetteur_id', Auth::id())
                ->orWhere('recepteur_id', Auth::id());
        })->where(function($query) use ($id) {
            $query->where('emetteur_id', $id)
                ->orWhere('recepteur_id', $id);
        })->get();

        return response()->json($messages);
    }

    public function store(Request $request)
    {
        $message = Message::create([
            'emetteur_id' => Auth::id(),
            'recepteur_id' => $request->recepteur_id,
            'contenu' => $request->contenu,
            'date_envoi' => Carbon::now()
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }
}
