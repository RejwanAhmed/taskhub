<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Http\Requests\Organization\CreateOrganizationRequest;
use App\Services\OrganizationService;
use Illuminate\Http\Request;
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

}
