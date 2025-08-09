<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['team_id', 'name', 'description', 'start_date', 'end_date', 'status'];
}
