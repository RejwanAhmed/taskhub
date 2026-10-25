<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\CommentRepositoryInterface;
use App\Services\AttachmentService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CommentService
{
    protected $commentRepo;
    protected $attachmentService;

    public function __construct(CommentRepositoryInterface $commentRepo, AttachmentService $attachmentService)
    {
        $this->commentRepo = $commentRepo;
        $this->attachmentService = $attachmentService;
    }
    
    public function getComments(Task $task): Collection
    {
        return $this->commentRepo->getComments($task);
    }

    public function createComment(Task $task, User $user, array $data): Comment
    {
        return DB::transaction(function () use ($task, $user, $data) {
            $data = array_merge($data, [
                'commentable_type' => Task::class,
                'commentable_id'   => $task->id,
                'user_id'          => $user->id,
                'content'          => $data['content'] ?? '',
            ]);

            $comment = $this->commentRepo->createComment($data);

            $files = $data['files'] ?? [];
            foreach ($files as $file) {
                $this->attachmentService->storeAttachment($comment, $user, $file);
            }
            
            return $comment;
        });
        
    }

    public function updateComment(Comment $comment, User $user, array $data): void
    {
        DB::transaction(function () use ($comment, $user, $data) {
            if ($comment->user_id !== $user->id)
                throw new BusinessException('You are not authorized to update this comment.');
            
            $existingCount  = $comment->attachments()->count();
            $removedCount   = count($data['removed_attachment_ids'] ?? []);
            $newCount       = count($data['new_files'] ?? []);
            $totalAfterUpdate = $existingCount - $removedCount + $newCount;

            if ($totalAfterUpdate > 5)
                throw new BusinessException('Total attachments per comment cannot exceed 5.');

            $content = $data['content'] ?? '';
            $this->commentRepo->updateComment($comment, $content);

            // delete removed attachments
            if (!empty($data['removed_attachment_ids'])) {
                $attachments = $this->attachmentService->getCommentAttachment($comment, $data['removed_attachment_ids']);

                foreach ($attachments as $attachment) {
                    $this->attachmentService->deleteAttachment($attachment, $user);
                }
            }

            // store new files
            $newFiles = $data['new_files'] ?? [];
            foreach ($newFiles as $file) {
                $this->attachmentService->storeAttachment($comment, $user, $file);
            }
        });
    }

    public function deleteComment(Comment $comment, User $user): void
    {
        if ($comment->user_id !== $user->id)
            throw new BusinessException('You are not authorized to delete this comment.');

        $this->commentRepo->deleteComment($comment);
    }
}