<?php

namespace App\Http\Controllers;

use App\Ecommerce\Cart\CartItemTypesEnum;
use App\Ecommerce\Cart\CartRepo;
use App\Ecommerce\Cart\SessionCart as Cart;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /*
     * param for settings item sizes from closure Image Intervention function
     */
    protected $size_data;

    /** @var Cart */
    protected $cart;

    /**
     * CartController constructor.
     *
     * @param Cart $cart
     */
    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    /**
     * Validate Item data
     *
     * @param Request $request
     *
     * @return array
     * @throws Exception
     */
    protected function prepareData(Request $request)
    {
        $item = [
            'id' => $request->get('id'),
            'type' => $request->get('type'),
            'price' => floatval(preg_replace("/[^-0-9\.]/", "", $request->get('price'))),
            'quantity' => $request->get('quantity'),
            'name' => $request->get('name'),
            'sub_gallery_password' => $request->get('sub_gallery_password'),
            'sub_gallery_id' => $request->get('sub_gallery_id'),
            'sub_gallery_data' => $request->get('sub_gallery_data', null),
            'crop_info' => $request->get('crop_info'),
        ];

        $rules = [
            'id' => 'required',
            'type' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric|min:1',
            'name' => 'required',
        ];

        $validator = Validator::make($item, $rules);

        if ($validator->fails()) {
            throw new Exception($validator->messages()->first());
        }

        $type = $request->get('type');
        if (CartItemTypesEnum::PACKAGE()->is($type)) {
            $item['products'] = $request->get('products');
        }

        if (CartItemTypesEnum::PRODUCT()->is($type)) {
            $attributes = [
                'size' => $request->get('size'),
                'size_id' => $request->get('size_id'),
                'sizes' => $request->get('sizes'),
                'image' => $request->get('image'),
                'image_id' => $request->get('image_id'),
                'size_original' => $request->get('size_original'),
                'index' => $request->get('index'),
                'product_type' => $request->get('product_type', null),
                'retouch' => $request->get('retouch', null),
            ];

            $item = array_merge($item, $attributes);
        }

        return $item;
    }

    /**
     * Add item to cart
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function addItem(Request $request)
    {
        $item = $this->prepareData($request);
        $cartItemId = $this->cart->add($item);

        return $this->getCartItemInJsonResponse($cartItemId);
    }

    /**
     * Get cart info
     *
     *
     * @return JsonResponse
     */
    public function getCart()
    {
        return response()->json($this->cart->getContent()->toArray()); //call sessionCart -> getContent
    }

    /**
     * Delete all items from cart
     *
     * @return JsonResponse
     */
    public function cartClear()
    {
        $this->cart->clear();

        return $this->getCart();
    }

    /**
     * Remove item by id from cart
     *
     * @param string $cartItemId
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function removeItem(string $cartItemId)
    {
        DB::beginTransaction();
        if ($this->cart->remove($cartItemId)) {
            DB::commit();
        } else {
            DB::rollBack();
        }

        return $this->getCart();
    }

    /**
     * @param string  $cartItemId
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function updateQty(string $cartItemId, Request $request)
    {
        $item = $this->prepareData($request);
        $this->cart->update($cartItemId, new Collection($item));

        return $this->getCartItemInJsonResponse($cartItemId);
    }

    /**
     * Update in db info about free gift
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Ecommerce\Cart\CartRepo $cartRepo
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function updateFreeGiftInfo(Request $request, CartRepo $cartRepo)
    {
       $session_key = $this->cart->getSessionCartID();
       $cart = $cartRepo->getOrCreateBySessionKey($session_key);

       $status = (bool)$request->get('free_gift', false);

       $cartRepo->update($cart->id, [
           'free_gift' => $status,
       ]);

        return response()->json(['free_gift' => $status]);
    }

    /**
     * Ger from db info about free gift
     *
     * @param \App\Ecommerce\Cart\CartRepo $cartRepo
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getFreeGiftInfo(CartRepo $cartRepo)
    {
        $session_key = $this->cart->getSessionCartID();
        $cart = $cartRepo->getOrCreateBySessionKey($session_key);

        return response()->json([
            'free_gift' => $cart->free_gift,
            'overdue' => $cart->gallery->isDeadlineCame()
        ]);
        // return response()->json([
        //     'free_gift' => 0,
        //     'overdue' => 1
        // ]);
    }

    /**
     * @param string  $cartItemId
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function updateImageOrSize(string $cartItemId, Request $request)
    {
        // Validate and prepare
        $item = $this->prepareData($request);

        // Update cart data
        $this->cart->update($cartItemId, new Collection($item));

        // Send cart data
        return $this->getCartItemInJsonResponse($cartItemId);
    }

    /**
     * @param string  $cartItemId
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function updateCropInfo(string $cartItemId, Request $request)
    {
        $item = $this->prepareData($request);
        $this->cart->update($cartItemId, new Collection($item));

        return $this->getCartItemInJsonResponse($cartItemId);
    }

    /**
     * @param string $cartItemId
     *
     * @return JsonResponse
     */
    protected function getCartItemInJsonResponse(string $cartItemId)
    {
        return response()->json([
            $cartItemId => $this->cart->getItem($cartItemId)
        ]);
    }
}
