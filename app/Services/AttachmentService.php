<?php

namespace App\Services;

use App\Events\Attachment\AttachmentCreated;
use App\Events\Attachment\AttachmentDeleted;
use App\Exceptions\BusinessException;
use App\Models\Attachment;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\AttachmentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentService
{
    protected $attachmentRepo;

    public function __construct(AttachmentRepositoryInterface $attachmentRepo)
    {
        $this->attachmentRepo = $attachmentRepo;
    }
    
    public function getAttachments(Model $attachable)
    {
        return $this->attachmentRepo->getAttachments($attachable);
    }

    public function storeAttachment(Model $attachable, User $user, UploadedFile $file): Attachment
    {
        $originalName = $file->getClientOriginalName();
        $extension    = $file->getClientOriginalExtension();
        $storedName   = Str::uuid() . '.' . $extension;
        $path         = $file->storeAs('attachments', $storedName, 'public');

        $data = [
            'attachable_type' => get_class($attachable),
            'attachable_id'   => $attachable->id,
            'user_id'         => $user->id,
            'file_name'       => $originalName,
            'file_path'       => $path,
            'file_size'       => $file->getSize(),
            'mime_type'       => $file->getMimeType(),
        ];

        $attachment = $this->attachmentRepo->storeAttachment($data);
        // only broadcast for task attachments
        if ($attachable instanceof Task) {
            broadcast(new AttachmentCreated($attachable->id))->toOthers();
        }
        return $attachment;
    }

    public function deleteAttachment(Attachment $attachment, User $user): void
    {
        if ($attachment->user_id !== $user->id)
            throw new BusinessException('You are not authorized to delete this attachment.');

        $attachmentId  = $attachment->id;
        $attachableId  = $attachment->attachable_id;
        $attachableType = $attachment->attachable_type;
        
        Storage::disk('public')->delete($attachment->file_path);
        $this->attachmentRepo->deleteAttachment($attachment);

        if ($attachableType === Task::class) {
            broadcast(new AttachmentDeleted($attachableId))->toOthers();
        }
    }

    public function getCommentAttachment(Comment $comment, array $ids)
    {
        return $this->attachmentRepo->getCommentAttachment($comment, $ids);
    }
}