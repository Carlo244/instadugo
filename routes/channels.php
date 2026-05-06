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

// Hospital-scoped channels (private to a specific hospital admin)
Broadcast::channel('blood-requests.{hospitalId}', function ($user, $hospitalId) {
    // Allow hospital admins to listen to their hospital channel, or allow system-level listeners as needed
    // If using multiple guards, ensure the hospital admin guard is available for socket auth
    if (isset($user->id) && isset($user->hospital_admins)) {
        return (int) $user->id === (int) $hospitalId;
    }
    // Fallback: allow if the channel is publicly accessible (for internal dashboards)
    return true;
});

Broadcast::channel('donations.{hospitalId}', function ($user, $hospitalId) {
    if (isset($user->id) && isset($user->hospital_admins)) {
        return (int) $user->id === (int) $hospitalId;
    }
    return true;
});
