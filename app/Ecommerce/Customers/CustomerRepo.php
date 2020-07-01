<?php

namespace App\Ecommerce\Customers;

use Webmagic\Core\Entity\EntityRepo;

class CustomerRepo extends EntityRepo
{
    protected $entity = Customer::class;

    public function findOrCreateByEmail(string $email)
    {
        $query = $this->query();
        return $query->firstOrCreate(['email' => $email]);
    }
}
