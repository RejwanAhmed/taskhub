<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Attachment;
use App\Models\Comment;
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
        return $this->attachmentRepo->storeAttachment($data);
    }

    public function deleteAttachment(Attachment $attachment, User $user): void
    {
        if ($attachment->user_id !== $user->id)
            throw new BusinessException('You are not authorized to delete this attachment.');

        Storage::disk('public')->delete($attachment->file_path);
        $this->attachmentRepo->deleteAttachment($attachment);
    }

    public function getCommentAttachment(Comment $comment, array $ids)
    {
        return $this->attachmentRepo->getCommentAttachment($comment, $ids);
    }
}