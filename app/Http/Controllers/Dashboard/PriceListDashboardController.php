<?php

namespace App\Http\Controllers\Dashboard;

use App\Ecommerce\Packages\PackageRepo;
use App\Ecommerce\PriceLists\PriceListDashboardPresenter;
use App\Ecommerce\PriceLists\PriceListRepo;
use App\Ecommerce\Products\ProductRepo;
use Illuminate\Http\Request;
use Webmagic\Core\Controllers\AjaxRedirectTrait;
use Webmagic\Dashboard\Dashboard;
use Webmagic\Dashboard\Elements\Links\LinkButton;

class PriceListDashboardController extends PriceListProductsDashboardController
{
    use AjaxRedirectTrait;

    /**
     * Get list of price lists with pagination
     *
     * @param \App\Ecommerce\PriceLists\PriceListRepo               $priceListRepo
     * @param \Webmagic\Dashboard\Dashboard                         $dashboard
     * @param \Illuminate\Http\Request                              $request
     * @param \App\Ecommerce\PriceLists\PriceListDashboardPresenter $dashboardPresenter
     *
     * @return \Webmagic\Dashboard\Dashboard
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function index(
        PriceListRepo $priceListRepo,
        Dashboard $dashboard,
        Request $request,
        PriceListDashboardPresenter $dashboardPresenter
    ) {
        $price_lists = $priceListRepo->getAll(10);

        return $dashboardPresenter->getTable($price_lists, $dashboard, $request);
    }

    /**
     * Show form in popup for creating price list
     *
     * @param \App\Ecommerce\PriceLists\PriceListDashboardPresenter $dashboardPresenter
     *
     * @return \Webmagic\Dashboard\Components\FormGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function create(PriceListDashboardPresenter $dashboardPresenter)
    {
        return $dashboardPresenter->getCreateForm();
    }

    /**
     * Show form in popup for editing price list
     *
     * @param $price_list_id
     * @param \App\Ecommerce\PriceLists\PriceListRepo $priceListRepo
     *
     * @return \Webmagic\Dashboard\Components\FormGenerator
     * @throws \Exception
     */
    public function edit($price_list_id, PriceListRepo $priceListRepo, PriceListDashboardPresenter $dashboardPresenter)
    {
        if (! $price_list = $priceListRepo->getByID($price_list_id)) {
            abort(404, 'Price list not found');
        };

        return $dashboardPresenter->getEditForm($price_list);
    }

    /**
     * Create price list
     *
     * @param \Illuminate\Http\Request                $request
     * @param \App\Ecommerce\PriceLists\PriceListRepo $priceListRepo
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function store(Request $request, PriceListRepo $priceListRepo)
    {
        if (! $priceListRepo->create($request->all())) {
            abort(500, 'Error on price list creating');
        }

        return $this->redirect(route('dashboard::price-lists.index'));
    }

    /**
     * Update price list
     *
     * @param $price_list_id
     * @param \Illuminate\Http\Request $request
     * @param \App\Ecommerce\PriceLists\PriceListRepo $priceListRepo
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function update($price_list_id, Request $request, PriceListRepo $priceListRepo)
    {
        if (! $priceListRepo->getByID($price_list_id)) {
            abort(404, 'Price list not found');
        }

        if (! $priceListRepo->update($price_list_id, $request->all())) {
            abort(500, 'Error on price list updating');
        }

        return $this->redirect(route('dashboard::price-lists.index'));
    }

    /**
     * Destroy price list
     *
     * @param                                        $price_list_id
     * @param \App\Ecommerce\PriceLists\PriceListRepo $priceListRepo
     *
     * @throws \Webmagic\Core\Entity\Exceptions\EntityNotExtendsModelException
     * @throws \Webmagic\Core\Entity\Exceptions\ModelNotDefinedException
     */
    public function destroy($price_list_id, PriceListRepo $priceListRepo)
    {
        if (! $priceListRepo->getByID($price_list_id)) {
            abort(404, 'Price list not found');
        };

        if (! $priceListRepo->destroy($price_list_id)) {
            abort(500, 'Error on price list destroying');
        }
    }

    /**
     * Get page with lists of products and packages of price list
     * Adding products and packages
     *
     * @param                                        $price_list_id
     * @param \App\Ecommerce\PriceLists\PriceListRepo $priceListRepo
     * @param \Webmagic\Dashboard\Dashboard $dashboard
     * @param \Illuminate\Http\Request $request
     * @param \App\Ecommerce\Products\ProductRepo $productRepo
     * @param \App\Ecommerce\Packages\PackageRepo $packageRepo
     *
     * @return \Webmagic\Dashboard\Pages\BasePage
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function show(
        $price_list_id,
        PriceListRepo $priceListRepo,
        Dashboard $dashboard,
        Request $request,
        ProductRepo $productRepo,
        PackageRepo $packageRepo
    ) {
        if (! $priceList = $priceListRepo->getByID($price_list_id)) {
            abort(404, 'Price list not found');
        };

        $page = $dashboard->page();
        $page->setPageTitle("$priceList->name");

        $addonsTable = $this->getAddonsTable($priceList->id, $productRepo, $request);

        $addAddonBtn = (new LinkButton())
            ->icon('fa fa-plus')
            ->content('Add')
            ->class('pull-right btn btn-flat btn-default')
            ->js()->openInModalOnClick()
            ->regular(route('dashboard::price-lists.addon.add', $price_list_id), 'GET', 'Add add-ons to the price list')
            ->dataAttr('reload-after-close-modal', 'true')
            ->dataAttr('modal-size', 'modal-lg');

        $packagesTable = $this->getPackagesTable($priceList->id, $packageRepo, $request);

        $addPackageBtn = (new LinkButton())
            ->icon('fa fa-plus')
            ->content('Add')
            ->class('pull-right btn btn-flat btn-default')
            ->js()->openInModalOnClick()
            ->regular(route('dashboard::price-lists.package.add', $price_list_id), 'GET', 'Add packages to the price list')
            ->dataAttr('reload-after-close-modal', 'true')
            ->dataAttr('modal-size', 'modal-lg');

        $dashboard->page()
            ->addElement()->h4Title('Packages')
            ->parent()
            ->addElement()->box($packagesTable)
            ->boxHeaderContent($addPackageBtn);

        $dashboard->page()->addElement()
            ->h4Title('Add-ons')
            ->parent()->addElement()
            ->box($addonsTable)
            ->boxHeaderContent($addAddonBtn);

        return $page;
    }

    /**
     * @param $price_list_id
     * @param \App\Ecommerce\PriceLists\PriceListRepo $priceListRepo
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function copy($price_list_id, PriceListRepo $priceListRepo)
    {
        if (! $priceList = $priceListRepo->getByID($price_list_id)) {
            abort(404, 'Price List not found');
        };

        $newPriceList = $priceListRepo->createCopyWithRelations($priceList);
        return $this->redirect(route('dashboard::price-lists.show', $newPriceList->id));
    }
}
