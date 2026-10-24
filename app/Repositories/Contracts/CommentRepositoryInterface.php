<?php

namespace App\Repositories\Contracts;

use App\Models\Comment;
use App\Models\Task;

interface CommentRepositoryInterface
{
    // Define your methods here
    public function getComments(Task $task);
    public function createComment(array $data);
    public function updateComment(Comment $comment, string $content);
    public function deleteComment(Comment $comment);
}
