<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Http\Requests\Organization\CreateOrganizationRequest;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Services\OrganizationService;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class OrganizationController extends Controller
{
    protected OrganizationService $organizationService;

    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    public function index()
    {
        $organizations = $this->organizationService->getUserOrganizations(auth()->user());
        $responseData = [
            'organizations' => $organizations,
        ];
        return Inertia::render('Organization/Index', $responseData);
    }

    public function store(CreateOrganizationRequest $request)
    {
        $validatedData = $request->validated();
        $organization = $this->organizationService->createOrganization($validatedData);
        $status = $organization ? Constants::SUCCESS : Constants::ERROR;
        $message = $organization ? 'New Organization Created Successfully' : 'New Organization Could Not Be Created';
        return Redirect::route('organizations.index')->with($status, $message);
    }

    public function update(UpdateOrganizationRequest $request, Organization $organization)
    {
        $validatedData = $request->validated();
        $ogranization = $this->organizationService->updateOrganization($organization, $validatedData);
        $status = $ogranization ? Constants::SUCCESS : Constants::ERROR;
        $message = $ogranization ? 'Organization Updated Successfully' : 'Organization Could Not Be Updated';
        return Redirect::route('organizations.index')->with($status, $message);
    }

    // Organizations will never be deleted, so we wont need this in future.
    public function destroy(Organization $organization)
    {
        $isDeleted = $this->organizationService->deleteOrganization($organization);
        $status = $isDeleted ? Constants::SUCCESS : Constants::ERROR;
        $message = $isDeleted ? 'Organization Deleted Successfully' : 'Organization Could Not Be Deleted';
        return Redirect::route('organizations.index')->with($status, $message);
    }
}
