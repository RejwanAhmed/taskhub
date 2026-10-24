<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\CommentRepositoryInterface;
use Illuminate\Support\Collection;

class CommentService
{
    protected $commentRepo;

    public function __construct(CommentRepositoryInterface $commentRepo)
    {
        $this->commentRepo = $commentRepo;
    }
    
    public function getComments(Task $task): Collection
    {
        return $this->commentRepo->getComments($task);
    }

    public function createComment(Task $task, User $user, array $data): Comment
    {
        $data = array_merge($data, [
            'commentable_type' => Task::class,
            'commentable_id'   => $task->id,
            'user_id'          => $user->id,
        ]);
        return $this->commentRepo->createComment($data);
    }

    public function updateComment(Comment $comment, User $user, string $content): void
    {
        if ($comment->user_id !== $user->id)
            throw new BusinessException('You are not authorized to update this comment.');

        $this->commentRepo->updateComment($comment, $content);
    }

    public function deleteComment(Comment $comment, User $user): void
    {
        if ($comment->user_id !== $user->id)
            throw new BusinessException('You are not authorized to delete this comment.');

        $this->commentRepo->deleteComment($comment);
    }
}