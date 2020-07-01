<?php

namespace App\Http\Controllers\Dashboard;

use App\Ecommerce\Sizes\SizeDashboardPresenter;
use App\Ecommerce\Sizes\SizeRepo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Webmagic\Core\Controllers\AjaxRedirectTrait;
use Webmagic\Dashboard\Dashboard;


class SizeDashboardController extends Controller
{
    use AjaxRedirectTrait;

    /**
     * Show list of sizes with pagination
     * and sorting by name or width
     *
     * @param \App\Ecommerce\Sizes\SizeRepo $sizeRepo
     * @param \Webmagic\Dashboard\Dashboard $dashboard
     * @param \Illuminate\Http\Request      $request
     *
     * @return \Webmagic\Dashboard\Dashboard
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     * @throws \Exception
     */
    public function index(
        SizeRepo $sizeRepo,
        Dashboard $dashboard,
        Request $request,
        SizeDashboardPresenter $dashboardPresenter
    )
    {
        $sort = $request->get('sort', 'asc');
        $sortBy = $request->get('sortBy', 'id');

        $sizes = $sizeRepo->getAllWithSorting(
            $request->get('per_page', 10),
            $request->get('page', 1),
            $sort,
            $sortBy
        );

        return $dashboardPresenter->getTablePage($sizes, $dashboard, $request, $sort, $sortBy);
    }

    /**
     * Show form in popup for creating
     *
     * @return \Webmagic\Dashboard\Components\FormGenerator
     * @throws \Exception
     */
    public function create(SizeDashboardPresenter $dashboardPresenter)
    {
        return $dashboardPresenter->getFormForCreate();
    }

    /**
     * Show form in popup for editing
     *
     * @param $size_id
     * @param \App\Ecommerce\Sizes\SizeRepo $sizeRepo
     * @param \App\Ecommerce\Sizes\SizeDashboardPresenter $dashboardPresenter
     *
     * @return \Webmagic\Dashboard\Components\FormGenerator
     * @throws \Exception
     */
    public function edit($size_id, SizeRepo $sizeRepo, SizeDashboardPresenter $dashboardPresenter)
    {
        if (! $size = $sizeRepo->getByID($size_id)) {
            abort(404);
        };

        return $dashboardPresenter->getFormForEdit($size);
    }

    /**
     * Create size and redirect to list of sizes
     *
     * @param \Illuminate\Http\Request      $request
     * @param \App\Ecommerce\Sizes\SizeRepo $sizeRepo
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function store(Request $request, SizeRepo $sizeRepo)
    {
        if(!$sizeRepo->create($request->all())){
            abort(500, 'Error on size creating');
        }

        return $this->redirect(route('dashboard::sizes.index'));
    }

    /**
     * Update size and redirect to list of sizes
     *
     * @param $size_id
     * @param \Illuminate\Http\Request $request
     * @param \App\Ecommerce\Sizes\SizeRepo $sizeRepo
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function update($size_id, Request $request, SizeRepo $sizeRepo)
    {
        if (! $size = $sizeRepo->getByID($size_id)) {
            abort(404);
        };

        if (! $sizeRepo->update($size_id, $request->all())) {
            abort(500);
        }

        return $this->redirect(route('dashboard::sizes.index'));
    }

    /**
     * Destroy size
     *
     * @param                              $size_id
     * @param \App\Ecommerce\Sizes\SizeRepo $sizeRepo
     *
     * @throws \Webmagic\Core\Entity\Exceptions\EntityNotExtendsModelException
     * @throws \Webmagic\Core\Entity\Exceptions\ModelNotDefinedException
     * @throws \Exception
     */
    public function destroy($size_id, SizeRepo $sizeRepo)
    {
        if (! $size = $sizeRepo->getByID($size_id)) {
            abort(404);
        };

        if (! $sizeRepo->destroy($size->id)) {
            abort(500);
        }
    }
}
