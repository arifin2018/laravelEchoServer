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
use Illuminate\Support\Str;

class ChatController extends Controller
{
    private $image = '';

    public function index(Request $request, User $user) {
        $chat = Chat::where([
            ['receiver_id' ,'=',$user->id],
            ['sender_id' ,'=',Auth::user()->id]
        ])->orWhere([
            ['receiver_id' ,'=',Auth::user()->id],
            ['sender_id' ,'=',$user->id]
        ])->get();
        foreach ($chat as $key => $value) {
            if ($value->type == 'image') {
                $expiresAt = new \DateTime('+3 months');
                $imageReference = app('firebase.storage')->getBucket()->object($value->message);
                $image = $imageReference->signedUrl($expiresAt);
                $value['message'] = $image;
            }else{
                $value['message'] = $value->message;
            }
        }
        return response()->json([
            'user'=>$user,
            'message'=> $chat
        ]);
    }

    public function store(Request $request, User $user) {
        $request->validate([
            'message' => 'required',
            'type' => 'required',
        ]);
        dd('a');
        $resultRequest = $request->all();
        if ($request->file('message') != null) {
            $resultRequest['message'] = $this->reqImage($request)['message'];
            $request['type'] = 'image';
        }

        $dataMessage = [
            'sender_id'=>Auth::user()->id,
            'receiver_id'=>$user->id,
            'message'=>$resultRequest['message'],
            'type'=>$resultRequest['type']
        ];
        MessageJob::dispatch($dataMessage)->onQueue('message');

        return response()->json([
            'message'=>'success send message'
        ],200);
    }

    public function reqImage(Request $request):array {
        $image = $request->file('message');
        $local_storage_path = 'Chat/';
        $name = rand(0, 999).'-'.pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME) . '-' .Str::random(4);

        $localfolder = public_path('firebase-temp-uploads') .'/';
        $extension = $image->getClientOriginalExtension();
        $file = $name. '.' . $extension;
        $Imove = $image->move($localfolder, $file);
        if ($Imove) {
            $uploadedfile = fopen($localfolder.$file, 'r');
            app('firebase.storage')->getBucket()->upload($uploadedfile, ['name' => $local_storage_path . $file]);
            unlink($localfolder . $file);
            $this->image = $local_storage_path . $file;
            return [
                'result'=>true,
                'message'=>$local_storage_path . $file
            ];
        } else {
            return [
                'result'=>false,
                'message'=>$local_storage_path . $file
            ];
        }
    }

}
