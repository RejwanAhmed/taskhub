<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('projects.{project_id}', function ($user, $projectId) {
    return $user->projects()->where('project_id', $projectId)->exists();
});