<?php

namespace App\Ecommerce\Cart;

use Exception;
use Illuminate\Session\SessionManager as Session;
use Illuminate\Support\Collection;
use App\Ecommerce\Cart\CartSession;

class SessionCart
{
    /**
     * the item storage
     *
     * @var
     */
    public $session;

    /**
     * the session key use for the cart
     *
     * @var
     */
    public $session_key;
    public $cartService;

    /**
     * Cart constructor.
     *
     * @param Session $session
     */
    public function __construct(Session $session, CartService $cartService)
    {
        $this->session = $session;
        $this->session_key = 'cart';
        $this->session_cart_id_key = 'cart_id';
        $this->cartService = $cartService;
    }

    /**
     * Add item to cart
     *
     * @param array $item
     *
     * @return bool|int|string
     * @throws Exception
     */
    public function add(array $item)
    {
        return $this->addItem($item);
    }

    /**
     * Return cart content
     *
     * @return Collection
     */
    public function getContent()
    {
        // $tmp = json_decode($this->session->get($this->session_key));
        
        $cartData = (new Collection($this->session->get($this->session_key)));
        if($cartData->isEmpty()){
            $hash = hash('sha1', time());
            $this->session->put($this->session_cart_id_key, $hash);
        }

        return $cartData;
    }

    /**
     * Return unique cart hash from session
     *
     * @return mixed
     */
    public function getSessionCartID()
    {
        return $this->session->get($this->session_cart_id_key);
    }

    /**
     * Add item to cart with unique id generate
     *
     * @param array $item
     *
     * @return string
     * @throws Exception
     */
    public function addItem(array $item)
    {
        $cart = $this->getContent();

        $cartItemId = uniqid();
        // dd(new Collection($item));
        $cart->put($cartItemId, new Collection($item));

        $this->save($cart);

        return $cartItemId;
    }

    /**
     * Save cart in session by key
     *
     * @param $cart
     * @throws Exception
     */
    protected function save($cart)
    {
        // $this->session->put($this->session_key, $cart);
        $tmp = $cart->toArray();
        $this->clear();
        $this->session->put($this->session_key, $tmp);
        // dd($this->session->get($this->session_key));
        // $aaa = json_decode($this->session->get($this->session_key));
        // dd(new Collection($aaa));
        // session([$this->session_key => $cart]);
        
        $this->cartService->updateCartInDB(
            $this->getContent(),
            $this->session->get($this->session_cart_id_key)
        );

        $this->session->get($this->session_key);
    }

    /**
     * Get cart item by unique id
     *
     * @param $cart_item_id
     * @return mixed
     */
    public function getItem($cart_item_id)
    {
        return $this->getContent()->get($cart_item_id);
    }

    /**
     * Remove cart item by unique id
     * and update cart in session
     *
     * @param $cart_item_id
     *
     * @return bool
     * @throws Exception
     */
    public function remove($cart_item_id)
    {
        $cart = $this->getContent();
        $ary = explode(",", $cart_item_id);
        foreach ($ary as $key) {
            $cart->forget($key);
            $this->save($cart);
        }
        // $cart->forget($cart_item_id);
        // $this->save($cart);

        return true;
    }

    /**
     * Destroy cart form session
     *
     * @return bool
     */
    public function clear()
    {   
        $this->session->forget($this->session_key);

        return true;
    }

    /**
     * Replace cart item by unique id
     * with new data
     *
     * @param $cart_item_id
     * @param $new_data
     * @return bool
     */
    public function update($cart_item_id, Collection $new_data)
    {
        $cart = $this->getContent();

        $cart->put($cart_item_id, $new_data);

        $this->save($cart);

        return $cart_item_id;
    }

    /**
     * Update item quantity relative to its current quantity value
     *
     * @param $item
     * @param $value
     * @return mixed
     */
    public function updateQuantityRelative($item, $value)
    {
        $item['quantity'] = ++$value;

        return $item;
    }
}
