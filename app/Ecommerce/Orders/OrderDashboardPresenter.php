<?php

namespace App\Ecommerce\Orders;


use App\Ecommerce\Packages\Package;
use App\Ecommerce\Products\Product;
use App\Photos\Galleries\GalleryRepo;
use App\Photos\Schools\SchoolRepo;
use App\Photos\Seasons\Season;
use App\Photos\Seasons\SeasonDashboardControlsGenerator;
use App\Photos\Seasons\SeasonRepo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Webmagic\Dashboard\Components\FormGenerator;
use Webmagic\Dashboard\Components\TableGenerator;
use Webmagic\Dashboard\Components\TablePageGenerator;
use Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable;
use Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined;
use Webmagic\Dashboard\Core\Content\JsActionsApplicable;
use Webmagic\Dashboard\Dashboard;
use Webmagic\Dashboard\Elements\Forms\Elements\Input;
use Webmagic\Dashboard\Elements\Links\Link;
use Webmagic\Dashboard\Elements\Links\LinkButton;
use Webmagic\Dashboard\Pages\BasePage;

class OrderDashboardPresenter
{

    /**
     * All Orders table
     *
     * @param $orders
     * @param Request $request
     * @return TableGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws \Exception
     */
    public function getAllOrdersTable($orders, Request $request)
    {
        $tableGenerator = (new TableGenerator())
            ->items($orders)
            ->tableTitles('ID', 'Creating date', 'Status', 'Payment status','Client name', 'Gallery name', 'Sub-gallery name', 'Price list', 'Items count', 'Subtotal', 'Discount',  'Total')
            ->showOnly('id', 'created_at', 'status', 'payment', 'customer_name', 'gallery', 'subgallery', 'price_list', 'items_count', 'subtotal', 'discount', 'total')
            ->setConfig([
                'payment' => function (Order $order) {
                    if ($order->payment_status == OrderPaymentStatusEnum::PAID) {
                        return  '<span class="text-green" title="'. OrderPaymentStatusEnum::PAID .'"><i class="fa fa-dollar"></i></span>';
                    }

                    return '<span class="text-red" title="'. OrderPaymentStatusEnum::NOT_PAID .'"><i class="fa fa-dollar"></i></span>';
                },
                'customer_name' => function (Order $order) {
                    return (new Link())->content($order->customer_first_name .' '. $order->customer_last_name)->link(route('dashboard::orders.show', $order));
                },
                'gallery' => function (Order $order) {
                    return (new Link())->content($order->gallery->present()->name)->link(route('dashboard::gallery.show', $order->gallery_id));
                },
                'subgallery' => function (Order $order) {
                    return (new Link())->content($order->subGallery->name)->link(route('dashboard::gallery.subgallery.show', $order->sub_gallery_id));
                },
                'price_list' => function (Order $order) {
                    return (new Link())->content($order->priceList->name)->link(route('dashboard::price-lists.show', $order->price_list_id));
                },
                'items_count' => function (Order $order) {
                    return $order->items_count;
                },
                'subtotal' => function (Order $order) {
                    return '$ '.$order->subtotal;
                },
                'discount' => function (Order $order) {
                    return $order->total_coupon ? '$ '.$order->total_coupon : '';
                },
                'total' => function (Order $order) {
                    return '$ '.$order->total;
                },
            ])
            ->withPagination($orders, route('dashboard::orders.index', $request->all()) )
            ->setShowLinkClosure(function (Order $order) {
                return route('dashboard::orders.show', $order);
            })
            ->setDestroyLinkClosure(function (Order $order) {
                return route('dashboard::orders.destroy', $order);
            })
        ;

        $galleries_for_select = [];
        $orderRepo = (new OrderRepo());
        $ordersForQueryGalleries = $orderRepo->getAll()->pluck('gallery_id')->toArray();
        $galleries = (new GalleryRepo())->getByIds($ordersForQueryGalleries);
        foreach ($galleries as $gallery){
            $galleries_for_select[$gallery->id] = $gallery->present()->name();
        }

        $subGalleries_for_select = $orderRepo->getSubGalleriesForSelect();
        $price_lists_for_select = $orderRepo->getPriceListsForSelect();
        $clients_for_select = $orderRepo->getClientsForSelect();
        $statuses_for_select = array_combine(OrderStatusEnum::values(), OrderStatusEnum::values());
        //$payment_statuses_for_select = array_combine(OrderPaymentStatusEnum::values(), OrderPaymentStatusEnum::values());

        $tableGenerator->addFiltering()
            ->action(route('dashboard::orders.index'))
            ->method('post')
            ->datePickerJS('date_from',  $request->get('date_from'), 'From')
            ->datePickerJs('date_to', $request->get('date_to'), 'To');

        $tableGenerator->getFilter()->addContent('<br>');
        (new FormGenerator($tableGenerator->getFilter()))
            ->selectJS('galleries[]', $galleries_for_select, ['galleries[]' => $request->get('galleries')], 'Gallery name', false, true)
            ->selectJS('subgalleries[]', $subGalleries_for_select, ['subgalleries[]' => $request->get('subgalleries')], 'Sub-gallery name', false, true)
            ->selectJS('price_lists[]', $price_lists_for_select, ['price_lists[]' => $request->get('price_lists')], 'Price list', false, true)
            ->selectJS('clients[]', $clients_for_select, ['clients[]' => $request->get('clients')], 'Client', false, true)
            ->selectJS('statuses[]', $statuses_for_select, ['statuses[]' => $request->get('statuses')], 'Status', false, true)
            //->selectJS('payments[]', $payment_statuses_for_select, ['payments[]' => $request->get('payments')], 'Payment', false, true)
            ->addClearButton(['class' => 'btn btn-default margin'], 'Clear')
            ->addSubmitButton(['class' => 'btn btn-info margin'], 'Filter');

        return $tableGenerator;
    }

    /**
     * Orders by season table
     *
     * @param $seasons
     * @param Request $request
     * @return TableGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws \Exception
     * @throws \Exception
     */
    public function getOrdersFilteredBySeasonTable($seasons, Request $request)
    {
        $data = $request->all();
        $data['seasons_tab_filter'] = true;

        $tableGenerator = (new TableGenerator())
            ->items($seasons)
            ->tableTitles('ID', 'Season', 'School', 'Orders count', 'Export controls')
            ->showOnly('id', 'season', 'school', 'orders_count', 'zip_generate_btn')
            ->setConfig([
                'season' => function (Season $season) {
                    return $season->gallery ? (new Link())->content($season->name)->link(route('dashboard::gallery.show', $season->gallery->id)) : $season->name;
                },
                'school' => function (Season $season) {
                    return $season->school->name;
                },
                'orders_count' => function (Season $season) {
                    return $season->gallery ? $season->gallery->orders->where('payment_status', OrderPaymentStatusEnum::PAID)->count() : 0;
                },

                'zip_generate_btn' => function (Season $season) {
                    if($season->gallery && $season->gallery->orders->where('payment_status', OrderPaymentStatusEnum::PAID)->count()) {
                        return $season->dashboardElements()->zipProcessingButton();
                    }
                    return '';
                }

            ])
            ->withPagination($seasons, route('dashboard::orders.index', $data));

        $seasons_for_select = (new SeasonRepo())->getForSelect('name', 'id');
        $schools_for_select = (new SchoolRepo())->getForSelect('name', 'id');

        $tableGenerator->addFiltering()
            ->action(route('dashboard::orders.index'))
            ->method('post')
            ->hiddenInput('seasons_tab_filter', true)
            ->selectJS('seasons[]', $seasons_for_select, ['seasons[]' => $request->get('seasons')], 'Season name', false, true, ['style'=>'width: 200px'])
            ->selectJS('schools[]', $schools_for_select, ['schools[]' => $request->get('schools')], 'School name', false, true, ['style'=>'width: 200px'])
            ->submitButton('Filter')
        ;

        return $tableGenerator;
    }


    /**
     * Description Page List
     *
     * @param Order $order
     * @param Dashboard $dashboard
     *
     * @return BasePage
     * @throws NoOneFieldsWereDefined
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     * @throws \Exception
     */
    public function getDescriptionList(Order $order, Dashboard $dashboard)
    {
        $gallery_link  = (new Link())->content($order->gallery->present()->name)
            ->link(route('dashboard::gallery.show', $order->gallery_id));
        $subgalleryLink  = (new Link())->content($order->subgallery->name)
            ->link(route('dashboard::gallery.subgallery.show', $order->sub_gallery_id));
        $price_list_link = (new Link())->content($order->priceList->name)
            ->link(route('dashboard::price-lists.show', $order->price_list_id));
        $email_send_link = (new Link())->content($order->customer->email)
            ->link(url("mailto:{$order->customer->email}"));

        $page = $dashboard->page();
        $page->setPageTitle("Order details")
            ->element()->grid()
            ->lgRowCount(3)
            ->mdRowCount(2)
            ->smRowCount(1)
            ->xsRowCount(1)
            ->element()
            ->box()
            ->boxTitle('Order details')
            ->footerAvailable(false)
            ->addElement()->descriptionList(
                ['data' => [
                    'ID:' => $order->id,
                    'Status:' => $order->status,
                    'Payment status:' => $order->payment_status,
                    'Placing date:' => $order->created_at,
                    'Gallery:' => $gallery_link,
                    'Sub gallery:' => $subgalleryLink,
                    'Price list:' => $price_list_link,
                    'Items count:' => $order->items_count,
                    'Subtotal:' => '$ ' . $order->subtotal,
                    $order->discount ? 'Discount (' .$order->discount_name.') :' : 'Discount:'  => ($order->discount ? ('$ '.$order->total_coupon) : 0),
                    'Free gift:' => $order->free_gift ? 'included': 'not included',
                    'Total:' => '$ ' . $order->total,
                ],
                ])->isHorizontal(true)
            ->parent()
            ->addElement('box_tools')->linkButton()->content('See as client')->link(route('order_page', [$order]))
            ->class('btn-default')
            ->js()->tooltip()->regular('View order on the public page')
            ->parent()

            ->addElement('box_tools')->linkButton()->content('Update status')
            ->link(url('#'))->class('btn-success')
            ->js()->openInModalOnClick()->smallModal(route('dashboard::orders.status.edit', $order), 'GET', 'Choose a new status', true)


            ->parent()
            ->addElement('box_tools')->linkButton()->content('Update payment')
            ->link(url('#'))->class('btn-success')
            ->js()->openInModalOnClick()->smallModal(route('dashboard::orders.payment-status.edit', $order), 'GET', 'Choose a new payment status', true)

            ->parent('grid')
            ->addElement()

            // Customer box
            ->box()
            ->boxTitle('Customer details')
            ->footerAvailable(false)
            //Link
            ->addElement('box_tools')->linkButton()->content('Edit')->icon('fa fa-edit ')
            ->class('pull-right btn btn-flat btn-default')
            ->js()->openInModalOnClick()->regular(route('dashboard::orders.customer.edit', $order), 'GET', 'Edit customer details')
            ->parent()
            ->addElement()->descriptionList(
                ['data' => [
                    'First name:' => $order->customer_first_name,
                    'Last name:' => $order->customer_last_name,
                    'E-mail:' => $email_send_link,
                    'Address:' => $order->address,
                    'City:' => $order->city,
                    'State:' => $order->state,
                    'Postal:' => $order->postal,
                    'Country:' => $order->country,
                    'Receive promotions:' => $order->receive_promotions_by_email ? 'yes' : 'no',
                    'Message:' => $order->message,
                ],
                ])->isHorizontal(true)
            ->parent('grid')
            ->addElement()
            ->box()
            ->boxTitle('Order actions')
            ->footerAvailable(false)
            ->addElement()->descriptionList(
                [ 'data' => [
                    'Download full order data in ZIP:' => $order->dashboardElements()->zipProcessingButton(),
                    '-' => ' ',
                    'Export order details in CSV' => (new LinkButton())->content(' Download order details')
                        ->class('btn-info')
                        ->icon('fa-download')
                        ->link(route('dashboard::orders.details.download', $order)),
                    '--' => ' ',
                    'Send updated order details on the customer email' => (new LinkButton())->content('Send order details')
                        ->js()->tooltip()->regular('All order details will be sent to the client email')
                        ->icon('fa-envelope-o')
                        ->link(route('dashboard::orders.send-order-details', $order->id))->class('btn-info')
                ]
                ]);

        return $page;
    }

    /**
     * @param Order $order
     * @param       $packages
     * @param       $addons
     * @param       $page
     *
     * @return mixed
     * @throws NoOneFieldsWereDefined
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function generateOrderItemsTable(Order $order, $packages, $addons, BasePage $page)
    {
        $page->addElement()
            ->box()
            ->addElement('box_tools')
            ->linkButton()->content('Add add-on')
            ->class('btn-success margin ')
            ->js()->openInModalOnClick()->bigModal(route('dashboard::orders.addon.add', $order), 'GET', 'Add add-ons to order', true)

            ->addElement()
            ->linkButton()->content('Add package')
            ->class('btn-success margin')
            ->js()->openInModalOnClick()->bigModal(route('dashboard::orders.package.add', $order), 'GET', 'Add packages to order', true)

            ->parent('box')
            ->footerAvailable(false)

            // Table generation
            ->content(view('dashboard.order-table', compact('packages', 'addons', 'order')));
            return $page;
    }

    /**
     * Get form for edit payment status
     *
     * @param Model $order
     * @param array $payment_statuses
     *
     * @return FormGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     */
    public function getPopupForUpdatePaymentStatus(Model $order, array $payment_statuses)
    {
        $formGenerator = (new FormGenerator())
            ->action(route('dashboard::orders.payment-status.update', $order->id))
            ->ajax(true)
            ->method('PUT')
            ->select('payment_status', array_combine($payment_statuses, $payment_statuses), $order, 'Status', true)
            ->submitButton('Update');

        return $formGenerator;
    }

    /**
     * Get form for edit payment status
     *
     * @param Model $order
     * @param array $statuses
     * @return FormGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     */
    public function getPopupForUpdateStatus(Model $order, array $statuses)
    {
        $formGenerator = (new FormGenerator())
            ->action(route('dashboard::orders.status.update', $order->id))
            ->ajax(true)
            ->method('PUT')
            ->select('status', array_combine($statuses, $statuses), $order, 'Status', true)
            ->submitButton('Update');

        return $formGenerator;
    }

    /**
     * Get form for edit orders customer data
     *
     * @param Model $order
     * @return FormGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     */
    public function getFormForEditCustomer(Model $order)
    {
        $formGenerator = (new FormGenerator())
            ->action(route('dashboard::orders.customer.update', $order->id))
            ->ajax(true)
            ->method('PUT')
            ->textInput('customer_first_name', $order, 'First name', true)
            ->textInput('customer_last_name', $order, 'Lats name', true)
            ->textInput('address', $order, 'Address', true)
            ->textInput('city', $order, 'City', true)
            ->textInput('state', $order, 'State', true)
            ->textInput('postal', $order, 'Postal', true)
            ->textInput('country', $order, 'Country', true)
            ->textarea('message', $order, 'Message')
            ->checkbox('receive_promotions_by_email', $order->receive_promotions_by_email ? true : false, 'Receive promotions')
            ->submitButton('Update');

        return $formGenerator;
    }

    /**
     * Form for edit promo code
     *
     * @param Model $order
     * @param array $codes
     *
     * @return FormGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     */
    public function getFormForEditPromoCode(Model $order, array $codes)
    {
        $formGenerator = (new FormGenerator())
            ->action(route('dashboard::orders.promo-code.update', $order->id))
            ->ajax(true)
            ->method('PUT')
            ->select('promo_code', $codes, $order, 'Choose promo code', true)
            ->submitButton('Update');

        return $formGenerator;
    }

    /**
     * Popup with price form for add addon
     *
     * @param Model $order
     * @param $items
     *
     * @return TableGenerator
     */
    public function getAddonsTableForPopup(Model $order, $items)
    {
        $addonsTable = (new TableGenerator())
            ->showOnly('id', 'type', 'name', 'status')
            ->setConfig([
                'status' => function (Product $product) use($order) {
                    $form =  (new FormGenerator())
                        ->action(route('dashboard::orders.addon.save', [$order, $product->id]))
                        ->method('POST')
                        ->numberInput('price', $product->default_price, 'Price', false, '0.01', 0)
                        ->input('id', $product->id, '', '', false, '', [], 'hidden')
                        ->addSubmitButton(['data-modal-hide' => 'true'], 'Add');

                    return $form;
                }
            ])
            ->items($items)
            ->toolsInModal(true);

        return $addonsTable;
    }

    /**
     * Popup with price form for add package
     *
     * @param Model $order
     * @param $items
     *
     * @return TableGenerator
     */
    public function getPackagesTableForPopup(Model $order, $items)
    {
        $packagesTable = (new TableGenerator())
            ->showOnly('id', 'name', 'status')
            ->setConfig([
                'status' => function (Package $package) use($order) {
                    $form =  (new FormGenerator())
                        ->action(route('dashboard::orders.package.save', [$order, $package->id]))
                        ->numberInput('price', $package->price, 'Price', false, '0.01', 0)
                        ->input('id', $package->id, '', '', false, '', [], 'hidden')
                        ->method('POST')
                        ->addSubmitButton(['data-modal-hide' => 'true'], 'Add');
                    return $form;
                }
            ])
            ->items($items)
            ->toolsInModal(true);

        return $packagesTable;
    }

    /**
     * Get Form for edit quantity
     *
     * @param Model $order_item
     * @param Model $order
     *
     * @return FormGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     */
    public function getFormForEditQuantity(Model $order_item, Model $order)
    {
        $formGenerator = (new FormGenerator())
            ->action(route('dashboard::orders.item.count.update', [$order, $order_item->item_id]))
            ->ajax(true)
            ->method('PUT')
            ->numberInput('quantity', $order_item, 'Quantity', true, 1, 1)
            ->submitButton('Update');

        return $formGenerator;
    }

    /**
     * Get Form for edit product size
     *
     * @param Model $order_item
     * @param Model $order
     * @param array $sizes
     *
     * @return FormGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     */
    public function getFormForEditSize(Model $order_item, Model $order, array $sizes)
    {
        $formGenerator = (new FormGenerator())
            ->action(route('dashboard::orders.item.size.update', [$order, $order_item]))
            ->ajax(true)
            ->method('PUT')
            ->select('size', $sizes, $order_item->size_combination_id, 'Choose size', true)
            ->submitButton('Update');

        return $formGenerator;
    }

    /**
     * Get Form for edit product image
     *
     * @param Order     $order
     * @param OrderItem $orderItem
     * @param           $items
     *
     * @return TableGenerator
     */
    public function getImagesTableForPopup(Order $order, OrderItem $orderItem, $items)
    {
        $addonsTable = (new TableGenerator())
            ->showOnly('image', 'status')
            ->setConfig([
                'image' => function ($item) {
                    $url =  $item['image'];
                    return "<img src='$url' width='100'>";
                },
                'status' => function ($item) use ($order, $orderItem) {
                    $form =  (new FormGenerator())
                        ->action(route('dashboard::orders.item.image.update', [$order, $orderItem]))
                        ->method('PUT')
                        ->input('photo_id',  $item['id'], '', '', false, '', [], 'hidden')
                        ->addSubmitButton(['data-modal-hide' => 'true'], 'Add');

                    return $form;
                }
            ])
            ->items($items)
            ->toolsInModal(true);

        return $addonsTable;
    }

    /**
     * Prepare orders tabs page
     *
     * @param $allOrders
     * @param $seasons
     * @param Dashboard $dashboard
     * @param Request $request
     * @return Dashboard
     * @throws NoOneFieldsWereDefined
     */
    public function prepareTabsPage($allOrders, $seasons, Dashboard $dashboard, Request $request)
    {
        $tabs = $dashboard->page()->setPageTitle('Orders lists')
            ->addElement()->tabs();

        $tabFirst = $tabs->addElement('tabs')
            ->tab()->title('All orders')->active(true)
            ->content($allOrders->render());

        $tabSecond = $tabs->addElement('tabs')
            ->tab()->title('Filtered by season')
            ->content($seasons->render());

        if ($request->ajax() && $request->has('seasons_tab_filter')) {
            return  $tabSecond;
        }
        if ($request->ajax() && !$request->has('seasons_tab_filter')){
            return  $tabFirst;
        }

        return $dashboard;
    }
}
