<?php

namespace App\Repositories\Eloquent;

use App\Models\Organization;
use App\Models\Tag;
use App\Repositories\Contracts\TagRepositoryInterface;

class TagRepository implements TagRepositoryInterface
{
    protected $model;

    public function __construct(Tag $model)
    {
        $this->model = $model;
    }

    public function getTags(Organization $organization)
    {
        return $organization->tags()->get();
    }

    public function createTag(array $data)
    {
        return $this->model::create($data);
    }

    public function updateTag(Tag $tag, array $data)
    {
        return $tag->update($data);
    }
    public function hasAttachedTasks(Tag $tag)
    {
        return $tag->tasks()->exists();
    }

    public function deleteTag(Tag $tag)
    {
        return $tag->delete();
    }
}
