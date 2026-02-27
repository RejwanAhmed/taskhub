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

    public function model(): string
    {
        return Organization::class;
    }

    public function __construct(OrganizationRepositoryInterface $organizationRepo)
    {
        $this->organizationRepo = $organizationRepo;
        $this->authUser = Auth::user();
    }

    public function getUserOrganizations()
    {
        return $this->organizationRepo->getUserOrganizations($this->authUser);
    }

    public function createOrganization($validatedData)
    {
        try {
            return DB::transaction(function () use ($validatedData) {
                $validatedData['slug'] = Str::slug($validatedData['name']) . '-' . Str::random(4);
                $validatedData['status'] = 'active';
                $validatedData['approved_by'] = $this->authUser->id;
                $validatedData['approved_at'] = now();
                
                $organization = $this->organizationRepo->create($validatedData);
                $this->organizationRepo->attachOwner($organization, $this->authUser->id);

                return $organization;
            });

        } catch(\Exception $e) {
            \Log::error($e->getMessage());
            return null;
        }
    }

    public function updateOrganization(Organization $organization, $validatedData)
    {
        try {
            $organization = $this->organizationRepo->update($organization, $validatedData);
            return $organization;
        } catch(\Exception $e) {
            \Log::error($e->getMessage());
            return null;
        }
    }

    public function deleteOrganization(Organization $organization)
    {
        try {
            return $this->organizationRepo->delete($organization);
        } catch(\Exception $e) {
            \Log::error($e->getMessage());
            return false;
        }
    }

    public function switchOrganization(Organization $organization)
    {
        try {
            return $this->organizationRepo->switch($organization, $this->authUser);
        } catch(\Exception $e) {
            \Log::error($e->getMessage());
            return false;
        }
    }
}