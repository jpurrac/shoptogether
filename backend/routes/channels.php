<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Solo miembros de la lista pueden suscribirse
Broadcast::channel('list.{listId}', function ($user, $listId) {
    $list = \App\Models\ShoppingList::find($listId);
    return $list && $list->hasMember($user);
});
