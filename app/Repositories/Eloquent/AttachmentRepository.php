<?php

namespace App\Repositories\Eloquent;

use App\Models\Attachment;
use App\Models\Comment;
use App\Repositories\Contracts\AttachmentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class AttachmentRepository implements AttachmentRepositoryInterface
{
    // Implement methods here
    protected $model;

    public function __construct(Attachment $model)
    {
        $this->model = $model;
    }

    public function getAttachments(Model $attachable)
    {
        return $attachable->attachments()->with('user:id,name')->get();
    }

    public function storeAttachment(array $data)
    {
        return $this->model::create($data);
    }

    public function deleteAttachment(Attachment $attachment)
    {
        $attachment->delete();
    }

    public function getCommentAttachment(Comment $comment, $ids)
    {
        return $comment->attachments()->whereIn('id', $ids)->get();
    }
}
