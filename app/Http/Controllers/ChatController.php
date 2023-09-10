<?php

namespace App\Http\Controllers;

use App\Events\Message;
use App\Events\PrivateSendMessage;
use App\Events\SendMessage;
use App\Jobs\MessageJob;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class ChatController extends Controller
{
    public function index(Request $request, User $user) {
        $chat = Chat::where([
            ['receiver_id' ,'=',$user->id],
            ['sender_id' ,'=',Auth::user()->id]
        ])->orWhere([
            ['receiver_id' ,'=',Auth::user()->id],
            ['sender_id' ,'=',$user->id]
        ])->get();
        return response()->json([
            'user'=>$user,
            'message'=> $chat
        ]);
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
        MessageJob::dispatch($dataMessage)->onQueue('message');
        // Chat::create($dataMessage);
        // broadcast(new PrivateSendMessage($dataMessage));

        // broadcast(new Message('arifin'));
        // event(new SendMessage($dataMessage));

        return response()->json([
            'message'=>'success send message'
        ],200   );
    }

}
