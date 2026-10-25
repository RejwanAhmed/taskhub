<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Exceptions\BusinessException;
use App\Http\Requests\Attachment\CreateAttachmentRequest;
use App\Models\Comment;
use App\Services\AttachmentService;
use Exception;
use Illuminate\Support\Facades\Log;

class AttachmentController extends Controller
{
    protected AttachmentService $attachmentService;

    public function __construct(AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    public function storeForComment(CreateAttachmentRequest $request, Comment $comment)
    {
        try {
            $attachments = [];
            foreach ($request->file('files') as $file) {
                $attachment = $this->attachmentService->storeAttachment($comment, auth()->user(), $file);
                $attachment->load('user:id,name');
                $attachments[] = $attachment;
            }
            $responseData = [
                Constants::SUCCESS => 'Files uploaded successfully',
                'attachments'      => $attachments,
            ];
            return response()->json($responseData, 201);
        } catch (BusinessException $e) {
            return response()->json([Constants::ERROR => $e->getMessage()], 422);
        } catch (Exception $e) {
            Log::error('Attachment upload failed: ' . $e->getMessage());
            return response()->json([Constants::ERROR => Constants::DEFAULTMESSAGE], 500);
        }
    }
}
