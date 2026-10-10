<?php

namespace App\Repositories\Contracts;

use App\Models\Organization;
use App\Models\Tag;

interface TagRepositoryInterface
{
    public function getTags(Organization $organization);
    public function createTag(array $data);
    public function updateTag(Tag $tag, array $data);
    public function hasAttachedTasks(Tag $tag);
    public function deleteTag(Tag $tag);
}
