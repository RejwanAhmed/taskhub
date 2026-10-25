<?php

namespace App\Repositories\Contracts;

use App\Models\Attachment;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;

interface AttachmentRepositoryInterface
{
    // Define your methods here
    public function getAttachments(Model $attachable);
    public function storeAttachment(array $data);
    public function deleteAttachment(Attachment $attachment);
    public function getCommentAttachment(Comment $comment, array $ids);
}
