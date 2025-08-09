<?php 

namespace App\Services;

use App\Models\Tag;
use App\Services\Core\BaseModelService;

class TagService extends BaseModelService
{
    public function model(): string
    {
        return Tag::class;
    }
}