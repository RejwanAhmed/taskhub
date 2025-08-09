<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Services\CommentService;

class CommentController extends Controller
{
    protected CommentService $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }
}
