<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('messageChat-{id}', function ($user,$id) {
    Log::info(json_encode($user));
    Log::info(json_encode($id));
    return true;
});
// Broadcast::channel('messageChat-12', function ($user,$id) {
//     Log::info(json_encode($user));
//     Log::info(json_encode($id));
//     return true;
// });
