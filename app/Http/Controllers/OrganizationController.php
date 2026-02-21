<?php

namespace App\Http\Controllers;

use App\Services\OrganizationService;
use Illuminate\Http\Request;
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

}
