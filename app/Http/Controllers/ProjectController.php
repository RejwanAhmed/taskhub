<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Http\Requests\Project\AssignMembersRequest;
use App\Http\Requests\Project\CreateProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Models\Project;
use App\Services\OrganizationService;
use App\Services\ProjectService;
use App\Support\OrganizationSession;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ProjectController extends Controller
{
    protected  ProjectService $projectService;
    protected  OrganizationService $organizationService;
    protected $currentOrganizationId;

    public function __construct(ProjectService $projectService, OrganizationService $organizationService)
    {
        $this->projectService = $projectService;
        $this->organizationService = $organizationService;
        $this->currentOrganizationId = OrganizationSession::getCurrentOrg();
    }

    public function index()
    {
        try {
            $projects = $this->projectService->getProjects($this->currentOrganizationId);
            $responseData = [
                'projects' => $projects,
            ];
            return Inertia::render('Project/Index', $responseData);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(500, Constants::DEFAULTMESSAGE);
        }
    }

    public function store(CreateProjectRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $this->projectService->createProject(auth()->user()->id, $this->currentOrganizationId, $validatedData);
            $message = 'New Project Created Successfully';
            return Redirect::route('projects.index')->with(Constants::SUCCESS, $message);
        } catch (Exception $e) {
            Log::info('Project creation failed = '. $e->getMessage());
            return Redirect::route('projects.index')->with(Constants::ERROR, Constants::DEFAULTMESSAGE);
        }
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        try {
            $this->authorize('update', $project);
            $validatedData = $request->validated();
            $this->projectService->updateProject($project, $validatedData);
            $message = 'Project Updated Successfully';
            return Redirect::route('projects.index')->with(Constants::SUCCESS, $message);
        } catch (AuthorizationException $e) {
            return back()->with(Constants::ERROR, Constants::PERMISSIONMESSAGE);
        } catch (Exception $e) {
            Log::info('Project update failed = '. $e->getMessage());
            return Redirect::route('projects.index')->with(Constants::ERROR, Constants::DEFAULTMESSAGE);
        }
    }

    public function show(Project $project)
    {
        try {
            $this->authorize('show', $project);
            $projectDetails = $this->projectService->getProjectDetails($project);
            $activeUsers = $this->organizationService->getOrganizationActiveUsers($this->currentOrganizationId);
            $responseData = [
                'project' => $projectDetails->toArray(),
                'activeUsers' => $activeUsers->toArray(),
            ];
            return Inertia::render('Project/Show', $responseData);
        } catch (AuthorizationException $e) {
            return back()->with(Constants::ERROR, Constants::PERMISSIONMESSAGE);
        } catch (Exception $e) {
            Log::info('Project details failed = '. $e->getMessage());
            return Redirect::route('projects.index')->with(Constants::ERROR, Constants::DEFAULTMESSAGE);
        }
    }

    public function assignMembers(AssignMembersRequest $request, Project $project)
    {
        try {
            $validatedData = $request->validated();
            $this->projectService->assignMembers($project, $validatedData, auth()->user());
            return back()->with(Constants::SUCCESS, 'Members updated successfully.');
        } catch (Exception $e) {
            Log::error('Assign members failed: ' . $e->getMessage());
            return back()->with(Constants::ERROR, Constants::DEFAULTMESSAGE);
        }
    }
}
