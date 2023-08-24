<?php

namespace App\Http\Controllers;

use App\Events\Message;
use App\Events\SendMessage;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class ChatController extends Controller
{
    public function index(Request $request) {

    }

    public function store(Request $request, User $user) {
        $request->validate([
            'message' => 'required'
        ]);
        $dataMessage = [
            'sender_id'=>Auth::user()->id,
            'receiver_id'=>$user->id,
            'message'=>$request->message
        ];

        // Chat::create($dataMessage);
        broadcast(new SendMessage($dataMessage));
        // broadcast(new Message('arifin'));
        // event(new SendMessage($dataMessage));
        // event(new SendMessage($dataMessage));

        return response()->json([
            'message'=>'success send message'
        ],200   );
    }

}
