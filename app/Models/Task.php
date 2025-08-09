<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['project_id', 'creator_id', 'title', 'description', 'due_date', 'priority', 'status'];
}
