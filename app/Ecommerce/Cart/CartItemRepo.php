<?php

namespace App\Ecommerce\Cart;

use Webmagic\Core\Entity\EntityRepo;

class CartItemRepo extends EntityRepo
{
    protected $entity = CartItem::class;

    /**
     * Destroy all items by cart_item_id
     *
     * @param $cart_item_id
     * @return mixed
     */
    public function destroyByCartItemID($cart_item_id)
    {
        $query = $this->query();
        $query->where('cart_item_id', $cart_item_id);

        return $query->delete();
    }

    public function getByCartItemID($cart_item_id)
    {
        $query = $this->query();
        $query->where('cart_item_id', $cart_item_id);

        return $this->realGetOne($query);
    }

    public function updateByCartItemID($cart_item_id, array $data)
    {
        $query = $this->query();
        $query->where('cart_item_id', $cart_item_id);

        return $query->update($data);
    }
}
