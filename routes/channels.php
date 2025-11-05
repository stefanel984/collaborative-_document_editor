<?php

use Illuminate\Support\Facades\Broadcast;

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


Broadcast::channel('document.{id}', function ($user, $id) {
    return ['id' => $user->id, 'name' => $user->name];
});

Broadcast::channel('presence-documents.{documentId}', function ($user, $documentId) {
    // ensure user is authorized for document
    return \App\Models\Document::where('id', $documentId)->where('owner_id', $user->id)->exists()
         ? ['id' => $user->id, 'name' => $user->name]
         : false;
});
