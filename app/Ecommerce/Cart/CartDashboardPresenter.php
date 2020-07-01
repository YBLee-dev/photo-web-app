<?php

namespace App\Ecommerce\Cart;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Webmagic\Dashboard\Components\TablePageGenerator;
use Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable;
use Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined;
use Webmagic\Dashboard\Dashboard;
use Webmagic\Dashboard\Elements\Links\Link;
use Webmagic\Dashboard\Elements\Links\LinkButton;
use Webmagic\Dashboard\Pages\BasePage;

class CartDashboardPresenter
{

    /**
     * Table Page
     *
     * @param                               $carts
     * @param array                         $galleries_for_select
     * @param array                         $subgalleries_for_select
     * @param array                         $price_lists_for_select
     * @param Dashboard                     $dashboard
     * @param Request                       $request
     * @param                               $sort
     * @param                               $sortBy
     *
     * @return Dashboard
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     */
    public function getTablePage(
        $carts,
        array $galleries_for_select,
        array $subgalleries_for_select,
        array $price_lists_for_select,
        Dashboard $dashboard,
        Request $request,
        $sort,
        $sortBy
    ) {
        $sorting = [$sortBy => $sort];
        $tablePageGenerator = (new TablePageGenerator($dashboard->page()))
            ->title('Carts list')
            ->tableTitles('ID')
            ->addTitleWithSorting('Last update', 'updated_at', data_get($sorting, 'updated_at', ''), true, route('dashboard::carts.index', $request->all()))
            ->addTitleWithSorting('Gallery name', 'gallery_name', data_get($sorting, 'gallery_name', ''), true, route('dashboard::carts.index', $request->all()))
            ->addTableTitle('Sub-gallery name')
            ->addTableTitle('Price list')
            ->addTableTitle('Items count')
            ->addTableTitle('Total')
            ->addTableTitle('Abandoned')
            ->addTableTitle('Free gift')
            ->items($carts)
            ->withPagination($carts, route('dashboard::carts.index', $request->all()) )
            ->showOnly('id', 'updated_at', 'gallery_name', 'sub_gallery', 'price_list', 'items_count', 'total', 'abandoned', 'free_gift')
            ->setConfig([
                'gallery_name' => function (Cart $cart) {
                    return (new Link())->content($cart->gallery->present()->name)->link(route('dashboard::gallery.show', $cart->gallery_id));
                },
                'subgallery' => function (Cart $cart) {
                    return (new Link())->content($cart->subgallery->name)->link(route('dashboard::gallery.subgallery.show', $cart->subgallery_id));
                },
                'price_list' => function (Cart $cart) {
                    return (new Link())->content($cart->priceList->name)->link(route('dashboard::price-lists.show', $cart->price_list_id));
                },
                'abandoned' => function (Cart $cart) {
                    if($cart->abandoned){
                        return '<i class="fa fa-check-circle text-success"></i>';
                    }
                },
                'free_gift' => function (Cart $cart) {
                    if($cart->free_gift){
                        return '<i class="fa fa-check-circle text-success"></i>';
                    }
                },
                'total' => function (Cart $cart) {
                   return '$ '. $cart->total;
                },
            ])
            ->setShowLinkClosure(function (Cart $cart) {
                return route('dashboard::carts.show', $cart);
            })
            ->addElementsToToolsCollection(function (Cart $cart) {
                $btn = (new LinkButton())
                    ->icon('fa-trash')
                    ->dataAttr('item', ".js_item_$cart->id")
                    ->class('text-red')
                    ->dataAttr('request', route('dashboard::carts.destroy', $cart))
                    ->dataAttr('method', 'DELETE');

                if(!$cart->abandoned){
                    $btn->addClass('disabled');
                    $btn = '<span title="You can delete only abandoned cart">'.$btn->render().'</span>';
                } else {
                    $btn->addClass('js_delete')->dataAttr('title', 'Delete');;
                }
                return $btn;
            });

        $tablePageGenerator->addFiltering()
            ->action(route('dashboard::carts.index'))
            ->method('POST')
            ->selectJS('galleries[]', $galleries_for_select, ['galleries[]' => $request->get('galleries')], 'Gallery name', false, true)
            ->selectJS('subgalleries[]', $subgalleries_for_select, ['subgalleries[]' => $request->get('subgalleries')], 'Sub-gallery name', false, true)
            ->selectJS('price_lists[]', $price_lists_for_select, ['price_lists[]' => $request->get('price_lists')], 'Price list', false, true)
            ->datePickerJS('date_from',  $request->get('date_from'), 'From')
            ->datePickerJs('date_to', $request->get('date_to'), 'To')
            ->checkbox('abandoned', $request->get('abandoned', false), 'Abandoned')
            ->addClearButton(['class' => 'btn btn-default margin'], 'Clear')
            ->addSubmitButton(['class' => 'btn btn-info margin'], 'Filter');


        if ($request->ajax()) {
            return $dashboard->page()->content()->toArray()['box_body'];
        }

        return $dashboard;
    }

    /**
     * Description Page List
     *
     * @param Model     $cart
     * @param Dashboard $dashboard
     *
     * @return BasePage
     * @throws NoOneFieldsWereDefined
     */
    public function getDescriptionList(Model $cart, Dashboard $dashboard)
    {
        $galleryLink  = (new Link())->content($cart->gallery->present()->name)
            ->link(route('dashboard::gallery.show', $cart->gallery_id));
        $subgalleryLink  = (new Link())->content($cart->subgallery->name)
            ->link(route('dashboard::gallery.subgallery.show', $cart->subgallery_id));
        $priceListLink = (new Link())->content($cart->priceList->name)
            ->link(route('dashboard::price-lists.show', $cart->price_list_id));

        $page = $dashboard->page();
        $page->setPageTitle("Cart details")
            ->element()->box()
            ->addElement()->descriptionList(
                ['data' => [
                    'ID:' => $cart->id,
                    'Abandoned:' => $cart->abandoned ? 'Yes' : 'No',
                    'Last update:' => $cart->updated_at,
                    'Gallery:' => $galleryLink,
                    'Sub-gallery:' => $subgalleryLink,
                    'Price list:' => $priceListLink,
                    'Items count:' => $cart->items_count,
                    'Sum:' => '$' . $cart->total,
                ],
                ])->isHorizontal(true);

        return $page;
    }

    /**
     * @param Model cart
     * @param $packages
     * @param $addons
     * @param $dashboard
     *
     * @return mixed
     */
    public function generateCartItemsTable(Model $cart, $packages, $addons, $dashboard)
    {
        $dashboard
            ->addElement()
            ->box(view('dashboard.carts-table', compact('packages', 'addons')))
            ->addElement()->descriptionList(
                ['data' => [
                    'Total:' => '$' . $cart->total,
                ],
                ])->isHorizontal(true)
        ;
        return $dashboard;
    }
}
