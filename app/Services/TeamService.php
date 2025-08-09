<?php 

namespace App\Services;

use App\Models\Team;
use App\Services\Core\BaseModelService;

class TeamService extends BaseModelService
{
    public function model(): string
    {
        return Team::class;
    }
}