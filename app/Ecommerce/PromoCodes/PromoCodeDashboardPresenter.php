<?php

namespace App\Ecommerce\PromoCodes;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;
use Webmagic\Dashboard\Components\FormPageGenerator;
use Webmagic\Dashboard\Components\TablePageGenerator;
use Webmagic\Dashboard\Dashboard;
use Webmagic\Dashboard\Elements\Links\Link;
use Webmagic\Dashboard\Elements\Links\LinkButton;

class PromoCodeDashboardPresenter
{
    /**
     * Table Page with filter and sorting
     *
     * @param \Illuminate\Pagination\AbstractPaginator $promo_codes
     * @param \Webmagic\Dashboard\Dashboard $dashboard
     * @param \Illuminate\Http\Request $request
     * @param $sort
     * @param $sortBy
     * @return \Webmagic\Dashboard\Dashboard
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getTablePage(AbstractPaginator $promo_codes, array $status_types,  Dashboard $dashboard, Request $request, $sort, $sortBy)
    {
        $sorting = [$sortBy => $sort];
        $tablePageGenerator = (new TablePageGenerator($dashboard->page()))
            ->title('Promo codes')
            ->tableTitles('ID')
            ->addTitleWithSorting('Status', 'status', data_get($sorting, 'status', ''), true, route('dashboard::promo-codes.index',$request->all()))
            ->addTableTitle('Name')
            ->addTableTitle('Code')
            ->addTableTitle('Total cart')
            ->addTitleWithSorting('Rate', 'discount_amount', data_get($sorting, 'discount_amount', ''), true,route('dashboard::promo-codes.index',$request->all()))
            ->addTableTitle('Period')
            ->addTableTitle('Used')
            ->showOnly('id', 'status', 'name', 'redeem_code', 'total_cart', 'rate', 'period', 'used_count')
            ->setConfig([
                'name' => function (PromoCode $promoCode) {
                    return (new Link())->content($promoCode->name)->link(route('dashboard::promo-codes.edit', $promoCode));
                },
                'total_cart' => function (PromoCode $promoCode) {
                    $from = $promoCode->cart_total_from ?: '0.00';
                    $to = $promoCode->cart_total_to ?: '999999.00';
                    return "$$from - $$to";
                },
                'rate' => function (PromoCode $promoCode) {
                    if(PromoCodeTypesEnum::MONEY()->is($promoCode->type)){
                        return "$ $promoCode->discount_amount";
                    } else {
                        return "$promoCode->discount_amount %";
                    }
                },
                'period' => function (PromoCode $promoCode) {
                    switch ($promoCode){
                        case (!$promoCode->active_from && !$promoCode->expires_at):
                            return "n/a";
                            break;
                        case ($promoCode->active_from && $promoCode->expires_at):
                            return "$promoCode->active_from <br> $promoCode->expires_at";
                            break;
                        case (!$promoCode->active_from && $promoCode->expires_at):
                            return "Till <br> $promoCode->expires_at";
                            break;
                        case ($promoCode->active_from && !$promoCode->expires_at):
                            return "From <br> $promoCode->active_from";
                            break;
                    }
                },
                'used_count' => function (PromoCode $promoCode) {
                    return count($promoCode->orders);
                    //return (new Link())->content(0)->link(url('#'));
                },
            ])
            ->items($promo_codes)
            ->withPagination($promo_codes, route('dashboard::promo-codes.index',$request->all()))
            ->createLink(route('dashboard::promo-codes.create'))
            ->setEditLinkClosure(function (PromoCode $promoCode) {
                return route('dashboard::promo-codes.edit', $promoCode);
            })
            ->addElementsToToolsCollection(function (PromoCode $promoCode) {
                $btn = (new LinkButton())
                    ->icon('fa-trash')
                    ->dataAttr('item', ".js_item_$promoCode->id")
                    ->class('text-red')
                    ->dataAttr('request', route('dashboard::promo-codes.destroy', $promoCode))
                    ->dataAttr('method', 'DELETE');

                if(count($promoCode->orders)){
                    $btn->addClass('disabled');
                    $btn = '<span title="This promo has connected orders">'.$btn->render().'</span>';
                } else {
                    $btn->addClass('js_delete')->dataAttr('title', 'Delete');;
                }
                return $btn;
            })
        ;

        $tablePageGenerator->addFiltering()
            ->action(route('dashboard::promo-codes.index', $request->all()))
            ->select('status', $status_types, $request->get('status'), 'Status')
            ->submitButton('Filter');

        if ($request->ajax()) {
            return $dashboard->page()->content()->toArray()['box_body'];
        }

        return $dashboard;
    }

    /**
     * Create Form Page
     *
     * @param array $types
     * @param array $used_types
     * @param string $redeem_code
     * @return \Webmagic\Dashboard\Components\FormGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getCreateForm(array $types, array $used_types, string $redeem_code)
    {
        $formPageGenerator = (new FormPageGenerator())
            ->title('Create a new promo code')
            ->action(route('dashboard::promo-codes.store'))
            ->ajax(true)
            ->method('POST')
            ->textInput('name', false, 'Coupon name', true)
            ->textInput('redeem_code', $redeem_code, 'Redeem code', false)
            ->select('type', array_combine($types, $types),1, 'Type', true)
            ->numberInput('discount_amount', false, 'Discount amount', true, 1, 1 )
            ->datePickerJS('active_from', null, 'Active from')
            ->datePickerJs('expires_at', null, 'Expires at')
            ->select('may_be_used', array_combine($used_types, $used_types),false, 'May be used', true)
            ->numberInput('cart_total_from', 0.00, 'Cart total from', false, 0.01, 0.00, 999999)
            ->numberInput('cart_total_to', 999999.00, 'Cart total to', false, 0.01, 0.00, 999999)
            ->textarea('description', false, 'Description')
            ->submitButtonTitle('Create');

        return $formPageGenerator;
    }

    /**
     * Edit Form Page
     *
     * @param \Illuminate\Database\Eloquent\Model $promo_code
     * @param array $types
     * @param array $used_types
     * @return \Webmagic\Dashboard\Components\FormPageGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getEditForm(Model $promo_code, array $types, array $used_types)
    {
        $formPageGenerator = (new FormPageGenerator())
            ->title("Promo code editing", $promo_code->name )
            ->action(route('dashboard::promo-codes.update', $promo_code))
            ->method('PUT')
            ->textInput('name', $promo_code, 'Coupon name', true)
            ->textInput('redeem_code', $promo_code, 'Redeem code', false)
            ->select('type', array_combine($types, $types),$promo_code, 'Type', true)
            ->numberInput('discount_amount', $promo_code, 'Discount amount', true, 1, 1 )
            ->datePickerJS('active_from', $promo_code, 'Active from', false)
            ->datePickerJs('expires_at', $promo_code, 'Expires at', false)
            ->select('may_be_used', array_combine($used_types, $used_types),$promo_code, 'May be used', true)
            ->numberInput('cart_total_from', $promo_code, 'Cart total from', false, 0.01, 0.00, 999999)
            ->numberInput('cart_total_to', $promo_code, 'Cart total to', false, 0.01, 0.00, 999999)
            ->textarea('description', $promo_code, 'Description')
            ->submitButtonTitle('Update');

        return $formPageGenerator;
    }
}
