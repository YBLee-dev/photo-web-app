<?php

namespace App\Ecommerce\Cart;


use App\Photos\SubGalleries\SubGalleryRepo;
use Carbon\Carbon;
use Exception;
use Webmagic\Core\Entity\Exceptions\EntityNotExtendsModelException;
use Webmagic\Core\Entity\Exceptions\ModelNotDefinedException;

class CartService
{
    public $cartRepo;
    public $itemRepo;

    public function __construct(CartRepo $cartRepo, CartItemRepo $itemRepo)
    {
        $this->cartRepo = $cartRepo;
        $this->itemRepo = $itemRepo;
    }

    /**
     * Check if time from last cart update more then session lifetime,
     * set to cart status abandoned
     *
     * @throws Exception
     */
    public function checkAndUpdateStatuses()
    {
        $carts = $this->cartRepo->getAll();

        foreach ($carts as $cart) {
            $difference = Carbon::now()->diffInMinutes($cart->updated_at);
            if($difference > config('session.lifetime')){
                $cart->timestamps = false;
                $cart->abandoned = true;
                $cart->save();
            }
        }
    }

    /**
     * Create or update cart with items and counting total sum
     *
     * @param $cartContent
     * @param $sessionKey
     *
     * @throws EntityNotExtendsModelException
     * @throws ModelNotDefinedException
     */
    public function updateCartInDB($cartContent, $sessionKey)
    {
        $cartDB = $this->cartRepo->getOrCreateBySessionKey($sessionKey);

        $subGalleryRepo = new SubGalleryRepo();

        $subGallery = $subGalleryRepo->getByID($cartContent->first()['sub_gallery_id']['id']);

        $cartDB->items()->delete();

        $items_count = 0;
        $total = 0;

        foreach ($cartContent as $id => $item) {
            $prepared_data = [
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'sum' => $item['price'] * $item['quantity'],
                'cart_id' => $cartDB->id,
                'cart_item_id' => $id,
            ];
            $items_count += $prepared_data['quantity'];
            $total +=  $prepared_data['sum'];

            if (CartItemTypesEnum::PACKAGE()->is($item['type'])) {
                foreach ($item['products'] as $product) {
                    $prepared_data['product_id'] = $product['id'];
                    $prepared_data['name'] = $product['name'];
                    $prepared_data['image'] = $product['image'];
                    $prepared_data['size_combination_id'] = $product['size']['id'];
                    $prepared_data['package_id'] = $item['id'];
                    $prepared_data['package_name'] = $item['name'];

                    /** @var CartItem $cartItem */
                    $cartItem = $this->itemRepo->create($prepared_data);
                    //Sync photo
                    $cartItem->photos()->sync([$product['image_id']]);
                }
            }

            if (CartItemTypesEnum::PRODUCT()->is($item['type'])) {
                $prepared_data['product_id'] = $item['id'];
                $prepared_data['name'] = $item['name'];
                $prepared_data['image'] = $item['image'];
                $prepared_data['size_combination_id'] = $item['size']['id'];
                $prepared_data['retouch'] = $item['retouch'] ?? '';
                $prepared_data['product_type'] = $item['product_type'] ?? '';

                /** @var CartItem $cartItem */
                $cartItem = $this->itemRepo->create($prepared_data);
                //Sync photo
                $cartItem->photos()->sync([$item['image_id']]);
            }
        }

        if($items_count == 0){
            $this->cartRepo->destroy($cartDB->id);
        } else {
            $this->cartRepo->update($cartDB->id, [
                'total' => $total,
                'items_count' => $items_count,
                'sub_gallery_id' => $subGallery->id,
                'gallery_id' => $subGallery->gallery_id,
                'price_list_id' => $subGallery->gallery->priceList->id,
            ]);
        }
    }
}
