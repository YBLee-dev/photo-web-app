<?php

namespace App\Http\Controllers\Dashboard;

use App\Ecommerce\PriceLists\PriceListRepo;
use App\Ecommerce\Products\Product;
use App\Ecommerce\Products\ProductRepo;
use App\Ecommerce\Products\ProductTypesEnum;
use Illuminate\Http\Request;
use Webmagic\Core\Controllers\AjaxRedirectTrait;
use Webmagic\Dashboard\Components\FormGenerator;
use Webmagic\Dashboard\Components\TableGenerator;
use Webmagic\Dashboard\Elements\Links\Link;

class PriceListProductsDashboardController extends PriceListPackagesDashboardController
{
    use AjaxRedirectTrait;

    public function getPopupForAddingAddons($price_list_id, ProductRepo $productRepo, Request $request)
    {
        if($request->get('type') || $request->get('name')){
            $products = $productRepo->getByFilter($request->get('type'), $request->get('name'));
        } else {
            $products = $productRepo->getAll();
        }
        $types = ProductTypesEnum::values();

        $addonsTable = $this->getAddonsTableForPopup($price_list_id,$products);

        $addonsTable->addFiltering()
            ->action(route('dashboard::price-lists.addon.add', $price_list_id))
            ->selectJS('type[]', array_combine($types, $types), ['type[]' => $request->get('type')], 'Type', false, true)
            ->textInput('name', $request->get('name'), 'Name');

        $addonsTable->getFilter()->addElement()->button('Filter')->type('submit')->dataAttr('modal-hide', false);
        $addonsTable->getFilter()->content()->getItem(0)->content()->attr('style', 'width:200px;');

        return $addonsTable;
    }

    public function getAddonsTable($price_list_id, ProductRepo $productRepo, Request $request)
    {
        $products = $productRepo->getByPriceListIdWithFilter(
            $price_list_id,
            $request->get('per_page', 10),
            $request->get('page', 1)
        );

        $addonsTable = (new TableGenerator())
            ->tableTitles('ID', 'Type', 'Name (reference)', 'Price', 'Sizes')
            ->showOnly('id', 'type', 'name', 'default_price', 'size')
            ->setConfig([
                'name' => function (Product $product) {
                    $content = $product->name . ($product->reference ? " ($product->reference)" : '');
                    return (new Link())->content($content)->link( route('dashboard::products.edit', $product));
                },
                'default_price' => function (Product $product) {
                    return '$ '.$product->price;
                },
                'size' => function (Product $product) {
                    $str = "";
                    foreach ($product->sizes as $size){
                        $str .= $size->name. "<br>";
                    }

                    return $str;
                },
            ])
            ->items($products)
            ->withPagination($products, route('dashboard::price-lists.addon.list',[$price_list_id]) )
            ->setEditLinkClosure(function ($product) use ($price_list_id) {
                return route('dashboard::price-lists.addon.edit', [$price_list_id, $product->id]);
            })->setDestroyLinkClosure(function (Product $product) use ($price_list_id) {
                return route('dashboard::price-lists.addon.remove', [$price_list_id, $product->id]);
            })
            ->toolsInModal(true);

        return $addonsTable;
    }

    protected function getAddonsTableForPopup(int $price_list_id, $items)
    {
        $addonsTable = (new TableGenerator())
            ->showOnly('id', 'type', 'name', 'size', 'status')
            ->setConfig([
                'size' => function (Product $product) {
                    $str = "";
                    foreach ($product->sizes as $size){
                        $str .= $size->name. "<br>";
                    }
                    return $str;
                },
                'status' => function (Product $product) use($price_list_id) {
                    if($product->priceLists()->allRelatedIds()->contains($price_list_id)){
                        return '<i class="fa fa-check-circle text-success"></i> <span class="text-success">Added</span>';
                    } else {
                        $form =  (new FormGenerator())
                            ->action(route('dashboard::price-lists.addon.save', [$price_list_id, $product->id]))
                            ->numberInput("price_$product->id", $product->default_price, 'Price', true, '0.01', 0, 999999, ['style' => 'width:90px'])
                            ->method('POST')
                            ->ajax(true)
                            ->replaceBlockClass(".js_item_$product->id")
                            ->addSubmitButton(['data-modal-hide' => 'false'], 'Add', 'btn btn-default');

                        return $form->getForm()->makeInline();
                    }
                }
            ])
            ->items($items)
            ->toolsInModal(true);

        return $addonsTable;
    }

    public function addAddon($price_list_id, $productId, Request $request, PriceListRepo $priceListRepo, ProductRepo $productRepo)
    {
        if (! $priceList = $priceListRepo->getByID($price_list_id)) {
            abort(404, 'Price list not found');
        };
        if (! $product = $productRepo->getByID($productId)) {
            abort(404, 'Product not found');
        };
        $priceList->products()->attach($product->id, ['price' => $request->get("price_$productId", $product->default_price)]);

        $str = "";
        foreach ($product->sizes as $size){
            $str .= $size->name. "<br>";
        }

        return "<tr class=' js_item_$product->id' data-original-title='' title=''>
                    <td data-original-title='' title='' style='vertical-align: middle;'>$product->id</td>
                    <td data-original-title='' title='' style='vertical-align: middle;'>$product->type</td>
                    <td data-original-title='' title='' style='vertical-align: middle;'>$product->name</td>
                    <td data-original-title='' title='' style='vertical-align: middle;'>$str</td>
                    <td data-original-title='' title='' style='vertical-align: middle;'><i class='fa fa-check-circle text-success'></i> <span class='text-success'>Added</span></td>
            </tr>";
    }

    public function removeAddon($price_list_id, $productId, PriceListRepo $priceListRepo, ProductRepo $productRepo)
    {
        if (! $priceList = $priceListRepo->getByID($price_list_id)) {
            abort(404, 'Price list not found');
        };
        if (! $product = $productRepo->getByID($productId)) {
            abort(404, 'Product not found');
        };
        $priceList->products()->detach($product->id);
    }

    public function getPopupFotEditAddon($price_list_id, $productId, ProductRepo $productRepo)
    {
        if (! $product = $productRepo->getByID($productId)) {
            abort(404, 'Price list not found');
        };

        $priceList = $productRepo->getRelatedPriceListById($price_list_id, $product);

        $formPageGenerator = (new FormGenerator())
            ->action(route('dashboard::price-lists.addon.update', [$price_list_id, $productId]))
            ->ajax(true)
            ->method('PUT')
            ->numberInput('price', $priceList->pivot->price, 'Price', true, '0.01', 0)
            ->submitButton('Update');

        return $formPageGenerator;
    }

    public function updateAddon($price_list_id, $productId, Request $request, PriceListRepo $priceListRepo, ProductRepo $productRepo)
    {
        $request->validate([
            'price' => 'required',
        ]);
        if (! $priceList = $priceListRepo->getByID($price_list_id)) {
            abort(404, 'Price list not found');
        };
        if (! $product = $productRepo->getByID($productId)) {
            abort(404, 'Price list not found');
        };
        $priceList->products()->detach($product->id);
        $priceList->products()->attach($product->id, ['price' => $request->get('price')]);

        return $this->redirect(route('dashboard::price-lists.show', $price_list_id));
    }
}

