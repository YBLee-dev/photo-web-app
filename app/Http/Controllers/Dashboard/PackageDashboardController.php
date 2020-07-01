<?php

namespace App\Http\Controllers\Dashboard;

use App\Ecommerce\Packages\PackageDashboardPresenter;
use App\Ecommerce\Packages\PackageRepo;
use App\Ecommerce\Products\ProductDashboardPresenter;
use App\Ecommerce\Products\ProductRepo;
use App\Ecommerce\Products\ProductTypesEnum;
use App\Http\Controllers\Controller;
use App\Services\DashboardImagesPrepare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Webmagic\Core\Controllers\AjaxRedirectTrait;
use Webmagic\Dashboard\Dashboard;

class PackageDashboardController extends Controller
{
    use AjaxRedirectTrait;

    /**
     * Get list of packages with pagination and sorting
     *
     * @param \App\Ecommerce\Packages\PackageRepo               $packageRepo
     * @param \Webmagic\Dashboard\Dashboard                     $dashboard
     * @param \Illuminate\Http\Request                          $request
     * @param \App\Ecommerce\Packages\PackageDashboardPresenter $dashboardPresenter
     *
     * @return \Webmagic\Dashboard\Dashboard
     * @throws \Exception
     */
    public function index(
        PackageRepo $packageRepo,
        Dashboard $dashboard,
        Request $request,
        PackageDashboardPresenter $dashboardPresenter
    ) {
        $sort = $request->get('sort', 'asc');
        $sortBy = $request->get('sortBy', 'id');

        $packages = $packageRepo->getAllWithSorting(
            $request->get('per_page', 10),
            $request->get('page', 1),
            $sort,
            $sortBy
        );

        return $dashboardPresenter->getTablePage($packages, $dashboard, $request, $sort, $sortBy);
    }

    /**
     * Show creating form
     *
     * @return \Webmagic\Dashboard\Components\FormPageGenerator
     * @throws \Exception
     */
    public function create(PackageDashboardPresenter $dashboardPresenter)
    {
        return $dashboardPresenter->getCreateForm();
    }

    /**
     * Show form for editing package
     *
     * @param int                                               $package_id
     * @param \App\Ecommerce\Packages\PackageRepo               $packageRepo
     * @param \App\Ecommerce\Packages\PackageDashboardPresenter $dashboardPresenter
     *
     * @return \Webmagic\Dashboard\Components\FormPageGenerator
     * @throws \Exception
     */
    public function edit(int $package_id, PackageRepo $packageRepo, PackageDashboardPresenter $dashboardPresenter)
    {
        if (! $package = $packageRepo->getByID($package_id)) {
            abort(404, 'Package not found');
        };

       return $dashboardPresenter->getEditForm($package);
    }

    /**
     * Create package
     *
     * @param \Illuminate\Http\Request             $request
     * @param \App\Ecommerce\Packages\PackageRepo  $packageRepo
     * @param \App\Services\DashboardImagesPrepare $imagesPrepare
     *
     * @throws \Exception
     */
    public function store(Request $request, PackageRepo $packageRepo, DashboardImagesPrepare $imagesPrepare)
    {
        $request->validate([
            'image_file' => 'image|max:10000'
        ]);

        $path = config('webmagic.dashboard.image_config.packages_img_path');
        $data = $imagesPrepare->saveImagesInDirectory($request->all(), ['image'], $path);

        if (! $packageRepo->create($data)) {
            abort(500, 'Error on package creating');
        }
    }

    /**
     * Update package info
     *
     * @param $package_id
     * @param \Illuminate\Http\Request $request
     * @param \App\Ecommerce\Packages\PackageRepo $packageRepo
     * @param \App\Services\DashboardImagesPrepare $imagesPrepare
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function update($package_id, Request $request, PackageRepo $packageRepo, DashboardImagesPrepare $imagesPrepare)
    {
        $request->validate([
            'image' => 'image|max:10000'
        ]);

        if (! $packageRepo->getByID($package_id)) {
            abort(404, 'Package not found');
        }

        $path = config('webmagic.dashboard.image_config.packages_img_path');
        $data = $imagesPrepare->saveImagesInDirectory($request->all(), ['image'], $path);

        $data['taxable'] = $request->get('taxable', 0);
        $data['available_after_deadline'] = $request->get('available_after_deadline', 0);

        if (! $packageRepo->update($package_id, $data)) {
            abort(500, 'Error on package updating');
        }
    }

    /**
     * Destroy package with products
     *
     * @param                                    $package_id
     * @param \App\Ecommerce\Packages\PackageRepo $packageRepo
     *
     * @throws \Webmagic\Core\Entity\Exceptions\EntityNotExtendsModelException
     * @throws \Webmagic\Core\Entity\Exceptions\ModelNotDefinedException
     */
    public function destroy($package_id, PackageRepo $packageRepo)
    {
        if (! $package = $packageRepo->getByID($package_id)) {
            abort(404, 'Package not found');
        };

        $path = $package->present()->fullImagePath;

        if (file_exists($path)){
            File::delete($path);
        }

        if (! $packageRepo->destroy($package_id)) {
            abort(500, 'Error on package destroying');
        }
    }

    /**
     * Get show page of package with associated products list
     *
     * @param int                                               $package_id
     * @param \App\Ecommerce\Packages\PackageRepo               $packageRepo
     * @param \Webmagic\Dashboard\Dashboard                     $dashboard
     * @param \Illuminate\Http\Request                          $request
     * @param \App\Ecommerce\Products\ProductRepo               $productRepo
     * @param \App\Ecommerce\Packages\PackageDashboardPresenter $dashboardPresenter
     * @param \App\Ecommerce\Products\ProductDashboardPresenter $productDashboardPresenter
     *
     * @return \Webmagic\Dashboard\Dashboard
     * @throws \Exception
     */
    public function show(
        int $package_id,
        PackageRepo $packageRepo,
        Dashboard $dashboard,
        Request $request,
        ProductRepo $productRepo,
        PackageDashboardPresenter $dashboardPresenter,
        ProductDashboardPresenter $productDashboardPresenter
    ) {
        if (! $package = $packageRepo->getByID($package_id)) {
            abort(404, 'Package not found');
        };

        $dashboard = $dashboardPresenter->getDescriptionList($package, $dashboard);

        $sort = $request->get('sort', 'asc');
        $sortBy = $request->get('sortBy', 'id');

        $products = $productRepo->getByPackageIdWithFilter(
            $package_id,
            $request->get('per_page', 10),
            $request->get('page', 1),
            $sort,
            $sortBy);

        return $productDashboardPresenter->getTablePageForPackage(
            $products,
            $dashboard,
            $package_id,
            $sort,
            $sortBy
        );
    }

    /**
     * Return popup for adding products
     * with filtering by type and name
     *
     * @param int                                               $package_id
     * @param \Webmagic\Dashboard\Dashboard                     $dashboard
     * @param \App\Ecommerce\Products\ProductRepo               $productRepo
     * @param \Illuminate\Http\Request                          $request
     * @param \App\Ecommerce\Products\ProductDashboardPresenter $dashboardPresenter
     *
     * @return \Webmagic\Dashboard\Components\TableGenerator
     * @throws \Exception
     */
    public function getPopupForAddingProducts(
        int $package_id,
        ProductRepo $productRepo,
        Request $request,
        ProductDashboardPresenter $dashboardPresenter
    ) {
        if ($request->get('type') || $request->get('name')) {
            $products = $productRepo->getByFilter($request->get('type'), $request->get('name'));
        } else {
            $products = $productRepo->getAll();
        }
        $types = ProductTypesEnum::values();

        return $dashboardPresenter->getPopupForAdding(
            $products,
            $package_id,
            $request,
            $types
        );
    }

    /**
     * Add product to package
     * with replacing submit for adding to accepted sign
     *
     * @param $package_id
     * @param $product_id
     * @param \App\Ecommerce\Packages\PackageRepo $packageRepo
     * @param \App\Ecommerce\Products\ProductRepo $productRepo
     *
     * @return string
     * @throws \Exception
     */
    public function addProduct($package_id, $product_id, PackageRepo $packageRepo, ProductRepo $productRepo)
    {
        if (! $package = $packageRepo->getByID($package_id)) {
            abort(404, 'Package not found');
        };
        if (! $product = $productRepo->getByID($product_id)) {
            abort(404, 'Product not found');
        };

        $package->products()->attach($product->id);

        $str = "";
        foreach ($product->sizes as $size){
            $str .= $size->name. "<br>";
        }

        return "<tr class=' js_item_$product->id' data-original-title='' title=''>
                    <td data-original-title='' title=''>$product->id</td>
                    <td data-original-title='' title=''>$product->type</td>
                    <td data-original-title='' title=''>$product->name</td>
                    <td data-original-title='' title=''>$str<br data-original-title='' title=''></td>
                    <td data-original-title='' title=''><i class='fa fa-check-circle text-success' data-original-title='' title=''></i>Added</td>
            </tr>";
    }

    /**
     * Remove product from package
     *
     * @param $package_id
     * @param $product_id
     * @param \App\Ecommerce\Packages\PackageRepo $packageRepo
     * @param \App\Ecommerce\Products\ProductRepo $productRepo
     *
     * @throws \Exception
     */
    public function removeProduct($package_id, $product_id, PackageRepo $packageRepo, ProductRepo $productRepo)
    {
        if (! $package = $packageRepo->getByID($package_id)) {
            abort(404, 'Package not found');
        };
        if (! $product = $productRepo->getByID($product_id)) {
            abort(404, 'Product not found');
        };

        $package->products()->detach($product->id);
    }

    /**
     * Create copy of package with products relations
     * and redirect to show page
     *
     * @param                                    $package_id
     * @param \App\Ecommerce\Packages\PackageRepo $packageRepo
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function copy($package_id, PackageRepo $packageRepo)
    {
        if (! $package = $packageRepo->getByID($package_id)) {
            abort(404, 'Package not found');
        };

        $new_package = $packageRepo->createCopyWithProductsRelations($package);

        return $this->redirect(route('dashboard::packages.show', $new_package->id));
    }

    /**
     * Get products by package with sorting and pagination
     *
     * @param int                                               $package_id
     * @param \Illuminate\Http\Request                          $request
     * @param \App\Ecommerce\Products\ProductRepo               $productRepo
     * @param \App\Ecommerce\Products\ProductDashboardPresenter $dashboardPresenter
     *
     * @return \Webmagic\Dashboard\Components\TableGenerator
     * @throws \Exception
     */
    public function getProductsTable(
        int $package_id,
        Request $request,
        ProductRepo $productRepo,
        ProductDashboardPresenter $dashboardPresenter
    ) {
        $sort = $request->get('sort', 'asc');
        $sortBy = $request->get('sortBy', 'product_id');

        $products = $productRepo->getByPackageIdWithFilter(
            $package_id,
            $request->get('per_page', 10),
            $request->get('page', 1),
            $sort,
            $sortBy
        );

        return $dashboardPresenter->getTable($products, $package_id, $sort, $sortBy);
    }
}
