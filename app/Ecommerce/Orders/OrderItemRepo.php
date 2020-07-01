<?php

namespace App\Ecommerce\Orders;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Webmagic\Core\Entity\EntityRepo;

class OrderItemRepo extends EntityRepo
{
    protected $entity = OrderItem::class;

    /**
     * Destroy all items by item_id
     *
     * @param $item_id
     *
     * @return mixed
     * @throws Exception
     */
    public function destroyByItemID($item_id)
    {
        $query = $this->query();
        $query->where('item_id', $item_id);

        return $query->delete();
    }

    /**
     * @param $item_id
     *
     * @return Model|null
     * @throws Exception
     */
    public function getByItemID($item_id)
    {
        $query = $this->query();
        $query->where('item_id', $item_id);

        return $this->realGetOne($query);
    }

    /**
     * @param       $item_id
     * @param array $data
     *
     * @return int
     * @throws Exception
     */
    public function updateByItemID($item_id, array $data)
    {
        $query = $this->query();
        $query->where('item_id', $item_id);

        return $query->update($data);
    }
}
