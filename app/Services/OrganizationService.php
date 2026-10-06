<?php 

namespace App\Services;

use App\Models\Organization;
use App\Repositories\Contracts\OrganizationRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class OrganizationService
{
    protected $organizationRepo;

    public function __construct(OrganizationRepositoryInterface $organizationRepo)
    {
        $this->organizationRepo = $organizationRepo;
    }

    public function getUserOrganizations(): Collection
    {
        return $this->organizationRepo->getUserOrganizations(auth()->user());
    }

    public function createOrganization(array $data): Organization
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = Str::slug($data['name']) . '-' . Str::random(4);
            $data['status'] = 'active';
            $data['approved_by'] = auth()->user()->id;
            $data['approved_at'] = now();
            
            $organization = $this->organizationRepo->create($data);
            $this->organizationRepo->attachOwner($organization, auth()->user()->id);

            return $organization;
        });
    }

    public function updateOrganization(Organization $organization, array $data): Organization
    {
        return $this->organizationRepo->update($organization, $data);
    }

    public function deleteOrganization(Organization $organization): bool
    {
        return $this->organizationRepo->delete($organization);
    }

    public function switchOrganization(Organization $organization): bool
    {
        return $this->organizationRepo->switch($organization, auth()->user());   
    }

    public function getOrganizationMembers(int $orgId): Collection
    {
        $organization = $this->organizationRepo->getCurrentOrganization($orgId);
        return $this->organizationRepo->getOrganizationMembers($organization);
    }

    public function getOrganizationActiveUsers(int $orgId): Collection
    {
        $organization = $this->organizationRepo->getCurrentOrganization($orgId);
        return $this->organizationRepo->getActiveUsers($organization);
    }
}