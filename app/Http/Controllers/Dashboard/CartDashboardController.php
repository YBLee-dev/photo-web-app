<?php

namespace App\Http\Controllers\Dashboard;


use App\Ecommerce\Cart\CartDashboardPresenter;
use App\Ecommerce\Cart\CartRepo;
use App\Ecommerce\Cart\CartService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Webmagic\Dashboard\Dashboard;

class CartDashboardController extends Controller
{
    /**
     * Display list of carts with filtration, sorting and pagination
     *
     * @param \Illuminate\Http\Request                   $request
     * @param \Webmagic\Dashboard\Dashboard              $dashboard
     * @param \App\Ecommerce\Cart\CartRepo               $cartRepo
     * @param \App\Ecommerce\Cart\CartDashboardPresenter $dashboardPresenter
     *
     * @return \Webmagic\Dashboard\Dashboard
     * @throws \Exception
     */
    public function index(
        Request $request,
        Dashboard $dashboard,
        CartRepo $cartRepo,
        CartDashboardPresenter $dashboardPresenter,
        CartService $cartService
    ) {
        $validated = $request->validate([
            'sort' => 'in:asc,desc',
            'sortBy' => 'in:updated_at,gallery_name'
        ]);

        $sort = data_get($request, 'sort') ? $validated['sort'] : 'desc';
        $sortBy = data_get($request->all(), 'sortBy') ? $validated['sortBy'] : 'updated_at';

        $carts = $cartRepo->getByFilter(
            $request->get('abandoned'),
            $request->get('galleries'),
            $request->get('subgalleries'),
            $request->get('price_lists'),
            $request->get('date_from'),
            $request->get('date_to'),
            $request->get('per_page', 10),
            $request->get('page', 1),
            $sort,
            $sortBy
        );

        $galleries_for_select = $cartRepo->getGalleriesForSelect();
        $subgalleries_for_select = $cartRepo->getSubgalleriesForSelect();
        $price_lists_for_select = $cartRepo->getPriceListsForSelect();

        $cartService->checkAndUpdateStatuses();

        return $dashboardPresenter->getTablePage(
            $carts,
            $galleries_for_select,
            $subgalleries_for_select,
            $price_lists_for_select,
            $dashboard,
            $request,
            $sort,
            $sortBy
        );
    }

    /**
     * Cart show page with info about items
     *
     * @param int                                        $cart_id
     * @param \App\Ecommerce\Cart\CartRepo               $cartRepo
     * @param \App\Ecommerce\Cart\CartDashboardPresenter $dashboardPresenter
     * @param \Webmagic\Dashboard\Dashboard              $dashboard
     *
     * @return \Webmagic\Dashboard\Pages\BasePage
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     * @throws \Exception
     */
    public function show(
        int $cart_id,
        CartRepo $cartRepo,
        CartDashboardPresenter $dashboardPresenter,
        Dashboard $dashboard,
        CartService $cartService
    ) {
        if (! $cart = $cartRepo->getByID($cart_id)) {
            abort(404, 'Cart Not found');
        };

        $packages = $cart->items()->whereNotNull('package_id')->get()->groupBy('cart_item_id');
        $addons = $cart->items()->whereNull('package_id')->get();

        $cartService->checkAndUpdateStatuses();

        $dashboard =  $dashboardPresenter->getDescriptionList($cart, $dashboard);

        return $dashboardPresenter->generateCartItemsTable($cart, $packages, $addons, $dashboard);
    }

    /**
     * Cart destroying if it abandoned
     *
     * @param int                          $cart_id
     * @param \App\Ecommerce\Cart\CartRepo $cartRepo
     *
     * @throws \Webmagic\Core\Entity\Exceptions\EntityNotExtendsModelException
     * @throws \Webmagic\Core\Entity\Exceptions\ModelNotDefinedException
     */
    public function destroy(int $cart_id, CartRepo $cartRepo)
    {
        if (! $cart = $cartRepo->getByID($cart_id)) {
            abort(404, 'Cart not found');
        };

        if($cart->abandoned){
            if (! $cartRepo->destroy($cart_id)) {
                abort(500, 'Error on cart destroying');
            }
        }
    }
}
