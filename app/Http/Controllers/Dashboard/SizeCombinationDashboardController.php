<?php

namespace App\Http\Controllers\Dashboard;

use App\Ecommerce\Sizes\SizeCombinationDashboardPresenter;
use App\Ecommerce\Sizes\SizeCombinationRepo;
use App\Ecommerce\Sizes\SizeDashboardPresenter;
use App\Ecommerce\Sizes\SizeRepo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Webmagic\Core\Controllers\AjaxRedirectTrait;
use Webmagic\Dashboard\Dashboard;

class SizeCombinationDashboardController extends Controller
{
    use AjaxRedirectTrait;

    /**
     * Show list of sizes with pagination
     * and sorting by name or width
     *
     * @param \App\Ecommerce\Sizes\SizeCombinationRepo               $combinationRepo
     * @param \Webmagic\Dashboard\Dashboard                          $dashboard
     * @param \Illuminate\Http\Request                               $request
     * @param \App\Ecommerce\Sizes\SizeCombinationDashboardPresenter $dashboardPresenter
     *
     * @return \Webmagic\Dashboard\Dashboard
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function index(
        SizeCombinationRepo $combinationRepo,
        Dashboard $dashboard,
        Request $request,
        SizeCombinationDashboardPresenter $dashboardPresenter
    )
    {
        $sort = $request->get('sort', 'asc');
        $sortBy = $request->get('sortBy', 'id');

        $combinations = $combinationRepo->getAllWithSorting(
            $request->get('per_page', 10),
            $request->get('page', 1),
            $sort,
            $sortBy
        );

        return $dashboardPresenter->getTablePage($combinations, $dashboard, $request, $sort, $sortBy);
    }

    /**
     * Show form in popup for creating
     *
     * @return \Webmagic\Dashboard\Components\FormGenerator
     * @throws \Exception
     */
    public function create(SizeCombinationDashboardPresenter $dashboardPresenter)
    {
        return $dashboardPresenter->getFormForCreate();
    }

    /**
     * Show form in popup for editing
     *
     * @param $combination_id
     * @param \App\Ecommerce\Sizes\SizeCombinationRepo $combinationRepo
     * @param \App\Ecommerce\Sizes\SizeCombinationDashboardPresenter $dashboardPresenter
     *
     * @return \Webmagic\Dashboard\Components\FormGenerator
     * @throws \Exception
     */
    public function edit(
        $combination_id,
        SizeCombinationRepo $combinationRepo,
        SizeCombinationDashboardPresenter $dashboardPresenter
    ) {
        if (! $combination = $combinationRepo->getByID($combination_id)) {
            abort(404, 'Combination not found');
        };

        return $dashboardPresenter->getFormForEdit($combination);
    }

    /**
     * Create size and redirect to list of combinations
     *
     * @param \Illuminate\Http\Request                 $request
     * @param \App\Ecommerce\Sizes\SizeCombinationRepo $combinationRepo
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function store(Request $request, SizeCombinationRepo $combinationRepo)
    {
        if(!$combinationRepo->create($request->all())){
            abort(500, 'Error on combination creating');
        }

        return $this->redirect(route('dashboard::combinations.index'));
    }

    /**
     * Update size and redirect to list of combinations
     *
     * @param $combination_id
     * @param \Illuminate\Http\Request $request
     * @param \App\Ecommerce\Sizes\SizeCombinationRepo $combinationRepo
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function update($combination_id, Request $request, SizeCombinationRepo $combinationRepo)
    {
        if (! $combination = $combinationRepo->getByID($combination_id)) {
            abort(404, 'Combination not found');
        };

        if (! $combinationRepo->update($combination_id, $request->all())) {
            abort(500, 'Error on combination updating');
        }

        return $this->redirect(route('dashboard::combinations.index'));
    }

    /**
     * Destroy combination
     *
     * @param $combination_id
     * @param \App\Ecommerce\Sizes\SizeCombinationRepo $combinationRepo
     *
     * @throws \Webmagic\Core\Entity\Exceptions\EntityNotExtendsModelException
     * @throws \Webmagic\Core\Entity\Exceptions\ModelNotDefinedException
     * @throws \Exception
     */
    public function destroy($combination_id, SizeCombinationRepo $combinationRepo)
    {
        if (! $combination = $combinationRepo->getByID($combination_id)) {
            abort(404, 'Combination not found');
        };

        if (! $combinationRepo->destroy($combination->id)) {
            abort(500, 'Error on combination destroying');
        }
    }

    /**
     * Show combination page for adding sizes
     *
     * @param int                                         $combination_id
     * @param \App\Ecommerce\Sizes\SizeCombinationRepo    $combinationRepo
     * @param \Webmagic\Dashboard\Dashboard               $dashboard
     * @param \Illuminate\Http\Request                    $request
     * @param \App\Ecommerce\Sizes\SizeDashboardPresenter $sizeDashboardPresenter
     *
     * @return mixed
     * @throws \Exception
     */
    public function show(
        int $combination_id,
        SizeCombinationRepo $combinationRepo,
        Dashboard $dashboard,
        Request $request,
        SizeDashboardPresenter $sizeDashboardPresenter
    ) {
        if (! $combination = $combinationRepo->getByID($combination_id)) {
            abort(404, 'Combination not found');
        };

        $page = $dashboard->page();
        $page->setPageTitle("$combination->name");

        $sizes = $combination->sizes;

        return $sizeDashboardPresenter->getTable(
            $combination,
            $sizes,
            $dashboard,
            $request
        );
    }

    /**
     * Get form in popup for adding sizes
     *
     * @param int                                         $combination_id
     * @param \App\Ecommerce\Sizes\SizeRepo               $sizeRepo
     * @param \App\Ecommerce\Sizes\SizeCombinationRepo    $combinationRepo
     * @param \App\Ecommerce\Sizes\SizeDashboardPresenter $dashboardPresenter
     *
     * @return \App\Ecommerce\Sizes\SizeDashboardPresenter
     * @throws \Exception
     */
    public function getPopupForAddingSizes(
        int $combination_id,
        SizeRepo $sizeRepo,
        SizeCombinationRepo $combinationRepo,
        SizeDashboardPresenter $dashboardPresenter
    ) {
        if (! $combination = $combinationRepo->getByID($combination_id)) {
            abort(404, 'Combination not found');
        };

        $sizes = $sizeRepo->getAll();
        return $dashboardPresenter->getTableForPopup($combination, $sizes);
    }

    /**
     * Add size to combination
     *
     * @param int                                      $combination_id
     * @param int                                      $size_id
     * @param \Illuminate\Http\Request                 $request
     * @param \App\Ecommerce\Sizes\SizeRepo            $sizeRepo
     * @param \App\Ecommerce\Sizes\SizeCombinationRepo $combinationRepo
     *
     * @return string
     * @throws \Exception
     */
    public function addSize(
        int $combination_id,
        int $size_id,
        Request $request,
        SizeRepo $sizeRepo,
        SizeCombinationRepo $combinationRepo
    ) {
        $request->validate([
            'quantity' => 'numeric|min:0'
        ]);

        if (! $combination = $combinationRepo->getByID($combination_id)) {
            abort(404, 'Combination not found');
        };

        if (! $size = $sizeRepo->getByID($size_id)) {
            abort(404, 'Size not found');
        };

        $combination->sizes()->attach($size->id, ['quantity' => $request->get('quantity')]);

        return "<tr class=' js_item_$size->id' data-original-title='' title=''>
                    <td data-original-title='' title=''>$size->id</td>
                    <td data-original-title='' title=''>$size->name</td>
                    <td data-original-title='' title=''>{$size->present()->prepareViewSize}</td>
                    <td data-original-title='' title=''>{$request->get('quantity')}<br data-original-title='' title=''></td>
                    <td data-original-title='' title=''><i class='fa fa-check-circle text-success' data-original-title='' title=''> Added</i></td>
            </tr>";
    }

    /**
     * Add popup for edit size in combination
     *
     * @param int                                         $combination_id
     * @param int                                         $size_id
     * @param \App\Ecommerce\Sizes\SizeRepo               $sizeRepo
     * @param \App\Ecommerce\Sizes\SizeDashboardPresenter $dashboardPresenter
     *
     * @return \Webmagic\Dashboard\Components\FormGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getPopupFotEditSize(
        int $combination_id,
        int $size_id,
        SizeRepo $sizeRepo,
        SizeDashboardPresenter $dashboardPresenter
    ) {
        if (! $size = $sizeRepo->getByID($size_id)) {
            abort(404, 'Size not found');
        };

        if (! $combination = $sizeRepo->getRelatedCombinationById($combination_id, $size)) {
            abort(404, 'Combination not found');
        };

        return $dashboardPresenter->getFormForEditQuantity($combination, $size);
    }

    /**
     * Update size quantity in combination
     *
     * @param int                                      $combination_id
     * @param int                                      $size_id
     * @param \Illuminate\Http\Request                 $request
     * @param \App\Ecommerce\Sizes\SizeRepo            $sizeRepo
     * @param \App\Ecommerce\Sizes\SizeCombinationRepo $combinationRepo
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function updateSize(
        int $combination_id,
        int $size_id,
        Request $request,
        SizeRepo $sizeRepo,
        SizeCombinationRepo $combinationRepo
    ) {
        $request->validate([
            'quantity' => 'required',
        ]);

        if (! $combination = $combinationRepo->getByID($combination_id)) {
            abort(404, 'Combination not found');
        };

        if (! $size = $sizeRepo->getByID($size_id)) {
            abort(404, 'Size not found');
        };

        $combination->sizes()->detach($size->id);
        $combination->sizes()->attach($size->id, ['quantity' => $request->get('quantity')]);

        return $this->redirect(route('dashboard::combinations.show', $combination));
    }

    /**
     * Remove size from combination
     *
     * @param int                                      $combination_id
     * @param int                                      $size_id
     * @param \App\Ecommerce\Sizes\SizeRepo            $sizeRepo
     * @param \App\Ecommerce\Sizes\SizeCombinationRepo $combinationRepo
     *
     * @throws \Exception
     */
    public function removeSize(
        int $combination_id,
        int $size_id,
        SizeRepo $sizeRepo,
        SizeCombinationRepo $combinationRepo
    ) {
        if (! $combination = $combinationRepo->getByID($combination_id)) {
            abort(404, 'Combination not found');
        };

        if (! $size = $sizeRepo->getByID($size_id)) {
            abort(404, 'Size not found');
        };

        $combination->sizes()->detach($size->id);
    }
}
