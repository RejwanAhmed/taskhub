<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Exceptions\BusinessException;
use App\Http\Requests\Tag\CreateTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Models\Tag;
use App\Services\TagService;
use App\Support\OrganizationSession;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TagController extends Controller
{
    protected TagService $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function index()
    {
        try {
            $orgId = OrganizationSession::getCurrentOrg();
            $tags = $this->tagService->getTags($orgId);
            $responseData = [
                'tags' => $tags,
            ];

            return Inertia::render('Tag/Index', $responseData);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(500, Constants::DEFAULTMESSAGE);
        }
    }

    public function store(CreateTagRequest $reuquest)
    {
        try {
            $validatadData = $reuquest->validated();
            $orgId = OrganizationSession::getCurrentOrg();
            $this->tagService->createTag($orgId, $validatadData);
            $message = 'New Tag Created Successfully';
            return Redirect::route('tags.index')->with(Constants::SUCCESS, $message);
        } catch (Exception $e) {
            Log::error('Tag creation failed = '. $e->getMessage());
            return Redirect::route('tags.index')->with(Constants::ERROR, Constants::DEFAULTMESSAGE);
        }
    }

    public function update(UpdateTagRequest $request, Tag $tag)
    {
        try {
            $validatedData = $request->validated();
            $this->tagService->updateTag($tag, $validatedData);
            $message = 'Tag Updated Successfully';
            return Redirect::route('tags.index')->with(Constants::SUCCESS, $message);
        } catch (Exception $e) {
            Log::error('Tag update failed = ' . $e->getMessage());
            return Redirect::route('tags.index')->with(Constants::ERROR, Constants::DEFAULTMESSAGE);
        }
    }

    public function destroy(Tag $tag)
    {
        try {
            $this->tagService->deleteTag($tag);
            $message = 'Tag Deleted Successfully';
            return Redirect::route('tags.index')->with(Constants::SUCCESS, $message);
        } catch (BusinessException $e) {
            return back()->with(Constants::ERROR, $e->getMessage());
        } catch (Exception $e) {
            Log::error('Tag deleted failed = ' . $e->getMessage());
            return Redirect::route('tags.index')->with(Constants::ERROR, Constants::DEFAULTMESSAGE);
        }
    }
}
