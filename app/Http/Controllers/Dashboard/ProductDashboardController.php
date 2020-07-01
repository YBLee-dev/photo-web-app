<?php

namespace App\Http\Controllers\Dashboard;

use App\Ecommerce\Products\ProductDashboardPresenter;
use App\Ecommerce\Products\ProductRepo;
use App\Ecommerce\Products\ProductTypesEnum;
use App\Ecommerce\Sizes\SizeCombinationRepo;
use App\Http\Controllers\Controller;
use App\Services\DashboardImagesPrepare;
use Illuminate\Http\Request;
use Webmagic\Dashboard\Dashboard;

class ProductDashboardController extends Controller
{
    /**
     * Show products page with filter
     *
     * @param \App\Ecommerce\Products\ProductRepo               $productRepo
     * @param \Webmagic\Dashboard\Dashboard                     $dashboard
     * @param \Illuminate\Http\Request                          $request
     * @param \App\Ecommerce\Products\ProductDashboardPresenter $dashboardPresenter
     *
     * @return \Webmagic\Dashboard\Dashboard
     * @throws \Exception
     */
    public function index(
        ProductRepo $productRepo,
        Dashboard $dashboard,
        Request $request,
        ProductDashboardPresenter $dashboardPresenter
    ) {
        $validated = $request->validate([
            'sort' => 'in:asc,desc',
            'sortBy' => 'in:type,name'
        ]);

        $sort = data_get($validated, 'sort') ? $validated['sort'] : 'asc';
        $sortBy = data_get($validated, 'sortBy') ? $validated['sortBy'] : 'id';

        $products = $productRepo->getByFilter(
            $request->get('type'),
            null,
            $request->get('per_page', 10),
            $request->get('page', 1),
            $sort,
            $sortBy
        );

        $types = array_combine(ProductTypesEnum::values(), ProductTypesEnum::values());
        array_unshift($types,"All");

        return $dashboardPresenter->getTablePage(
            $products,
            $dashboard,
            $request,
            $types,
            $sort,
            $sortBy
        );
    }

    /**
     * Show form for creating {{type}} product
     *
     * @param $type
     * @param \App\Ecommerce\Products\ProductDashboardPresenter $dashboardPresenter
     *
     * @return \Webmagic\Dashboard\Components\FormPageGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function createByType($type, ProductDashboardPresenter $dashboardPresenter)
    {
        $sizes = [];
        if(ProductTypesEnum::PRINTABLE()->is($type)){
            $sizes = (new SizeCombinationRepo())->getForSelect('name', 'id');
        }

        return $dashboardPresenter->getCreateForm($type, $sizes);
    }

    /**
     * Create product
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Ecommerce\Products\ProductRepo $productRepo
     *
     * @param DashboardImagesPrepare $imagesPrepare
     * @throws \Exception
     */
    public function store(Request $request, ProductRepo $productRepo, DashboardImagesPrepare $imagesPrepare)
    {
        $request->validate([
            'name' => 'required',
            'default_price' => 'min:0',
            'image' => 'nullable|image'
        ]);

        $path = config('webmagic.dashboard.image_config.products_img_path');
        $data = $imagesPrepare->saveImagesInDirectory($request->all(), ['image'], $path);

        $product = $productRepo->create([
            'name' =>$data['name'],
            'type'=> $data['type'],
            'reference'=> $data['reference'],
            'default_price' => $data['default_price'] ?: 0,
            'taxable' => true,
            'description' => $data['description'],
            'image' => $data['image'] ?? null,
        ]);

        if (ProductTypesEnum::PRINTABLE()->is($product->type)) {
            $product->sizes()->sync($data['size']);
        }
    }

    /**
     * Destroy product
     *
     * @param                                    $product_id
     * @param \App\Ecommerce\Products\ProductRepo $productRepo
     *
     * @throws \Webmagic\Core\Entity\Exceptions\EntityNotExtendsModelException
     * @throws \Webmagic\Core\Entity\Exceptions\ModelNotDefinedException
     */
    public function destroy($product_id, ProductRepo $productRepo)
    {
        if (! $product = $productRepo->getByID($product_id)) {
            abort(404, 'Product not found');
        };

        if (! $productRepo->destroy($product->id)) {
            abort(500, 'Error on product destroying');
        }
    }

    /**
     * Show edit form
     *
     * @param                                                  $product_id
     * @param \App\Ecommerce\Products\ProductRepo              $productRepo
     * @param \App\Ecommerce\Products\ProductDashboardPresenter $dashboardPresenter
     * @param \App\Ecommerce\Sizes\SizeCombinationRepo $combinationRepo
     *
     * @return \Webmagic\Dashboard\Components\FormPageGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function edit(
        $product_id,
        ProductRepo $productRepo,
        ProductDashboardPresenter $dashboardPresenter,
        SizeCombinationRepo $combinationRepo
    ) {
        if (! $product = $productRepo->getByID($product_id)) {
            abort(404, 'Product not found');
        };
        if(ProductTypesEnum::PRINTABLE()->is($product->type)) {
            $sizes = $combinationRepo->getForSelect('name', 'id');
            $selected_sizes = $product->sizes->pluck('id')->toArray();
        }

        return $dashboardPresenter->getEditForm($product, $sizes ?? [], $selected_sizes ?? []);
    }

    /**
     * Update product
     *
     * @param                                    $product_id
     * @param \App\Ecommerce\Products\ProductRepo $productRepo
     * @param \Illuminate\Http\Request $request
     *
     * @param DashboardImagesPrepare $imagesPrepare
     * @throws \Exception
     */
    public function update($product_id, ProductRepo $productRepo, Request $request, DashboardImagesPrepare $imagesPrepare)
    {
        if (! $product = $productRepo->getByID($product_id)) {
            abort(404, 'Product not found');
        };

        $path = config('webmagic.dashboard.image_config.products_img_path');
        $data = $imagesPrepare->saveImagesInDirectory($request->all(), ['image'], $path);

        if (! $productRepo->update($product_id, [
            'name' => $data['name'],
            'reference'=> $data['reference'],
            'default_price' => $data['default_price'],
            'taxable' => true,
            'description' => $data['description'],
            'image' => $data['image'] ?? $product->iamge,
        ])) {
            abort(500, 'Error on product updating');
        }

        if (ProductTypesEnum::PRINTABLE()->is($product->type)) {
            $product->sizes()->sync($data['size']);
        }
    }

    /**
     * Create copied product and show edit form
     *
     * @param                                                  $product_id
     * @param \App\Ecommerce\Products\ProductRepo              $productRepo
     * @param \App\Ecommerce\Products\ProductDashboardPresenter $dashboardPresenter
     * @param \App\Ecommerce\Sizes\SizeCombinationRepo $combinationRepo
     *
     * @return \Webmagic\Dashboard\Components\FormPageGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function copy(
        $product_id,
        ProductRepo $productRepo,
        ProductDashboardPresenter $dashboardPresenter,
        SizeCombinationRepo $combinationRepo
    ) {
        if (! $product = $productRepo->getByID($product_id)) {
            abort(404, 'Product not found');
        };

        $new_product = $productRepo->createCopyWithSizesRelations($product);

        if(ProductTypesEnum::PRINTABLE()->is($product->type)) {
            $sizes = $combinationRepo->getForSelect('name', 'id');
            $selected_sizes = $new_product->sizes->pluck('id')->toArray();
        }

        return $dashboardPresenter->getEditCopyForm($product, $sizes ?? [], $selected_sizes ?? []);
    }
}
