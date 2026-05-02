<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Services\ProjectService;
use App\Support\OrganizationSession;
use Exception;
use Inertia\Inertia;

class ProjectController extends Controller
{
    protected  ProjectService $projectService;
    protected $currentOrganizationId;
    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
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
            \Log::error($e->getMessage());
            abort(500, Constants::DEFAULTMESSAGE);
        }
    }
}
