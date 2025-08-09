<?php 

namespace App\Services;

use App\Models\Comment;
use App\Services\Core\BaseModelService;

class CommentService extends BaseModelService
{
    public function model(): string
    {
        return Comment::class;
    }
}