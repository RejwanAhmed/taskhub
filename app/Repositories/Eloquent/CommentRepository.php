<?php

namespace App\Repositories\Eloquent;

use App\Models\Comment;
use App\Models\Task;
use App\Repositories\Contracts\CommentRepositoryInterface;

class CommentRepository implements CommentRepositoryInterface
{
    // Implement methods here
    protected $model;

    public function __construct(Comment $model)
    {
        $this->model = $model;
    }

    public function getComments(Task $task)
    {
        return $task->comments()->with('user:id,name')->get();
    }

    public function createComment(array $data)
    {
        return $this->model::create($data);
    }

    public function updateComment(Comment $comment, string $content)
    {
        $comment->update(['content' => $content]);
    }

    public function deleteComment(Comment $comment)
    {
        $comment->delete();
    }
}
