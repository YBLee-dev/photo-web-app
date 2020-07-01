<?php

namespace App\Ecommerce\Sizes;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Webmagic\Dashboard\Components\FormGenerator;
use Webmagic\Dashboard\Components\TablePageGenerator;
use Webmagic\Dashboard\Dashboard;
use Webmagic\Dashboard\Elements\Links\LinkButton;

class SizeCombinationDashboardPresenter
{
    /**
     * Table Page
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $sizes
     * @param \Webmagic\Dashboard\Dashboard $dashboard
     * @param \Illuminate\Http\Request $request
     * @param $sort
     * @param $sortBy
     * @return \Webmagic\Dashboard\Dashboard
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getTablePage(LengthAwarePaginator $combinations, Dashboard $dashboard, Request $request, $sort, $sortBy)
    {
        $sorting = [$sortBy => $sort];

        (new TablePageGenerator($dashboard->page()))
            ->title('Combinations')
            ->tableTitles('ID')
            ->addTitleWithSorting('Name', 'name', data_get($sorting, 'name', ''), true)
            ->addTableTitle('Sizes')
            ->showOnly('id', 'name', 'sizes')
            ->setConfig([
                'sizes' => function (SizeCombination $combination) {
                    $str = "";
                    foreach ($combination->sizes as $size){
                        $str .= $size->present()->prepareViewSize. "<br>";
                    }

                    return $str;
                },
            ])
            ->items($combinations)
            ->withPagination($combinations, route('dashboard::combinations.index',[
                'sort' => $sort,
                'sortBy' => $sortBy
            ]))
            ->createLink(route('dashboard::combinations.create'))
            ->setShowLinkClosure(function (SizeCombination $combination) {
                return route('dashboard::combinations.show', $combination);
            })
            ->setEditLinkClosure(function (SizeCombination $combination) {
                return route('dashboard::combinations.edit', $combination);
            })
//            ->setDestroyLinkClosure(function (SizeCombination $combination) {
//                return route('dashboard::combinations.destroy', $combination);
//            })

            ->addElementsToToolsCollection(function (SizeCombination $combination) {
                $btn = (new LinkButton())
                    ->icon('fa-trash')
                    ->dataAttr('item', ".js_item_$combination->id")
                    ->class('text-red')
                    ->dataAttr('request', route('dashboard::combinations.destroy', $combination))
                    ->dataAttr('method', 'DELETE');

                if($combination->orderItems->count()){
                    $btn->addClass('disabled');
                    $btn = '<span title="This combination has connected order items">'.$btn->render().'</span>';
                }elseif($combination->cartItems->count()){
                    $btn->addClass('disabled');
                    $btn = '<span title="This combination has connected cart items">'.$btn->render().'</span>';
                } else {
                    $btn->addClass('js_delete')->dataAttr('title', 'Delete');;
                }
                return $btn;
            })

            ->toolsInModal('true');

        if ($request->ajax()) {
            return $dashboard->page()->content()->toArray()['box_body'];
        }

        return $dashboard;
    }


    /**
     * Get Form
     *
     * @return \Webmagic\Dashboard\Components\FormGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getFormForCreate()
    {
        $formGenerator = (new FormGenerator())
            ->action(route('dashboard::combinations.store'))
            ->ajax(true)
            ->method('POST')
            ->textInput('name', null, 'Name', true)
            ->submitButton('Create');

        return $formGenerator;
    }

    /**
     * Get Form
     *
     * @param \Illuminate\Database\Eloquent\Model $size
     * @return \Webmagic\Dashboard\Components\FormGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getFormForEdit(Model $combination)
    {
        $formGenerator = (new FormGenerator())
            ->action(route('dashboard::combinations.update', $combination))
            ->ajax(true)
            ->method('PUT')
            ->textInput('name', $combination, 'Name', true)
            ->submitButton('Update');

        return $formGenerator;
    }
}
