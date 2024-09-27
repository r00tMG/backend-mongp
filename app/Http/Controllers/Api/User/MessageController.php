<?php

namespace App\Http\Controllers\Api\User;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\MessageResource;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends Controller
{
    public function index(Request $request,$userId)
    {
        $emetteur_id = $request->emetteur_id;
        $recepteur_id = $request->recepteur_id;

        // valdiation

        $messages = Message::where(function($query) use ($userId) {
            $query->where('emetteur_id', Auth::id())
                ->orWhere('recepteur_id', Auth::id());
        })->where(function($query) use ($userId) {
            $query->where('emetteur_id', $userId)
                ->orWhere('recepteur_id', $userId);
        })->get();

        return response()->json([
            'status' => Response::HTTP_OK,
            'messages'=> MessageResource::collection($messages)
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
           'recepteur_id' => 'required|exists:users,id',
            'contenu'=>'required|string'
        ]);
        if($validator->fails())
        {
            return response()->json([
               'status' => Response::HTTP_BAD_REQUEST,
               'errors' => $validator->errors()
            ]);
        }
        try {

            $message = Message::create([
                'emetteur_id' => Auth::id(),
                'recepteur_id' => $request->recepteur_id,
                'contenu' => $request->contenu,
                'date_envoi' => Carbon::now()
            ]);
            logger('message create');

            broadcast(new MessageSent($message))->toOthers();
            logger('message sent');
            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message'=> new MessageResource($message)
            ]);
        }catch (\Exception $e){
            logger('Error sending message: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function getUnreadMessagesCount($userId)
    {
        $unreadMessagesCount = Message::where('recepteur_id', $userId)
            ->where('is_read', false)
            ->groupBy('emetteur_id')
            ->count();

        return response()->json([
            'unread_messages_count' => $unreadMessagesCount
        ]);
    }
}
