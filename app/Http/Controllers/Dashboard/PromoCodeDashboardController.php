<?php

namespace App\Http\Controllers\Dashboard;

use App\Ecommerce\PromoCodes\PromoCodeDashboardPresenter;
use App\Ecommerce\PromoCodes\PromoCodeRepo;
use App\Ecommerce\PromoCodes\PromoCodeService;
use App\Ecommerce\PromoCodes\PromoCodeStatusTypesEnum;
use App\Ecommerce\PromoCodes\PromoCodeTypesEnum;
use App\Ecommerce\PromoCodes\PromoCodeUsedTypesEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Webmagic\Core\Controllers\AjaxRedirectTrait;
use Webmagic\Dashboard\Dashboard;

class PromoCodeDashboardController extends Controller
{
    use AjaxRedirectTrait;

    /**
     * Get list of promo codes with pagination, sorting and filtration
     *
     * Update statuses by expires field
     *
     * @param \App\Ecommerce\PromoCodes\PromoCodeRepo               $promoCodeRepo
     * @param \Webmagic\Dashboard\Dashboard                         $dashboard
     * @param \Illuminate\Http\Request                              $request
     * @param \App\Ecommerce\PromoCodes\PromoCodeDashboardPresenter $dashboardPresenter
     *
     * @return \Webmagic\Dashboard\Dashboard
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function index(
        PromoCodeRepo $promoCodeRepo,
        Dashboard $dashboard,
        Request $request,
        PromoCodeDashboardPresenter $dashboardPresenter,
        PromoCodeService $promoCodeService
    ) {
        $sort = $request->get('sort', 'asc');
        $sortBy = $request->get('sortBy', 'id');

        $status_types = PromoCodeStatusTypesEnum::values();
        array_unshift($status_types, 'All');
        $promoCodeService->checkAndUpdateStatuses();


        $promo_codes = $promoCodeRepo->getAllWithSortingAndFilter(
            $request->get('status'),
            $request->get('per_page', 10),
            $request->get('page', 1),
            $sort,
            $sortBy
        );

        return $dashboardPresenter->getTablePage(
            $promo_codes,
            $status_types,
            $dashboard,
            $request,
            $sort,
            $sortBy
        );
    }

    /**
     * Show creating form
     *
     * @return \Webmagic\Dashboard\Components\FormGenerator
     * @throws \Exception
     */
    public function create(PromoCodeDashboardPresenter $dashboardPresenter, PromoCodeRepo $promoCodeRepo)
    {
        $types = PromoCodeTypesEnum::values();
        $used_types = PromoCodeUsedTypesEnum::values();
        $redeem_code = $this->generateRedeemCode($promoCodeRepo);

        return $dashboardPresenter->getCreateForm($types, $used_types, $redeem_code);
    }

    /**
     * Create promo code
     *
     * Generate redeem code if it doesn't set in form
     *
     * @param \Illuminate\Http\Request                $request
     * @param \App\Ecommerce\PromoCodes\PromoCodeRepo $promoCodeRepo
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, PromoCodeRepo $promoCodeRepo)
    {
        $rules = [
            'name' => 'required',
            'type' => 'required',
        ];

        if (PromoCodeTypesEnum::PERCENT()->is($request->get('type'))) {
            $rules['discount_amount'] = 'required|numeric|min:1|max:100|integer';
        }
        if (PromoCodeTypesEnum::MONEY()->is($request->get('type'))) {
            $rules['discount_amount'] = 'required|numeric|min:0';
        }
        if ($request->get('redeem_code')) {
            $rules['redeem_code'] = 'unique:promo_codes,redeem_code';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if (! $request->get('redeem_code')) {
            $request['redeem_code'] = $this->generateRedeemCode($promoCodeRepo);
        }

        $request['status'] = PromoCodeStatusTypesEnum::ACTIVE;

        if (! $promoCodeRepo->create($request->all())) {
            abort(500, 'Error on promo code creating');
        }
    }

    /**
     * Generate redeem code till it make unique
     *
     * @param \App\Ecommerce\PromoCodes\PromoCodeRepo $promoCodeRepo
     *
     * @return string
     * @throws \Exception
     */
    protected function generateRedeemCode(PromoCodeRepo $promoCodeRepo)
    {
        $redeem_code = Str::random(8);
        if ($promoCodeRepo->getByRedeemCode($redeem_code)) {
            $this->generateRedeemCode($promoCodeRepo);
        }

        return $redeem_code;
    }

    /**
     * Show editing form
     *
     * @param int                                                   $promo_code_id
     * @param \App\Ecommerce\PromoCodes\PromoCodeRepo               $promoCodeRepo
     * @param \App\Ecommerce\PromoCodes\PromoCodeDashboardPresenter $dashboardPresenter
     *
     * @return \Webmagic\Dashboard\Components\FormGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function edit(
        int $promo_code_id,
        PromoCodeRepo $promoCodeRepo,
        PromoCodeDashboardPresenter $dashboardPresenter
    ) {
        if (! $promo_code = $promoCodeRepo->getByID($promo_code_id)) {
            abort(404, 'Promo code not found');
        };

        $types = PromoCodeTypesEnum::values();
        $used_types = PromoCodeUsedTypesEnum::values();

        return $dashboardPresenter->getEditForm($promo_code, $types, $used_types);
    }

    /**
     * Update promo code
     *
     * @param int                                     $promo_code_id
     * @param \App\Ecommerce\PromoCodes\PromoCodeRepo $promoCodeRepo
     * @param \Illuminate\Http\Request                $request
     *
     * @throws \Exception
     */
    public function update(int $promo_code_id, PromoCodeRepo $promoCodeRepo, Request $request)
    {
        if (! $promo_code = $promoCodeRepo->getByID($promo_code_id)) {
            abort(404, 'Promo code not found');
        };

        if (! $request->get('redeem_code')) {
            $request = $this->generateRedeemCode($promoCodeRepo);
        } else {
            $request->validate([
                'redeem_code' => 'unique:promo_codes,redeem_code,'.$promo_code_id,
            ]);
        }

        if (! $promoCodeRepo->update($promo_code_id, $request->all())) {
            abort(500, 'Error on promo code updating');
        }
    }

    /**
     * Delete promo code
     *
     * @param int                                     $promo_code_id
     * @param \App\Ecommerce\PromoCodes\PromoCodeRepo $promoCodeRepo
     *
     * @throws \Webmagic\Core\Entity\Exceptions\EntityNotExtendsModelException
     * @throws \Webmagic\Core\Entity\Exceptions\ModelNotDefinedException
     */
    public function destroy(int $promo_code_id, PromoCodeRepo $promoCodeRepo)
    {
        if (! $promo_code = $promoCodeRepo->getByID($promo_code_id)) {
            abort(404, 'Promo code not found');
        };

        if (! $promoCodeRepo->destroy($promo_code_id)) {
            abort(500, 'Error on promo code destroying');
        }
    }
}
