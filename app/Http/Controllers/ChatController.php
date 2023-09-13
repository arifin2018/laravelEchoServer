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
        if ($request->file('image') != null) {
            $request['message'] = $this->reqImage($request)['message'];
        }
        $request->validate([
            'message' => 'required',
        ]);
        $dataMessage = [
            'sender_id'=>Auth::user()->id,
            'receiver_id'=>$user->id,
            'message'=>$request->message
        ];
        MessageJob::dispatch($dataMessage)->onQueue('message');

        return response()->json([
            'message'=>'success send message'
        ],200);
    }

    public function reqImage(Request $request):array {
        $image = $request->file('image');
        $local_storage_path = 'Chat/';
        $name = $image->getClientOriginalName();
        $localfolder = public_path('firebase-temp-uploads') .'/';
        $extension = $image->getClientOriginalExtension();
        $file      = $name. '.' . $extension;
        $Imove = $image->move($localfolder, $file);
        if ($Imove) {
            $uploadedfile = fopen($localfolder.$file, 'r');
            app('firebase.storage')->getBucket()->upload($uploadedfile, ['name' => $local_storage_path . $name]);
            unlink($localfolder . $file);
            return [
                'result'=>true,
                'message'=>$local_storage_path . $name
            ];
        } else {
            return [
                'result'=>false,
                'message'=>$local_storage_path . $name
            ];
        }
    }

}
