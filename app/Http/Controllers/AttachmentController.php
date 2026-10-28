<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Exceptions\BusinessException;
use App\Http\Requests\Attachment\CreateAttachmentRequest;
use App\Models\Attachment;
use App\Models\Task;
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

    public function index(Task $task)
    {
        try {
            $attachments = $this->attachmentService->getAttachments($task);
            $responseData = [
                'attachments' => $attachments,
            ];
            return response()->json($responseData);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([Constants::ERROR => Constants::DEFAULTMESSAGE], 500);
        }
    }

    public function store(CreateAttachmentRequest $request, Task $task)
    {
        try {
            $validatedData = $request->validated();
            $attachments   = [];

            foreach ($validatedData['files'] as $file) {
                $attachment    = $this->attachmentService->storeAttachment($task, auth()->user(), $file);
                $attachments[] = $attachment;
            }

            $message = 'Files uploaded successfully';
            $responseData = [
                'attachments' => $attachments,
                Constants::SUCCESS => $message,
            ];

            return response()->json($responseData, 201);
        } catch (BusinessException $e) {
            return response()->json([Constants::ERROR => $e->getMessage()], 422);
        } catch (Exception $e) {
            Log::error('Attachment upload failed: ' . $e->getMessage());
            return response()->json([Constants::ERROR => Constants::DEFAULTMESSAGE], 500);
        }
    }

    public function destroy(Task $task, Attachment $attachment)
    {
        try {
            $this->attachmentService->deleteAttachment($attachment, auth()->user());
            $message = 'Attachment deleted successfully';
            return response()->json([Constants::SUCCESS => $message]);
        } catch (BusinessException $e) {
            return response()->json([Constants::ERROR => $e->getMessage()], 422);
        } catch (Exception $e) {
            Log::error('Attachment deletion failed: ' . $e->getMessage());
            return response()->json([Constants::ERROR => Constants::DEFAULTMESSAGE], 500);
        }
    }
}
