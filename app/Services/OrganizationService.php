<?php 

namespace App\Services;

use App\Models\Organization;
use App\Repositories\Contracts\OrganizationRepositoryInterface;
use App\Services\Core\BaseModelService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrganizationService extends BaseModelService
{
    protected $organizationRepo;
    protected $authUser;
    protected $currentOrganization;

    public function model(): string
    {
        return Organization::class;
    }

    public function __construct(OrganizationRepositoryInterface $organizationRepo)
    {
        $this->organizationRepo = $organizationRepo;
        $this->authUser = Auth::user();
        $this->currentOrganization = $this->authUser->currentOrganization;
    }

    public function getUserOrganizations()
    {
        return $this->organizationRepo->getUserOrganizations($this->authUser);
    }

    public function createOrganization($validatedData)
    {
        return DB::transaction(function () use ($validatedData) {
            $validatedData['slug'] = Str::slug($validatedData['name']) . '-' . Str::random(4);
            $validatedData['status'] = 'active';
            $validatedData['approved_by'] = $this->authUser->id;
            $validatedData['approved_at'] = now();
            
            $organization = $this->organizationRepo->create($validatedData);
            $this->organizationRepo->attachOwner($organization, $this->authUser->id);

            return $organization;
        });
    }

    public function updateOrganization(Organization $organization, $validatedData)
    {
        $organization = $this->organizationRepo->update($organization, $validatedData);
        return $organization;
    }

    public function deleteOrganization(Organization $organization)
    {
        return $this->organizationRepo->delete($organization);
    }

    public function switchOrganization(Organization $organization)
    {
        return $this->organizationRepo->switch($organization, $this->authUser);   
    }

    public function getOrganizationMembers()
    {
        return $this->organizationRepo->getOrganizationMembers($this->currentOrganization);
    }
}