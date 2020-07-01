<?php

namespace App\Photos\Schools;

use Illuminate\Database\Eloquent\Builder;
use Webmagic\Core\Entity\EntityRepo;

class SchoolRepo extends EntityRepo
{
    protected $entity = School::class;

    /**
     * Set default ordering
     *
     * @param Builder $query
     *
     * @return Builder
     */
    protected function addOrdering(Builder $query): Builder
    {
        $query->orderBy('id', 'desc');

        return  $query;
    }

}
