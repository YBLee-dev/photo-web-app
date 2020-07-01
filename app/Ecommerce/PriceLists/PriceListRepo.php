<?php

namespace App\Ecommerce\PriceLists;

use Illuminate\Database\Eloquent\Model;
use Webmagic\Core\Entity\EntityRepo;

class PriceListRepo extends EntityRepo
{
    protected $entity = PriceList::class;

    public function createCopyWithRelations(Model $priceList)
    {
        $newPriceList = $priceList->replicate();
        $newPriceList->name = 'COPIED ---'.$newPriceList->name;
        $newPriceList->save();

        $newPriceList->products()->attach($priceList->products()->allRelatedIds());
        $newPriceList->packages()->attach($priceList->packages()->allRelatedIds());

        return $newPriceList;
    }
}
