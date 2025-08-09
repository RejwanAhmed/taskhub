<?php

namespace App\Http\Controllers;

use App\Services\TeamService;

class TeamController extends Controller
{
    protected TeamService $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }
}
