<?php

namespace App\Ecommerce\Products;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Webmagic\Dashboard\Components\FormPageGenerator;
use Webmagic\Dashboard\Components\TableGenerator;
use Webmagic\Dashboard\Components\TablePageGenerator;
use Webmagic\Dashboard\Dashboard;
use Webmagic\Dashboard\Elements\Links\Link;
use Webmagic\Dashboard\Elements\Links\LinkButton;

class ProductDashboardPresenter
{
    /**
     * Table Page with sorting
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $products
     * @param \Webmagic\Dashboard\Dashboard $dashboard
     * @param $package_id
     * @param $sort
     * @param $sortBy
     * @return \Webmagic\Dashboard\Dashboard
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getTablePageForPackage(LengthAwarePaginator $products, Dashboard $dashboard, $package_id, $sort, $sortBy)
    {
        $productsTable = $this->getTable($products, $package_id, $sort, $sortBy);

        $addProductBtn = (new LinkButton())
            ->icon('fa fa-plus')
            ->content('Add')
            ->class('pull-right btn btn-flat btn-default')
            ->js()->openInModalOnClick()
            ->regular(route('dashboard::packages.add-product', $package_id), 'GET', 'Add products to the package')
            ->dataAttr('reload-after-close-modal', 'true')
            ->dataAttr('modal-size', 'modal-lg')
            ->dataAttr('data-modal-hide', 'false');

        $dashboard->page()->addElement()
            ->h4Title('Associated products')
            ->parent()->addElement()
            ->box($productsTable)
            ->boxHeaderContent($addProductBtn);

        return $dashboard;
    }

    /**
     * Table with sorting and pagination
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $products
     * @param $package_id
     * @param $sort
     * @param $sortBy
     * @return \Webmagic\Dashboard\Components\TableGenerator
     */
    public function getTable(LengthAwarePaginator $products, $package_id, $sort, $sortBy)
    {
        $sorting = [$sortBy => $sort];

        $productsTable = (new TableGenerator())
            ->tableTitles(['ID'])
            ->addTitleWithSorting('Type', 'type', data_get($sorting, 'type', ''), true, route('dashboard::packages.products-post',[$package_id]), 'POST')
            ->addTitleWithSorting('Name', 'name', data_get($sorting, 'name', ''), true, route('dashboard::packages.products-post',[$package_id]), 'POST')
            ->addTitleWithSorting('Default price', 'default_price', data_get($sorting, 'default_price', ''), true, route('dashboard::packages.products-post',[$package_id]), 'POST')
            ->addTableTitle('Sizes')
            ->showOnly(['id', 'type', 'name', 'default_price', 'size'])

            ->setConfig([
                'name' => function (Product $product) {
                    return (new Link())->content($product->name)->link(route('dashboard::products.edit', $product->id));
                },
                'default_price' => function (Product $product) {
                    return '$ '.$product->default_price;
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
            ->withPagination($products, route('dashboard::packages.products',[
                $package_id,
                'sort' => $sort,
                'sortBy' => $sortBy
            ]))
            ->setDestroyLinkClosure(function (Product $product) use ($package_id) {
                return route('dashboard::packages.remove-product', [$package_id, $product->id]);
            })
            ->toolsInModal(true);

        return $productsTable;
    }

    /**
     * Popup with table and filters for adding
     *
     * @param $products
     * @param int $package_id
     * @param \Illuminate\Http\Request $request
     * @param array $types
     * @return \Webmagic\Dashboard\Components\TableGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getPopupForAdding($products, int $package_id, Request $request, array $types)
    {
        $productsTable = (new TableGenerator())
            ->showOnly('id', 'type', 'name', 'size', 'status')
            ->setConfig([
                'size' => function (Product $product) {
                    $str = "";
                    foreach ($product->sizes as $size){
                        $str .= $size->name. "<br>";
                    }
                    return $str;
                },
                'status' => function (Product $product) use ($package_id) {
                    if($product->packages()->allRelatedIds()->contains($package_id)){
                        return '<i class="fa fa-check-circle text-success"></i> Added';
                    } else {
                        $createBtn = (new LinkButton())
                            ->content('Add')
                            ->addClass(' js_ajax-by-click-btn')
                            ->dataAttr('action', route('dashboard::packages.save-product', [$package_id, $product->id]))
                            ->dataAttr('method', 'POST')
                            ->dataAttr('replace-blk', ".js_item_$product->id")
                            ->dataAttr('modal-hide', 'false');

                        return $createBtn;
                    }
                }
            ])
            ->items($products)
            ->toolsInModal(true);

        $productsTable->addFiltering()
            ->action(route('dashboard::packages.add-product', $package_id))
            ->selectJS('type[]', array_combine($types, $types), ['type[]' => $request->get('type')], 'Type', false, true)
            ->textInput('name', $request->get('name'), 'Name');

        $productsTable->getFilter()->addElement()->button('Filter')->type('submit')->dataAttr('modal-hide', false);
        $productsTable->getFilter()->content()->getItem(0)->content()->attr('style', 'width:200px;');
        $productsTable->getFilter()->content()->getItem(0)->content()->attr('style', 'width:200px;');

        return $productsTable;
    }

    /**
     * Table Page with pagination and sorting
     *
     * @param \Illuminate\Pagination\AbstractPaginator $products
     * @param \Webmagic\Dashboard\Dashboard $dashboard
     * @param \Illuminate\Http\Request $request
     * @param array $types
     * @param $sort
     * @param $sortBy
     * @return \Webmagic\Dashboard\Dashboard
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getTablePage(AbstractPaginator $products, Dashboard $dashboard, Request $request, array $types, $sort, $sortBy)
    {
        $sorting = [$sortBy => $sort];

        $tablePageGenerator = (new TablePageGenerator($dashboard->page()))
            ->title('Products')
            ->showOnly('id', 'type', 'name', 'default_price', 'size')
            ->setConfig([
                'name' => function (Product $product) {
                    $reference = $product->reference ? '('.$product->reference.')' : '';
                    $link = (new Link())->content($product->name)
                        ->link( route('dashboard::products.edit', $product));
                    return $link.$reference;
                },
                'default_price' => function (Product $product) {
                    return '$ '.$product->default_price;
                },
                'size' => function (Product $product) {
                    $str = "";
                    foreach ($product->sizes as $size){
                        $str .= $size->name . "<br>";
                    }
                    return $str;
                },
            ])
            ->tableTitles(['ID'])
            ->addTitleWithSorting('Type', 'type', data_get($sorting, 'type', ''), true, route('dashboard::products.index', $request->all()))
            ->addTitleWithSorting('Name (reference)', 'name', data_get($sorting, 'name', ''), true, route('dashboard::products.index', $request->all()))
            ->addTableTitle('Default price')
            ->addTableTitle('Sizes')
            ->items($products)
            ->withPagination($products, route('dashboard::products.index', $request->all()) )
            ->setEditLinkClosure(function (Product $product) {
                return route('dashboard::products.edit', $product);
            })
            ->addElementsToToolsCollection(function (Product $product) {
                $btn = (new LinkButton())
                    ->icon('fa-trash')
                    ->dataAttr('item', ".js_item_$product->id")
                    ->class('text-red')
                    ->dataAttr('request', route('dashboard::products.destroy', $product))
                    ->dataAttr('method', 'DELETE');

                if(count($product->cart_items)){
                    $btn->addClass('disabled');
                    $btn = '<span title="This product has connected carts">'.$btn->render().'</span>';
                }  elseif(count($product->order_items)){
                    $btn->addClass('disabled');
                    $btn = '<span title="This product has connected orders">'.$btn->render().'</span>';
                } else {
                    $btn->addClass('js_delete')->dataAttr('title', 'Delete');;
                }
                return $btn;
            })
            ->addElementsToToolsCollection(function (Product $product) {
                $createBtn = (new LinkButton())
                    ->icon('fa-clone')
                    ->class('btn-light')
                    ->link(route('dashboard::products.copy', $product))
                    ->dataAttr('title', 'Create copy');
                return $createBtn;
            });

        $tablePageGenerator->addFiltering()
            ->action(route('dashboard::products.index'))
            ->select('type', $types, $request->get('type'), 'Type')
            ->submitButton('Filter');

        /** @var \Webmagic\Dashboard\Elements\Boxes\Box $box */
        $tablePageGenerator->getBox()
            ->addToolsLinkButton(
                route('dashboard::products.create.by-type', ProductTypesEnum::PRINTABLE),
                'Add Printable',
                '',
                'btn btn-flat btn-info margin'
            )
            ->headerAvailable(true);
        $tablePageGenerator->getBox()
            ->addToolsLinkButton(
                route('dashboard::products.create.by-type', ProductTypesEnum::RETOUCH),
                'Add Retouch',
                '',
                'btn btn-flat btn-primary margin'
            );
        $tablePageGenerator->getBox()
            ->addToolsLinkButton(
                route('dashboard::products.create.by-type', ProductTypesEnum::DIGITAL),
                'Add Digital',
                '',
                'btn btn-flat btn-success margin'
            );
        $tablePageGenerator->getBox()
            ->addToolsLinkButton(
                route('dashboard::products.create.by-type', ProductTypesEnum::DIGITAL_FULL),
                'Add Digital Full',
                '',
                'btn btn-flat btn-success margin'
            );

        $tablePageGenerator->getBox()
            ->addToolsLinkButton(
                route('dashboard::products.create.by-type', ProductTypesEnum::SINGLE_DIGITAL),
                'Add Digital Single',
                '',
                'btn btn-flat btn-success margin'
            );

        if ($request->ajax()) {
            return $dashboard->page()->content()->toArray()['box_body'];
        }

        return $dashboard;
    }

    /**
     * Create Form
     *
     * @param $type
     * @return \Webmagic\Dashboard\Components\FormPageGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    protected function getBaseCreateForm($type)
    {
        $formPageGenerator = (new FormPageGenerator())
            ->action(route('dashboard::products.store'))
            ->method('POST')
            ->textInput('name', false, 'Name', true)
            ->imageInput('image', '', 'Image')
            ->textInput('reference', false, 'Reference')
            ->numberInput('default_price', 0, 'Default price,  $', false, 0.01, 0)
            ->checkbox('taxable', true, 'Taxable', false, ['disabled' => true])
            ->visualEditor('description', false, 'Description', false);


        $formPageGenerator->title("Create new product with type $type");

        $formPageGenerator->getForm()->content()
            ->addElement()
            ->input([
                'name' => 'type',
                'type' => 'hidden',
                'value' => $type
            ]);

        return $formPageGenerator;
    }

    /**
     * Create form by type
     *
     * @param $type
     * @param array $sizes
     * @return \Webmagic\Dashboard\Components\FormPageGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getCreateForm($type, array $sizes = [])
    {
        $formPageGenerator = $this->getBaseCreateForm($type);

        if(ProductTypesEnum::PRINTABLE()->is($type)) {
            $formPageGenerator->selectJS('size[]', $sizes, null, 'Size Combinations', true, true)
                ->submitButtonTitle('Create');
        }

        return $formPageGenerator;
    }

    /**
     * Edit Form Page
     *
     * @param \Illuminate\Database\Eloquent\Model $product
     * @param array $sizes
     * @param array $selected_sizes
     * @return \Webmagic\Dashboard\Components\FormPageGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getEditForm(Model $product, array $sizes = [], array $selected_sizes = [])
    {
        $formPageGenerator = (new FormPageGenerator())
            ->title('Product editing', $product->type)
            ->action(route('dashboard::products.update', $product->id))
            ->method('PUT')
            ->textInput('name', $product, 'Name', true)
            ->imageInput('image', $product->image ? $product->present()->image() : '', 'Image', '', '', '', '')
            ->textInput('reference', $product, 'Reference')
            ->numberInput('default_price', $product, 'Default price,  $', true, 0.01, 0)
            ->checkbox('taxable', true, 'Taxable', false, ['disabled' => true])
            ->visualEditor('description', $product, 'Description');


        if(count($sizes)){
            $formPageGenerator->selectJS('size[]', $sizes, ['size[]' => $selected_sizes], 'Size Combinations', true, true);
        }

        return $formPageGenerator->submitButtonTitle('Update');
    }

    /**
     * Edit copy Form Page
     *
     * @param \Illuminate\Database\Eloquent\Model $product
     * @param array $sizes
     * @param array $selected_sizes
     * @return \Webmagic\Dashboard\Components\FormPageGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getEditCopyForm(Model $product, array $sizes = [], array $selected_sizes = [])
    {
        $form = $this->getEditForm($product, $sizes, $selected_sizes);
        $form->title('Copied product editing', $product->type);

        return $form;
    }
}
