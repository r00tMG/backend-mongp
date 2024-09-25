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
    public function index($userId)
    {
        $messages = Message::where(function($query) use ($userId) {
            $query->where('emetteur_id', Auth::id())
                ->orWhere('recepteur_id', Auth::id());
        })->where(function($query) use ($userId) {
            $query->where('emetteur_id', $userId)
                ->orWhere('recepteur_id', $userId);
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

    /*public function index(Request $request,$recepteurId)
    {
        $userId = auth()->id();
        $messages = Message::where(function($query) use ($userId, $recepteurId) {
            $query->where('emetteur_id', $userId)
                ->where('recepteur_id', $recepteurId);
        })->orWhere(function($query) use ($userId, $recepteurId) {
            $query->where('emetteur_id', $recepteurId)
                ->where('recepteur_id', $userId);
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);

    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'recepteur_id' => 'required|exists:users,id',
            'contenu' => 'required|string',
        ]);

        try {
            $emetteur = auth()->user();
            $recepteur = $validatedData['recepteur_id'];
            $messageContent = $validatedData['contenu'];

            $message = new Message();
            $message->emetteur_id = $emetteur->id;
            $message->recepteur_id = $recepteur;
            $message->message = $messageContent;
            $message->save();

            event(new MessageSent($emetteur->name, $messageContent));

            return response()->json(['status' => 'Message sent successfully'], 200);
        } catch (\Exception $e) {
            logger('Error sending message: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }*/
}
