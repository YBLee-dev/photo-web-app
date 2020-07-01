<?php

namespace App\Ecommerce\PriceLists;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Webmagic\Dashboard\Components\FormGenerator;
use Webmagic\Dashboard\Components\TablePageGenerator;
use Webmagic\Dashboard\Dashboard;
use Webmagic\Dashboard\Elements\Links\Link;
use Webmagic\Dashboard\Elements\Links\LinkButton;

class PriceListDashboardPresenter
{
    /**
     * Table Page
     *
     * @param $price_lists
     * @param \Webmagic\Dashboard\Dashboard $dashboard
     * @param \Illuminate\Http\Request $request
     * @return \Webmagic\Dashboard\Dashboard
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getTable($price_lists, Dashboard $dashboard, Request $request)
    {
        (new TablePageGenerator($dashboard->page()))
            ->title('Price lists')
            ->tableTitles('ID', 'Name')
            ->showOnly('id', 'name')
            ->setConfig([
                'name' => function (PriceList $price_list) {
                    return (new Link())->content($price_list->name)
                        ->link(route('dashboard::price-lists.show', $price_list->id));
                },
            ])
            ->items($price_lists)
            ->withPagination($price_lists, route('dashboard::price-lists.index', $request->all()) )
            ->createLink(route('dashboard::price-lists.create'))
            ->setShowLinkClosure(function (PriceList $price_list) {
                return route('dashboard::price-lists.show', $price_list);
            })
            ->setEditLinkClosure(function (PriceList $price_list) {
                return route('dashboard::price-lists.edit', $price_list);
            })
            ->addElementsToToolsCollection(function (PriceList $price_list) {
                $btn = (new LinkButton())
                    ->icon('fa-trash')
                    ->dataAttr('item', ".js_item_$price_list->id")
                    ->class('text-red')
                    ->dataAttr('request', route('dashboard::price-lists.destroy', $price_list))
                    ->dataAttr('method', 'DELETE');

                if(count($price_list->galleries)){
                    $btn->addClass('disabled');
                    $btn = '<span title="This price list has connected galleries">'.$btn->render().'</span>';
                } elseif(count($price_list->carts)){
                    $btn->addClass('disabled');
                    $btn = '<span title="This price list has connected carts">'.$btn->render().'</span>';
                }elseif(count($price_list->orders)){
                    $btn->addClass('disabled');
                    $btn = '<span title="This price list has connected orders">'.$btn->render().'</span>';
                }else {
                    $btn->addClass('js_delete')->dataAttr('title', 'Delete');;
                }
                return $btn;
            })
            ->addElementsToToolsCollection(function (PriceList $price_list) {
                $createBtn = (new LinkButton())
                    ->icon('fa-clone')
                    ->class('btn-light   js_ajax-by-click-btn')
                    ->dataAttr('action', route('dashboard::price-lists.copy', $price_list))
                    ->dataAttr('title', 'Create copy')
                    ->dataAttr('method', 'POST');

                return $createBtn;
            })
            ->toolsInModal(true);


        if ($request->ajax()) {
            return $dashboard->page()->content()->toArray()['box_body'];
        }

        return $dashboard;
    }

    /**
     * Create Form Popup
     *
     * @return \Webmagic\Dashboard\Components\FormGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getCreateForm()
    {
        $formPageGenerator = (new FormGenerator())
            ->action(route('dashboard::price-lists.store'))
            ->ajax(true)
            ->method('POST')
            ->textInput('name', false, 'Name', true)
            ->submitButton('Create price list');

        return $formPageGenerator;
    }

    /**
     * Edit Form Popup
     *
     * @param \Illuminate\Database\Eloquent\Model $priceList
     * @return \Webmagic\Dashboard\Components\FormGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getEditForm(Model $price_list)
    {
        $formPageGenerator = (new FormGenerator())
            ->action(route('dashboard::price-lists.update', $price_list->id))
            ->ajax(true)
            ->method('PUT')
            ->textInput('name', $price_list, 'Name', true)
            ->submitButton('Update price list');

        return $formPageGenerator;
    }
}
