<?php

namespace App\Photos\People;

use Webmagic\Core\Entity\EntityRepo;

class PersonRepo extends EntityRepo
{
    protected $entity = Person::class;
}
