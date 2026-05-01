<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('blood-requests', function () {
    return true;
});

Broadcast::channel('donations', function () {
    return true;
});
