<?php 

namespace App\Services;

use App\Models\Organization;
use App\Repositories\Contracts\OrganizationRepositoryInterface;
use App\Services\Core\BaseModelService;

class OrganizationService extends BaseModelService
{
    protected $organizationRepo;

    public function model(): string
    {
        return Organization::class;
    }

    public function __construct(OrganizationRepositoryInterface $organizationRepo)
    {
        $this->organizationRepo = $organizationRepo;
    }

    public function getUserOrganizations($user)
    {
        return $this->organizationRepo->getUserOrganizations($user);
    }
}