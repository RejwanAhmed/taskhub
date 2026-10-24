<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Exceptions\BusinessException;
use App\Http\Requests\Comment\CreateCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Task;
use App\Services\CommentService;
use Exception;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    protected CommentService $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function index(Task $task)
    {
        try {
            $comments = $this->commentService->getComments($task);
            $responseData = [
                'comments' => $comments,
            ];
            return response()->json($responseData);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([Constants::ERROR => Constants::DEFAULTMESSAGE], 500);
        }
    }

    public function store(CreateCommentRequest $request, Task $task)
    {
        try {
            $validatedData = $request->validated();
            $comment = $this->commentService->createComment($task, auth()->user(), $validatedData);
            $comment->load('user:id,name');
            $message = 'Comment added successfully';
            $responseData = [
                'comment'   => $comment,
                Constants::SUCCESS => $message,
            ];
            return response()->json($responseData, 201);
        } catch (Exception $e) {
            Log::error('Comment creation failed: ' . $e->getMessage());
            return response()->json([Constants::ERROR => Constants::DEFAULTMESSAGE], 500);
        }
    }

    public function update(UpdateCommentRequest $request, Task $task, Comment $comment)
    {
        try {
            $validatedData = $request->validated();
            $this->commentService->updateComment($comment, auth()->user(), $validatedData['content']);
            $message = 'Comment updated successfully';
            return response()->json([Constants::SUCCESS => $message]);
        } catch (BusinessException $e) {
            return response()->json([Constants::ERROR => $e->getMessage()], 422);
        } catch (Exception $e) {
            Log::error('Comment updated failed: ' . $e->getMessage());
            return response()->json([Constants::ERROR => Constants::DEFAULTMESSAGE], 500);
        }
    }

    public function destroy(Task $task, Comment $comment)
    {
        try {
            $this->commentService->deleteComment($comment, auth()->user());
            $message = 'Comment deleted successfully';
            return response()->json([Constants::SUCCESS => $message]);
        } catch (BusinessException $e) {
            return response()->json([Constants::ERROR => $e->getMessage()], 422);
        } catch (Exception $e) {
            Log::error('Comment updated failed: ' . $e->getMessage());
            return response()->json([Constants::ERROR => Constants::DEFAULTMESSAGE], 500);
        }
    }
}
