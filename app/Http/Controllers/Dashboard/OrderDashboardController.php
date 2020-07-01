<?php

namespace App\Http\Controllers\Dashboard;

use App\Ecommerce\Orders\Order;
use App\Ecommerce\Orders\OrderDashboardPresenter;
use App\Ecommerce\Orders\OrderExportService;
use App\Ecommerce\Orders\OrderItem;
use App\Ecommerce\Orders\OrderItemRepo;
use App\Ecommerce\Orders\OrderPaymentStatusEnum;
use App\Ecommerce\Orders\OrderRepo;
use App\Ecommerce\Orders\OrderService;
use App\Ecommerce\Orders\OrderStatusEnum;
use App\Ecommerce\Packages\PackageRepo;
use App\Ecommerce\Products\ProductRepo;
use App\Ecommerce\Products\ProductTypesEnum;
use App\Ecommerce\PromoCodes\PromoCodeRepo;
use App\Events\ConfirmationPaymentEvent;
use App\Events\CustomerDataUpdatedEvent;
use App\Events\OrderCompositionUpdatedEvent;
use App\Events\OrderPaymentUpdatedEvent;
use App\Events\OrderStatusUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Photos\Photos\PhotoRepo;
use App\Photos\Seasons\SeasonRepo;
use App\Processing\Scenarios\OrderZipPreparingScenario;
use App\Services\GenerateZipWithPrintablePhotos;
use Illuminate\Support\Collection;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Storage;
use Laracasts\Presenter\Exceptions\PresenterException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Webmagic\Core\Controllers\AjaxRedirectTrait;
use Webmagic\Core\Entity\Exceptions\EntityNotExtendsModelException;
use Webmagic\Core\Entity\Exceptions\ModelNotDefinedException;
use Webmagic\Dashboard\Components\FormGenerator;
use Webmagic\Dashboard\Components\TableGenerator;
use Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable;
use Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined;
use Webmagic\Dashboard\Core\Content\JsActionsApplicable;
use Webmagic\Dashboard\Dashboard;
use Webmagic\Dashboard\Elements\Links\LinkButton;


class OrderDashboardController extends Controller
{
    use AjaxRedirectTrait;

    /**
     * Display a listing of orders.
     *
     * @param Request $request
     * @param OrderDashboardPresenter $dashboardPresenter
     * @param Dashboard $dashboard
     *
     * @return Dashboard
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws Exception
     */
    public function index(Request $request, OrderDashboardPresenter $dashboardPresenter, Dashboard $dashboard)
    {
        $orders = (new OrderRepo())->getByFilter(
            $request->get('galleries'),
            $request->get('subgalleries'),
            $request->get('price_lists'),
            $request->get('clients'),
            $request->get('statuses'),
            [OrderPaymentStatusEnum::PAID],
            $request->get('date_from'),
            $request->get('date_to'),
            $request->get('per_page', 10),
            $request->get('page', 1)
        );

        $allOrdersTable = $dashboardPresenter->getAllOrdersTable($orders, $request);

        $seasons = (new SeasonRepo())->getByFilter(
            $request->get('seasons'),
            $request->get('schools'),
            $request->get('per_page', 10),
            $request->get('page', 1)
        );

        $seasonTable = $dashboardPresenter->getOrdersFilteredBySeasonTable($seasons, $request);

        return $dashboardPresenter->prepareTabsPage($allOrdersTable, $seasonTable, $dashboard, $request);
    }

    /**
     * Display the specified resource.
     *
     * @param int                     $order_id
     * @param OrderRepo               $orderRepo
     * @param OrderDashboardPresenter $dashboardPresenter
     * @param Dashboard               $page
     *
     * @return Response
     * @throws NoOneFieldsWereDefined
     * @throws Exception
     */
    public function show(int $order_id, OrderRepo $orderRepo, OrderDashboardPresenter $dashboardPresenter, Dashboard $page)
    {
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };

        $packages = $order->items()->whereNotNull('package_id')->groupBy('order_id')->get();
        // $packages = $order->items()->whereNotNull('package_id')->get()->groupBy('order_id');
        
        $collectionObj = new Collection();
        $collectionObj->push((object) $packages);
        $packages = $collectionObj;

        $addons = $order->items()->whereNull('package_id')->groupBy('order_id')->get();
        
        $packages = $order->items()->whereNotNull('package_id')->get()->groupBy('item_id');
        $addons = $order->items()->whereNull('package_id')->get();

        $page = $dashboardPresenter->getDescriptionList($order, $page);

        return $dashboardPresenter->generateOrderItemsTable($order, $packages, $addons, $page);
    }

    /**
     * Destroy order with order items
     *
     * @param int $order_id
     * @param OrderRepo $orderRepo
     *
     * @throws EntityNotExtendsModelException
     * @throws ModelNotDefinedException
     * @throws Exception
     */
    public function destroy(int $order_id, OrderRepo $orderRepo)
    {
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };

        if (! $orderRepo->destroy($order_id)) {
            abort(404, 'Error on order deleting');
        };
    }

    /**
     * Show popup for edit payment status
     *
     * @param int                     $order_id
     * @param OrderRepo               $orderRepo
     * @param OrderDashboardPresenter $dashboardPresenter
     *
     * @return FormGenerator
     * @throws Exception
     */
    public function editPaymentStatus(int $order_id, OrderRepo $orderRepo, OrderDashboardPresenter $dashboardPresenter)
    {
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };
        $payment_statuses = OrderPaymentStatusEnum::values();

        return $dashboardPresenter->getPopupForUpdatePaymentStatus($order, $payment_statuses);
    }

    /**
     * Update payment status
     *
     * @param int       $order_id
     * @param OrderRepo $orderRepo
     * @param Request   $request
     *
     * @return JsonResponse|RedirectResponse|Redirector
     * @throws Exception
     */
    public function updatePaymentStatus(int $order_id, OrderRepo $orderRepo, Request $request)
    {
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };

        $order->update(['payment_status' => $request->get('payment_status')]);

        event(new OrderPaymentUpdatedEvent($order));

        return $this->redirect(route('dashboard::orders.show', $order_id));
    }

    /**
     * Show popup for edit status
     *
     * @param int                     $order_id
     * @param OrderRepo               $orderRepo
     * @param OrderDashboardPresenter $dashboardPresenter
     *
     * @return FormGenerator
     * @throws Exception
     */
    public function editStatus(int $order_id, OrderRepo $orderRepo, OrderDashboardPresenter $dashboardPresenter)
    {
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };
        $payment_statuses = OrderStatusEnum::values();

        return $dashboardPresenter->getPopupForUpdateStatus($order, $payment_statuses);
    }

    /**
     * Update order status
     *
     * @param int       $order_id
     * @param OrderRepo $orderRepo
     * @param Request   $request
     *
     * @return JsonResponse|RedirectResponse|Redirector
     * @throws Exception
     */
    public function updateStatus(int $order_id, OrderRepo $orderRepo, Request $request)
    {
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };

        $order->update(['status' => $request->get('status')]);

        event(new OrderStatusUpdatedEvent($order));

        return $this->redirect(route('dashboard::orders.show', $order_id));
    }

    /**
     * Show form in popup for edit customer data
     *
     * @param int                     $order_id
     * @param OrderRepo               $orderRepo
     * @param OrderDashboardPresenter $dashboardPresenter
     *
     * @return FormGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws Exception
     */
    public function editCustomerDetails(int $order_id, OrderRepo $orderRepo, OrderDashboardPresenter $dashboardPresenter)
    {
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };

        return $dashboardPresenter->getFormForEditCustomer($order);
    }

    /**
     * Update customer data
     *
     * @param int       $order_id
     * @param OrderRepo $orderRepo
     * @param Request   $request
     *
     * @return JsonResponse|RedirectResponse|Redirector
     * @throws Exception
     */
    public function updateCustomerDetails(int $order_id, OrderRepo $orderRepo, Request $request)
    {
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };

        $request['receive_promotions_by_email'] = $request->get('receive_promotions_by_email') ? true : false;
        $orderRepo->update($order_id, $request->all());
        $updatedOrder = $orderRepo->getByID($order_id);

        event(new CustomerDataUpdatedEvent($updatedOrder, $order));

        return $this->redirect(route('dashboard::orders.show', $order_id));
    }

    /**
     * Download csv with order details
     *
     * @param int       $order_id
     * @param OrderRepo $orderRepo
     *
     * @return BinaryFileResponse
     * @throws Exception
     */
    public function downloadOrderDetails(int $order_id, OrderRepo $orderRepo)
    {
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };

        if($order->isZipPreparingInProgress()){
            return $this->redirect(route('dashboard::orders.show', $order_id));
        }

        $exportFile = (new OrderExportService())->generateAndSaveOrderDetailsCsv($order);

        return response()->download($exportFile)->deleteFileAfterSend(true);
    }

    /**
     * Show form for choose promo code
     *
     * @param int                     $order_id
     * @param OrderRepo               $orderRepo
     * @param OrderDashboardPresenter $dashboardPresenter
     * @param PromoCodeRepo           $promoCodeRepo
     *
     * @return FormGenerator
     * @throws Exception
     */
    public function editPromoCode(
        int $order_id,
        OrderRepo $orderRepo,
        OrderDashboardPresenter $dashboardPresenter,
        PromoCodeRepo $promoCodeRepo
    ) {
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };

        $promo_codes = $promoCodeRepo->getForSelectWithFullName();
        $promo_codes[0] = 'None';

        return $dashboardPresenter->getFormForEditPromoCode($order, $promo_codes);
    }

    /**
     * Update cart in order with chosen promo code
     *
     * @param int $order_id
     * @param OrderRepo $orderRepo
     * @param Request $request
     * @param PromoCodeRepo $promoCodeRepo
     * @param OrderService $orderService
     * @return JsonResponse|RedirectResponse|Redirector
     * @throws Exception
     */
    public function updatePromoCode(
        int $order_id,
        OrderRepo $orderRepo,
        Request $request,
        PromoCodeRepo $promoCodeRepo,
        OrderService $orderService
    ) {
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };

        $promo_code = $promoCodeRepo->getByID($request->get('promo_code'));

        $orderService->recalculateWithPromo($order, $promo_code);

        $updatedOrder = $orderRepo->getByID($order_id);
        event(new OrderCompositionUpdatedEvent($updatedOrder));

        return $this->redirect(route('dashboard::orders.show', $order_id));
    }

    /**
     * Get popup with filter for adding addon to order cart
     *
     * @param int                     $order_id
     * @param ProductRepo             $productRepo
     * @param Request                 $request
     * @param OrderDashboardPresenter $dashboardPresenter
     * @param OrderRepo               $orderRepo
     *
     * @return TableGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws Exception
     */
    public function addAddon(
        int $order_id,
        ProductRepo $productRepo,
        Request $request,
        OrderDashboardPresenter $dashboardPresenter,
        OrderRepo $orderRepo
    ) {
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };

        if($request->get('type') || $request->get('name')){
            $products = $productRepo->getByFilter($request->get('type'), $request->get('name'));
        } else {
            $products = $productRepo->getAll();
        }
        $types = ProductTypesEnum::values();

        $addonsTable = $dashboardPresenter->getAddonsTableForPopup($order, $products);

        $addonsTable->addFiltering()
            ->action(route('dashboard::orders.addon.add', $order_id))
            ->selectJS('type[]', array_combine($types, $types), ['type[]' => $request->get('type')], 'Type', false, true)
            ->textInput('name', $request->get('name'), 'Name');

        $addonsTable->getFilter()->addElement()->button('Filter')->type('submit')->dataAttr('modal-hide', 'false');
        $addonsTable->getFilter()->content()->getItem(0)->content()->attr('style', 'width:200px;');

        return $addonsTable;
    }

    /**
     * Add addon to order cart and recalculate cart info
     *
     * @param int           $order_id
     * @param Request       $request
     * @param ProductRepo   $productRepo
     * @param OrderRepo     $orderRepo
     * @param OrderItemRepo $itemRepo
     * @param OrderService  $orderService
     *
     * @return string
     * @throws Exception
     */
    public function saveAddon(
        int $order_id,
        Request $request,
        ProductRepo $productRepo,
        OrderRepo $orderRepo,
        OrderItemRepo $itemRepo,
        OrderService $orderService
    ) {
        if (! $product = $productRepo->getByID($request->get('id'))) {
            abort(404, 'Product not found');
        };

        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };

        $price = $request->get('price', $product->default_price);
        $size = $product->sizes->first();

        $orderItem = $itemRepo->create([
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => $price,
            'quantity' => 1,
            'sum' => $price,
            'order_id' => $order->id,
            'item_id' => hash('adler32', time().$order->id),
            'image' => $order->subgallery->present()->mainPhotoPreviewUrl(),
            'size_combination_id' => $size ? $size->id : null
        ]);

        $orderService->recalculateByItems($order);

        $orderItem->photos()->attach($order->subgallery->mainPhoto()->id);

        $updatedOrder = $orderRepo->getByID($order_id);
        event(new OrderCompositionUpdatedEvent($updatedOrder));


        //GenerateOrdersPrintablePhotosZip::dispatch($order)->onQueue('generate_order_printable');

    }

    /**
     * Delete cart item by special hash and recalculate order
     *
     * @param int                                $order_id
     * @param                                    $item_id
     * @param OrderItemRepo                      $itemRepo
     * @param OrderService                       $orderService
     * @param OrderRepo                          $orderRepo
     *
     * @return JsonResponse|RedirectResponse|Redirector
     * @throws Exception
     */
    public function deleteItem(
        int $order_id,
        $item_id,
        OrderItemRepo $itemRepo,
        OrderService $orderService,
        OrderRepo $orderRepo
    ) {
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };

        if (! $itemRepo->destroyByItemID($item_id)) {
            abort(500, 'Error on cart item destroying');
        }

        $orderService->recalculateByItems($order);
        //GenerateOrdersPrintablePhotosZip::dispatch($order)->onQueue('generate_order_printable');

        return $this->redirect(route('dashboard::orders.show', $order_id));
    }

    /**
     *
     * Get popup with filter for adding package to order cart
     *
     * @param int                     $order_id
     * @param PackageRepo             $packageRepo
     * @param Request                 $request
     * @param OrderDashboardPresenter $dashboardPresenter
     * @param OrderRepo               $orderRepo
     *
     * @return TableGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws Exception
     */
    public function addPackage(
        int $order_id,
        PackageRepo $packageRepo,
        Request $request,
        OrderDashboardPresenter $dashboardPresenter,
        OrderRepo $orderRepo
    ) {
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };

        if ($request->get('name')) {
            $packages = $packageRepo->getByName($request->get('name'));
        } else {
            $packages = $packageRepo->getAll();
        }
        $packageTable = $dashboardPresenter->getPackagesTableForPopup($order, $packages);

        $packageTable->addFiltering()
            ->action(route('dashboard::orders.package.add', $order_id))
            ->textInput('name', $request->get('name'), 'Name');

        $packageTable->getFilter()->addElement()->button('Filter')->type('submit')->dataAttr('modal-hide', 'false');
        $packageTable->getFilter()->content()->getItem(0)->content()->attr('style', 'width:200px;');

        return $packageTable;
    }

    /**
     * Add package to order cart and recalculate cart info
     *
     * @param int           $order_id
     * @param Request       $request
     * @param PackageRepo   $packageRepo
     * @param OrderRepo     $orderRepo
     * @param OrderItemRepo $itemRepo
     * @param OrderService  $orderService
     *
     * @throws Exception
     */
    public function savePackage(
        int $order_id,
        Request $request,
        PackageRepo $packageRepo,
        OrderRepo $orderRepo,
        OrderItemRepo $itemRepo,
        OrderService $orderService
    ) {
        $request->validate([
            'price' => 'numeric|min:0'
        ]);

        if (! $package = $packageRepo->getByID($request->get('id'))) {
            abort(404, 'Product not found');
        };

        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };

        $price = $request->get('price', $package->price);
        $hash =  hash('adler32', time().$order->id);

        foreach ($package->products as $product) {
            $size = $product->sizes->first();
            $orderItem = $itemRepo->create([
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $price,
                'quantity' => 1,
                'sum' => $price,
                'order_id' => $order->id,
                'item_id' => $hash,
                'image' => $order->subgallery->present()->mainPhotoPreviewUrl(),
                'package_id' => $package->id,
                'package_name' => $package->name,
                'size_combination_id' => $size ? $size->id : null
            ]);

            $orderItem->photos()->attach($order->subgallery->mainPhoto()->id);
        }

        $orderService->recalculateByItems($order);

        $updatedOrder = $orderRepo->getByID($order_id);
        event(new OrderCompositionUpdatedEvent($updatedOrder));

        //GenerateOrdersPrintablePhotosZip::dispatch($order)->onQueue('generate_order_printable');
    }

    /**
     * Get edit for change item quantity
     *
     * @param int                                          $order_id
     * @param                                              $cart_item_id
     * @param OrderRepo                                    $orderRepo
     * @param OrderDashboardPresenter                      $dashboardPresenter
     * @param OrderItemRepo                                $itemRepo
     *
     * @return FormGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws Exception
     */
    public function editQuantity(
        int $order_id,
        $cart_item_id,
        OrderRepo $orderRepo,
        OrderDashboardPresenter $dashboardPresenter,
        OrderItemRepo $itemRepo
    ) {
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };

        if (! $item = $itemRepo->getByItemID($cart_item_id)) {
            abort(500, 'Cart item not found');
        }

        return $dashboardPresenter->getFormForEditQuantity($item, $order);
    }

    /**
     * Update quantity of items with recalculate order
     *
     * @param int           $order_id
     * @param $item_id
     * @param OrderItemRepo $itemRepo
     * @param OrderService  $orderService
     * @param OrderRepo     $orderRepo
     * @param Request       $request
     *
     * @throws Exception
     */
    public function updateQuantity(
        int $order_id,
        $item_id,
        OrderItemRepo $itemRepo,
        OrderService $orderService,
        OrderRepo $orderRepo,
        Request $request
    ) {
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };

        if (! $order_item = $itemRepo->getByItemID($item_id)) {
            abort(500, 'Cart item not found');
        }

        $itemRepo->updateByItemID($item_id,[
            'quantity' => $request->get('quantity'),
            'sum' => $request->get('quantity') * $order_item->price,
        ]);

        $orderService->recalculateByItems($order);

        $updatedOrder = $orderRepo->getByID($order_id);
        event(new OrderCompositionUpdatedEvent($updatedOrder));

        //GenerateOrdersPrintablePhotosZip::dispatch($order)->onQueue('generate_order_printable');
    }

    /**
     * Show form for edit product size
     *
     * @param int                     $order_id
     * @param $item_id
     * @param OrderRepo               $orderRepo
     * @param OrderDashboardPresenter $dashboardPresenter
     * @param OrderItemRepo           $itemRepo
     * @param ProductRepo             $productRepo
     *
     * @return FormGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws Exception
     */
    public function editSize(
        int $order_id,
        $item_id,
        OrderRepo $orderRepo,
        OrderDashboardPresenter $dashboardPresenter,
        OrderItemRepo $itemRepo,
        ProductRepo $productRepo
    ) {
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };

        if (! $order_item = $itemRepo->getByID($item_id)) {
            abort(500, 'Cart item not found');
        }

        $sizes = $productRepo->getAvailableSizesForProduct($order_item->product_id);

        return $dashboardPresenter->getFormForEditSize($order_item, $order, $sizes);
    }

    /**
     * Update size for product in order
     *
     * @param int           $order_id
     * @param               $item_id
     * @param OrderItemRepo $itemRepo
     * @param OrderRepo     $orderRepo
     * @param Request       $request
     *
     * @throws Exception
     */
    public function updateSize(
        int $order_id,
        $item_id,
        OrderItemRepo $itemRepo,
        OrderRepo $orderRepo,
        Request $request
    ) {
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };

        if(! $itemRepo->update($item_id,[
            'size_combination_id' => $request->get('size'),
        ])){
            abort(500, 'Error on order item updating');
        };

        $updatedOrder = $orderRepo->getByID($order_id);
        event(new OrderCompositionUpdatedEvent($updatedOrder));

        //GenerateOrdersPrintablePhotosZip::dispatch($order)->onQueue('generate_order_printable');
    }

    /**
     * Show form for update product image
     *
     * @param int                     $order_id
     * @param                         $item_id
     * @param OrderRepo               $orderRepo
     * @param OrderDashboardPresenter $dashboardPresenter
     * @param OrderItemRepo           $itemRepo
     *
     * @return TableGenerator
     * @throws Exception
     */
    public function editImage(
        int $order_id,
        $item_id,
        OrderRepo $orderRepo,
        OrderDashboardPresenter $dashboardPresenter,
        OrderItemRepo $itemRepo
    ) {
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };

        if (! $orderItem = $itemRepo->getByID($item_id)) {
            abort(500, 'Order item not found');
        }

        $photos = $order->subgallery->photos;

        foreach ($photos as $key => $photo) {
            $imageData[] = [
                'id' => $photo->id,
                'image' => $photo->present()->previewUrl(),
            ];
        }

        return $dashboardPresenter->getImagesTableForPopup($order, $orderItem, $imageData);
    }

    /**
     * Update image for product in order
     *
     * @param int           $orderId
     * @param               $itemId
     * @param OrderItemRepo $itemRepo
     * @param OrderRepo     $orderRepo
     * @param Request       $request
     *
     * @throws Exception
     */
    public function updateImage(
        int $orderId,
        $itemId,
        OrderItemRepo $itemRepo,
        OrderRepo $orderRepo,
        Request $request
    ) {
        $validData = $request->validate([
            'photo_id' => 'required|exists:photos,id'
        ]);

        if (! $order = $orderRepo->getByID($orderId)) {
            abort(404, 'Order not found');
        };

        $newPhoto = (new PhotoRepo())->getByID($validData['photo_id']);
        /** @var OrderItem $orderItem */
        $orderItem = $itemRepo->getByID($itemId);
        $orderItem['image'] = $newPhoto->present()->previewUrl();
        $orderItem->save();

        //Update photo
        $orderItem->photos()->sync($newPhoto->id);

        if(!$orderItem){
            abort(500, 'Error on order item updating');
        };

        event(new OrderCompositionUpdatedEvent($order));

//        GenerateOrdersPrintablePhotosZip::dispatch($order)->onQueue('generate_order_printable');
    }

    /**
     * Download zip with resizing printable photos for order
     * If zip exist on s3 just download, in another way zip will create, pull to s3 and then download
     *
     * @param int                            $order_id
     * @param OrderRepo                      $orderRepo
     * @param OrderService                   $orderService
     * @param GenerateZipWithPrintablePhotos $zipWithPrintablePhotos
     *
     * @return mixed
     * @throws Exception
     */
    public function downloadPhotosForPrintByOrder(
        int $order_id,
        OrderRepo $orderRepo,
        OrderService $orderService,
        GenerateZipWithPrintablePhotos $zipWithPrintablePhotos
    ) {
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };
        $items = $orderService->getPrintableItems($order);

        $zipPath = $order->present()->exportFilePath();

        //todo UPDATE LOGIC
        $exists = Storage::disk('s3')->exists($zipPath);

        if (! $exists) {
            $zipWithPrintablePhotos->generateForOrder($order, $items);
        }

        return Storage::disk('s3')->download($zipPath);
    }

    /**
     * Download zip with printable photos and csv with order details
     * then delete zip
     *
     * @param OrderRepo                      $orderRepo
     * @param GenerateZipWithPrintablePhotos $zipWithPrintablePhotos
     *
     * @return BinaryFileResponse
     * @throws Exception
     */
    public function downloadPhotosForPrintFromAllOrders(
        OrderRepo $orderRepo,
        GenerateZipWithPrintablePhotos $zipWithPrintablePhotos
    ) {
        if (! $orders = $orderRepo->getAllPaid()) {
            abort(404, 'Orders not found');
        };

        $zip_path = $zipWithPrintablePhotos->generateForAllOrders($orders);
        $file = Storage::disk('public')->path($zip_path);

        return response()->download($file)->deleteFileAfterSend(true);
    }

    /**
     * Send order details to customer
     *
     * @param int       $order_id
     * @param OrderRepo $orderRepo
     *
     * @return JsonResponse|RedirectResponse|Redirector
     * @throws Exception
     */
    public function sendOrderDetails(int $order_id, OrderRepo $orderRepo)
    {
        if (! $order = $orderRepo->getByID($order_id)) {
            abort(404, 'Order not found');
        };

        $packages = $order->items()->whereNotNull('package_id')->get()->groupBy('item_id');
        $addons = $order->items()->whereNull('package_id')->get();

        event(new ConfirmationPaymentEvent($order, $packages, $addons));

        return $this->redirect(route('dashboard::orders.show', $order_id));
    }

    /**
     * @param int $orderId
     * @param OrderRepo $orderRepo
     *
     * @return mixed|JsActionsApplicable|LinkButton
     * @throws NoOneFieldsWereDefined
     * @throws PresenterException
     * @throws Exception
     */
    public function zipPreparingStatus(int $orderId, OrderRepo $orderRepo)
    {
        /** @var Order $order */
        $order = $orderRepo->getByID($orderId);

        if (!$order) {
            abort(404);
        }
        if($order->isZipPrepared()){
            return response($order->dashboardElements()->zipProcessingButton())
                ->header('X-Update-Action', 'update-stop');
        }

        return $order->dashboardElements()->zipProcessingButton();
    }

    /**
     * @param int $orderId
     * @param OrderRepo $orderRepo
     *
     * @return mixed|JsActionsApplicable|LinkButton
     * @throws NoOneFieldsWereDefined
     * @throws PresenterException
     * @throws Exception
     */
    public function zipPreparingStart(int $orderId, OrderRepo $orderRepo)
    {
        /** @var Order $order */
        $order = $orderRepo->getByID($orderId);

        if (!$order) {
            abort(404);
        }

        (new OrderZipPreparingScenario($orderId))->start();

        return $order->dashboardElements()->zipProcessingButton();
    }

    /**
     * @param int $orderId
     * @param OrderRepo $orderRepo
     *
     * @return mixed|JsActionsApplicable|LinkButton
     * @throws NoOneFieldsWereDefined
     * @throws PresenterException
     * @throws Exception
     * @throws Exception
     */
    public function zipDigitalPreparingStatus(int $orderId, OrderRepo $orderRepo)
    {
        /** @var Order $order */
        $order = $orderRepo->getByID($orderId);

        if(!$order) {
            abort(404);
        }

        if($order->isDigitalZipPrepared()){
            return response()->json(['link' => $order->present()->zipDigitalUrl()], 200);
        }

        return \response()->json(['link' => null], 200);
    }
}
