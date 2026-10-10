<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use App\Models\Tag;
use App\Repositories\Contracts\OrganizationRepositoryInterface;
use App\Repositories\Contracts\TagRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TagService
{
    protected $tagRepo;
    protected $organizationRepo;

    public function __construct(TagRepositoryInterface $tagRepo, OrganizationRepositoryInterface $organizationRepo)
    {
        $this->tagRepo = $tagRepo;
        $this->organizationRepo = $organizationRepo;
    }

    public function getTags(int $orgId): Collection
    {
        $organization = $this->organizationRepo->getCurrentOrganization($orgId);
        return $this->tagRepo->getTags($organization);
    }

    public function createTag(int $orgId, array $data): void
    {
        $data = array_merge($data, [
            'organization_id' => $orgId,
        ]);

        $this->tagRepo->createTag($data);
    }

    public function updateTag(Tag $tag, array $data): void
    {
        $this->tagRepo->updateTag($tag, $data);
    }

    public function deleteTag(Tag $tag): bool
    {
        return DB::transaction(function () use ($tag) {
            if ($this->tagRepo->hasAttachedTasks($tag))
                throw new BusinessException('Can not delete this tag" — it is assigned to some task(s).');

            $this->tagRepo->deleteTag($tag);
            return true;
        });
    }
}