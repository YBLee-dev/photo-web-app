<?php

namespace App\Http\Controllers;

use App\Ecommerce\Cart\CartItemTypesEnum;
use App\Ecommerce\Cart\CartRepo;
use App\Ecommerce\Cart\SessionCart as Cart;
use App\Ecommerce\Customers\CustomerRepo;
use App\Ecommerce\Orders\Order;
use App\Ecommerce\Orders\OrderItem;
use App\Ecommerce\Orders\OrderItemRepo;
use App\Ecommerce\Orders\OrderPaymentStatusEnum;
use App\Ecommerce\Orders\OrderRepo;
use App\Ecommerce\Orders\OrderStatusEnum;
use App\Ecommerce\Products\ProductTypesEnum;
use App\Ecommerce\PromoCodes\PromoCodeRepo;
use App\Ecommerce\PromoCodes\PromoCodeService;
use App\Events\ConfirmationPaymentEvent;
use App\Events\NewPaymentEvent;
use App\Photos\SubGalleries\SubGalleryRepo;
use App\Processing\Scenarios\DigitalZipPreparingScenario;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\contract\v1\CreateTransactionRequest;
use net\authorize\api\contract\v1\CreditCardType;
use net\authorize\api\contract\v1\CustomerAddressType;
use net\authorize\api\contract\v1\CustomerDataType;
use net\authorize\api\contract\v1\MerchantAuthenticationType;
use net\authorize\api\contract\v1\OrderType;
use net\authorize\api\contract\v1\PaymentType;
use net\authorize\api\contract\v1\SettingType;
use net\authorize\api\contract\v1\TransactionRequestType;
use net\authorize\api\contract\v1\UserFieldType;
use net\authorize\api\controller\CreateTransactionController;
use Symfony\Component\HttpFoundation\Response;
use Webmagic\Core\Controllers\AjaxRedirectTrait;

class OrderController extends Controller
{
    use AjaxRedirectTrait;

    /**
     * Create customer and order,save cart with items in db
     *
     * @param Request        $request
     * @param Cart           $cart
     * @param CustomerRepo   $customerRepo
     * @param SubGalleryRepo $subGalleryRepo
     * @param OrderItemRepo  $orderItemRepo
     * @param OrderRepo      $orderRepo
     * @param PromoCodeRepo  $promoCodeRepo
     * @param CartRepo       $cartRepo
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function makeOrder(
        Request $request,
        Cart $cart,
        CustomerRepo $customerRepo,
        SubGalleryRepo $subGalleryRepo,
        OrderItemRepo $orderItemRepo,
        OrderRepo $orderRepo,
        PromoCodeRepo $promoCodeRepo,
        CartRepo $cartRepo
    ) {
        $request->validate([
            'id' => 'required',
            'email' => 'required',
        ]);

        $customer = $customerRepo->findOrCreateByEmail($request->get('email'));
        $subGallery = $subGalleryRepo->getByID($request->get('id'));
        $coupon = $promoCodeRepo->getByID($request->get('coupone_id'));

        /** @var Order $order */
        $order = $orderRepo->create([
            'status' => OrderStatusEnum::NEW,
            'payment_status' => $request->get('total') == 0 ? OrderPaymentStatusEnum::PAID() : OrderPaymentStatusEnum::NOT_PAID(),
            'customer_id' => $customer->id,
            'sub_gallery_id' => $subGallery->id,
            'gallery_id' => $subGallery->gallery_id,
            'price_list_id' => $subGallery->getPriceList()->id,
            'customer_first_name' => $request->get('name'),
            'customer_last_name' => $request->get('lastname'),
            'address' => $request->get('address'),
            'city' => $request->get('city'),
            'state' => $request->get('state'),
            'postal' => $request->get('postalcode'),
            'country' => $request->get('country'),
            'message' => $request->get('comment'),
            'receive_promotions_by_email' => $request->get('promotion') ? true : false,
            // 'total' => $request->get('total'),
            'total' => $request->get('subtotal') + $request->get('tax'),
            'subtotal' => $request->get('subtotal'),
            'tax' => $request->get('tax'),
            'total_coupon' => $request->get('total_coupone'),
            'discount' => $coupon ? $coupon->discount_amount : null,
            'discount_type' => $coupon ? $coupon->type : null,
            'discount_name' => $coupon ? $coupon->name : null,
            'promo_code_id' => $coupon ? $coupon->id : null,
            'items_count' => $request->get('items'),
            'free_gift' => $request->get('free_gift', 0),
            'hash' => hash('adler32', time().$customer->id)
        ]);


        $cart_items = $cart->getContent();


        $image_prefix = Storage::disk('s3')->url('preview/');
        foreach ($cart_items as $id => $item) {
            $prepared_data = [
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'sum' => $item['price'] * $item['quantity'],
                'order_id' => $order->id,
                'sub_gallery_id' => $item['sub_gallery_id']['id'],
                'item_id' => $id,
            ];

            if (CartItemTypesEnum::PACKAGE()->is($item['type'])) {
                foreach ($item['products'] as $product) {

                    $prepared_data['product_id'] = $product['id'];
                    $prepared_data['name'] = $product['name'];
                    $prepared_data['image'] = str_replace($image_prefix, '',urldecode($product['image']));
                    $prepared_data['size_combination_id'] = $product['size']['id'];
                    $prepared_data['package_id'] = $item['id'];
                    $prepared_data['package_name'] = $item['name'];

                    if(isset($product['crop_info'])){
                        $prepared_data['crop_info_width'] = $product['crop_info']['width'] ?? null;
                        $prepared_data['crop_info_height'] = $product['crop_info']['height'] ?? null;
                        $prepared_data['crop_info_x'] = $product['crop_info']['x'] ?? null;
                        $prepared_data['crop_info_y'] = $product['crop_info']['y'] ?? null;
                    }

                    /** @var OrderItem $orderItem */
                    $orderItem = $orderItemRepo->create($prepared_data);
                    // Attach image
                    $orderItem->photos()->attach($product['image_id']);
                }
            }

            if (CartItemTypesEnum::PRODUCT()->is($item['type'])) {

                $prepared_data['product_id'] = $item['id'];
                $prepared_data['name'] = $item['name'];
                if(ProductTypesEnum::PRINTABLE()->is($item['product_type'])){
                    $prepared_data['image'] = str_replace($image_prefix, '',urldecode($item['image']));
                }
                $prepared_data['size_combination_id'] = $item['size']['id'];
                $prepared_data['retouch'] = $item['retouch'];

                if(isset($item['crop_info'])){
                    $prepared_data['crop_info_width'] = $item['crop_info']['width'] ?? null;
                    $prepared_data['crop_info_height'] = $item['crop_info']['height'] ?? null;
                    $prepared_data['crop_info_x'] = $item['crop_info']['x'] ?? null;
                    $prepared_data['crop_info_y'] = $item['crop_info']['y'] ?? null;
                }

                /** @var OrderItem $orderItem */
                $orderItem = $orderItemRepo->create($prepared_data);
                // Attach image
                $orderItem->photos()->attach($item['image_id']);
            }
        }

        // $cart_key = $cart->getSessionCartID();
        // $cartRepo->destroyBySessionKey($);
        // $cart->clear();

        $packages = $order->items()->whereNotNull('package_id')->get()->groupBy('item_id');
        $addons = $order->items()->whereNull('package_id')->get();

        // Start zip generation for Digital if customer get it for free
        if(OrderPaymentStatusEnum::PAID()->is($order['payment_status']) && ($order->isDigital() || $order->isDigitalFull())) {
            (new DigitalZipPreparingScenario($order['id']))->start();
        }
        // Send mail if customer get it for free
        if(OrderPaymentStatusEnum::PAID()->is($order['payment_status'])){
            event(new NewPaymentEvent($order, $packages, $addons, true));
            event(new ConfirmationPaymentEvent($order, $packages, $addons));
        }

        session()->put('order_id', $order->id);

        return response()->json(['status' => true, 'order_id' => $order->id], 200);
    }

    public function mainPage(Cart $cart, CartRepo $cartRepo) {
        $cart_key = $cart->getSessionCartID();
        $cartRepo->destroyBySessionKey($cart_key);
        $cart->clear();
        return view('main');
    }

    /**
     * Get pay form
     *
     * @param Cart $cart
     * @return Factory|JsonResponse|RedirectResponse|Redirector|View
     */
    public function getPayForm(Cart $cart, OrderRepo $orderRepo)
    {
        $order_id = session()->get('order_id');
        if(! $order_id){
            // if no order id try get cart data to find gallery password
            $cart_data = $cart->getContent();
            if($cart_data->isEmpty()){
                // if no card data & order id - redirect to main page
                return redirect()->route('main-page');
            }

            $item = $cart_data->first();
            // if no order id but cart set, go to cart
            return $this->redirect(url('app/'. $item['sub_gallery_password'] .'/cart'));
        }

        if(!$order = $orderRepo->getByID($order_id)){
            return response()->json(['status' => false], 404);
        }

        // if order id set - go to pay form
        return view('card_form', compact('order'));
    }

    /**
     * @param $order_hash
     * @param \App\Ecommerce\Orders\OrderRepo $orderRepo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getPayFormForUnpaidOrder($order_hash, OrderRepo $orderRepo)
    {
        if(!$order = $orderRepo->getByHash($order_hash)){
            return redirect()->route('main-page');
        }

        if($order->payment_status == OrderPaymentStatusEnum::PAID){
            return $this->getOrderPage($order->id, $orderRepo);
        }

        return view('card_form', ['order' => $order]);
    }


    /**
     * Send payment data to Authorize.net
     *
     * @param Request $request
     * @param OrderRepo $orderRepo
     * @return JsonResponse
     * @throws Exception
     */
    public function sendPaymentRequest(Request $request, OrderRepo $orderRepo)
    {
        $request['card_number'] = str_replace(' ', '', $request['card_number']);
        $request['expiration_date'] = str_replace('/', '', $request['expiration_date']);

        $cardData = $request->validate([
            'card_number' => 'required|digits_between:15,16|numeric',
            'expiration_date' => 'required|digits:4|numeric',
            'code' => 'required|digits_between:3,4|numeric',
            'order_id' => 'required|numeric'
        ]);

        session()->forget('order_id');

        /** @var Order $storedOrder */
        $storedOrder = $orderRepo->getByID($cardData['order_id']);

        /* Create a merchantAuthenticationType object with authentication details
           retrieved from the constants file */
        $merchantAuthentication = new MerchantAuthenticationType();
        $merchantAuthentication->setName(env('MERCHANT_LOGIN_ID'));
        $merchantAuthentication->setTransactionKey(env('MERCHANT_TRANSACTION_KEY'));

        // Set the transaction's refId
        $refId = 'ref' . time();

        // Create the payment data for a credit card
        $creditCard = new CreditCardType();
        $creditCard->setCardNumber($cardData['card_number']); // "4111111111111111"
        $creditCard->setExpirationDate($cardData['expiration_date']); // "1225"
        $creditCard->setCardCode($cardData['code']); // "123"

        // Add the payment data to a paymentType object
        $paymentOne = new PaymentType();
        $paymentOne->setCreditCard($creditCard);

        // Create order information
        $order = new OrderType();
        $order->setInvoiceNumber($storedOrder['id']);
        $order->setDescription("Photos ");

        // Set the customer's Bill To address
        $customerAddress = new CustomerAddressType();
        $customerAddress->setFirstName($storedOrder['customer_first_name']);
        $customerAddress->setLastName($storedOrder['customer_last_name']);
        $customerAddress->setAddress($storedOrder['address']);
        $customerAddress->setCity($storedOrder['city']);
        $customerAddress->setState($storedOrder['state']);
        $customerAddress->setZip($storedOrder['postal']);
        $customerAddress->setCountry($storedOrder['country']);

        // Set the customer's identifying information
        $customerData = new CustomerDataType();
        $customerData->setType("individual");
        $customerData->setId($storedOrder['customer_id']);
        $customerData->setEmail($storedOrder->customer['email']);

        // Add values for transaction settings
        $duplicateWindowSetting = new SettingType();
        $duplicateWindowSetting->setSettingName("duplicateWindow");
        $duplicateWindowSetting->setSettingValue("60");

        // Add some merchant defined fields. These fields won't be stored with the transaction,
        // but will be echoed back in the response.
        $merchantDefinedField1 = new UserFieldType();
        $merchantDefinedField1->setName("merchant");
        $merchantDefinedField1->setValue("PlayFul Portraits");

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($storedOrder['total']);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
        $transactionRequestType->addToUserFields($merchantDefinedField1);

        // Assemble the complete transaction request
        $request = new CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new CreateTransactionController($request);
        $endPoint = ANetEnvironment::PRODUCTION;
        if(env('A_NET_ENVIRONMENT') == 'sandbox'){
            $endPoint = ANetEnvironment::SANDBOX;
        }
        $response = $controller->executeWithApiResponse($endPoint);

        // Prepare response message
        $message = "No response returned \n";
        $statusCode = 500;
        $status = false;

        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == "Ok") {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getMessages() != null) {
                    $statusCode = 200;
                    $status = true;
                    $transactionId = $tresponse->getTransId();

                    $message = " Successfully created transaction with Transaction ID: " . $transactionId . "\n";

                    // Update order status
                    $storedOrder['payment_status'] = OrderPaymentStatusEnum::PAID();
                    $storedOrder['transaction_id'] = $transactionId;
                    $storedOrder->save();

                    // Start zip generation if Digital
                    if($storedOrder->isDigital() || $storedOrder->isDigitalFull()) {
                        (new DigitalZipPreparingScenario($storedOrder['id']))->start();
                    }
                    // Send
                    $packages = $storedOrder->items()->whereNotNull('package_id')->get()->groupBy('item_id');
                    $addons = $storedOrder->items()->whereNull('package_id')->get();

                    event(new NewPaymentEvent($storedOrder, $packages, $addons, true));
                    event(new ConfirmationPaymentEvent($storedOrder, $packages, $addons));

                } else {
                    $message = "Transaction Failed \n";
                    logger($message, ['response' => $tresponse, 'order_id' => $storedOrder['id']]);
                }
                // Or, log errors if the API request wasn't successful
            } else {
                $message = "Transaction Failed \n";
                $tresponse = $response->getTransactionResponse();

                logger($message, ['response' => $tresponse, 'order_id' => $storedOrder['id']]);
            }
        }else{
            logger($message, ['response' => $response, 'order_id' => $storedOrder['id']]);
        }


        return response()->json([
            'status' => $status,
            'message' => $message,
            'order_id' => $storedOrder['id']
        ], $statusCode);
    }

    /**
     * Check promo code is valid
     *
     * @param Request          $request
     * @param PromoCodeService $promoCodeService
     *
     * @return JsonResponse
     */
    public function checkIsPromoCodeValid(Request $request, PromoCodeService $promoCodeService, CustomerRepo $customerRepo)
    {
        $request->validate([
            'promo_code' => 'required',
            'cart_total' => 'required',
        ]);

        $code = $promoCodeService->getIfValid(
            $request->get('promo_code'),
            $request->get('cart_total'),
            $request->get('user_email')
        );

        if (! $code) {
            return response()->json(['status' => false], 200);
        }

        if($request->get('user_email')){
            $customer = $customerRepo->findOrCreateByEmail($request->get('user_email'));
            $customer->promoCodes()->save($code);
        }

        return response()->json(['status' => true, 'promo' => $code->toArray()], 200);
    }

    /**
     * Return order page information
     *
     * @param int       $order_id
     * @param OrderRepo $orderRepo
     *
     * @return Factory|View
     * @throws Exception
     */
    public function getOrderPage(int $order_id, OrderRepo $orderRepo, Cart $cart, CartRepo $cartRepo)
    {
        session()->forget('order_id');

        /** @var Order $order */
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order Not found');
        };

        if($order->payment_status == OrderPaymentStatusEnum::NOT_PAID){
            return redirect()->route('main-page');
        }

        $packages = $order->items()->whereNotNull('package_id')->get()->groupBy('item_id');
        
        $viewOld = false;
        foreach ($packages as $package) {
            if ($package[0]->subGallery()->first() == null) {
                $viewOld = true;
            }
        }
        // dd($packages['5e558feeacc66'][0]->subGallery()->first()->photos[0]->present()->previewUrl());
        // dd($packages['5e558feeacc66'][0]->subGallery()->first()->name);
        // dd($packages['5e558feeacc66'][0]->order()->first());

        $addons = $order->items()->whereNull('package_id')->get();
        // dd($order->subgallery);
        $text = DB::table('order_pages_text')->find(1); // todo: Need be moved to Settings
        $text_warning = '';

        $cart_key = $cart->getSessionCartID();
        $cartRepo->destroyBySessionKey($cart_key);
        $cart->clear();
        
        if ($viewOld) {
            return view('order_old', compact('order', 'packages', 'addons', 'text', 'text_warning'));
        } else {
            return view('order', compact('order', 'packages', 'addons', 'text', 'text_warning'));
        }
    }


    public function getOrderEmailPage(int $order_id, OrderRepo $orderRepo, Cart $cart, CartRepo $cartRepo)
    {

        /** @var Order $order */
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order Not found');
        };

        if($order->payment_status == OrderPaymentStatusEnum::NOT_PAID){
            return redirect()->route('main-page');
        }

        $packages = $order->items()->whereNotNull('package_id')->get()->groupBy('item_id');
        $addons = $order->items()->whereNull('package_id')->get();
        $text_warning = '<p style="color:red;"><strong><u>Your offer has expired</u></strong> because the ordering deadline has passed.</p>';

        return view('order', compact('order', 'packages', 'addons', 'text', 'text_warning'));
    }

    /**
     * @param int $order_id
     * @param \App\Ecommerce\Orders\OrderRepo $orderRepo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function getDownloadablePhotos(int $order_id, OrderRepo $orderRepo)
    {
        /** @var Order $order */
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order Not found');
        };

        $downloadableItems = $order->subGallery->photos;

        if($order->isDigitalFull()){
            $personalClassPhoto = $order->subGallery->person->classPersonalPhoto();
            $downloadableItems->push($personalClassPhoto);
        }

        return view('order_images_preview', compact('order', 'downloadableItems'));
    }

    /**
     * @param int $item_id
     * @param \App\Ecommerce\Orders\OrderItemRepo $itemRepo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function getDownloadablePhoto(int $item_id, OrderItemRepo $itemRepo)
    {
        if (! $item = $itemRepo->getByID($item_id)) {
            abort(404, 'Order Not found');
        };
        $order = $item->order;
        $image_url = $item->photo()->present()->originalUrl();

        return view('order_images_preview', compact('order', 'image_url'));
    }

    /**
     * Set free gift to order
     *
     * @param Request $request
     * @param OrderRepo $orderRepo
     *
     * @return ResponseFactory|JsonResponse|RedirectResponse|Redirector|Response
     * @throws Exception
     */
    public function setGiftToOrder(Request $request, OrderRepo $orderRepo)
    {
        $validData = $request->validate([
            'order_id' => 'required|integer'
        ]);

        if (! $order = $orderRepo->getByID($validData['order_id'])) {
            abort(404, 'Order Not found');
        };

        if(! OrderStatusEnum::NEW()->is($order['status'])){
            return response(['message' => "Can't add gift to order. Order is processed."], 500);
        }

        if(! $order['free_gift']) {
            $orderRepo->update($order['id'], ['free_gift' => true]);
        }

        return $this->redirect(route('order_page', $order['id']), 302);
    }

    public function getFormForGettingGift($order_id)
    {
        return view('get_gift', compact('order_id'));
    }
}

