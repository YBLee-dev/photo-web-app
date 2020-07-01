<?php

namespace App\Users;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Webmagic\Core\Entity\EntityRepo;

class UserRepo extends EntityRepo
{
    protected $entity = User::class;

    /**
     * @return LengthAwarePaginator|Collection
     * @throws Exception
     */
    public function getAllPhotographers()
    {
        $query = $this->query();
        $query->where('role_id', 2);

        return $this->realGetMany($query);
    }
}
