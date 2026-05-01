<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Http\Requests\Organization\CreateOrganizationRequest;
use App\Http\Requests\Organization\SwitchOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Services\OrganizationService;
use App\Support\OrganizationSession;
use Exception;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Log;

class OrganizationController extends Controller
{
    protected OrganizationService $organizationService;
    protected $currentOrganizationId;
    
    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
        $this->currentOrganizationId = OrganizationSession::getCurrentOrg();
    }

    public function index()
    {
        try {
            $organizations = $this->organizationService->getUserOrganizations();
            $responseData = [
                'organizations' => $organizations,
            ];
            return Inertia::render('Organization/Index', $responseData);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(500, Constants::DEFAULTMESSAGE);
        }
        
    }

    public function store(CreateOrganizationRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $organization = $this->organizationService->createOrganization($validatedData);
            $message = 'New Organization Created Successfully';
            return Redirect::route('organizations.index')->with(Constants::SUCCESS, $message);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return Redirect::route('organizations.index')->with(Constants::ERROR, Constants::DEFAULTMESSAGE);
        }
    }

    public function update(UpdateOrganizationRequest $request, Organization $organization)
    {
        try {
            $validatedData = $request->validated();
            $ogranization = $this->organizationService->updateOrganization($organization, $validatedData);
            $message = 'Organization Updated Successfully';
            return Redirect::route('organizations.index')->with(Constants::SUCCESS, $message);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return Redirect::route('organizations.index')->with(Constants::ERROR, Constants::DEFAULTMESSAGE);
        }
        
    }

    // Organizations will never be deleted, so we wont need this in future.
    public function destroy(Organization $organization)
    {
        try {
            $isDeleted = $this->organizationService->deleteOrganization($organization);
            $message = 'Organization Deleted Successfully';
            return Redirect::route('organizations.index')->with(Constants::SUCCESS, $message);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return Redirect::route('organizations.index')->with(Constants::ERROR, Constants::DEFAULTMESSAGE);
        }
        
    }

    public function switchOrganization(SwitchOrganizationRequest $request, Organization $organization)
    {
        try {
            $isSwitched = $this->organizationService->switchOrganization($organization);
            $message = 'Organization Switched Successfully';
            return Redirect::route('organizations.index')->with(Constants::SUCCESS, $message);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return Redirect::route('organizations.index')->with(Constants::ERROR, Constants::DEFAULTMESSAGE);
        }
    }

    public function members()
    {
        try {
            $members = $this->organizationService->getOrganizationMembers($this->currentOrganizationId);
            $responseData = [
                'members' => $members,
            ];
            return Inertia::render('Organization/Members', $responseData);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(500, Constants::DEFAULTMESSAGE);
        }
        
    }
}
