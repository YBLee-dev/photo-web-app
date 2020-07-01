<?php

namespace App\Ecommerce\Sizes;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Webmagic\Dashboard\Components\FormGenerator;
use Webmagic\Dashboard\Components\TableGenerator;
use Webmagic\Dashboard\Components\TablePageGenerator;
use Webmagic\Dashboard\Dashboard;
use Webmagic\Dashboard\Elements\Links\LinkButton;

class SizeDashboardPresenter
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
    public function getTablePage(LengthAwarePaginator $sizes, Dashboard $dashboard, Request $request, $sort, $sortBy)
    {
        $sorting = [$sortBy => $sort];

        (new TablePageGenerator($dashboard->page()))
            ->title('Sizes')
            ->tableTitles('ID')
            ->addTitleWithSorting('Name', 'name', data_get($sorting, 'name', ''), true)
            ->addTitleWithSorting('Width x Height', 'width', data_get($sorting, 'width', ''), true)
            ->showOnly('id', 'name', 'width_height')
            ->setConfig([
                'width_height' => function (Size $size) {
                    return $size->present()->prepareViewSize;
                },
            ])
            ->items($sizes)
            ->withPagination($sizes, route('dashboard::sizes.index',[
                'sort' => $sort,
                'sortBy' => $sortBy
            ]))
            ->createLink(route('dashboard::sizes.create'))
            ->setEditLinkClosure(function (Size $size) {
                return route('dashboard::sizes.edit', $size);
            })

            ->addElementsToToolsCollection(function (Size $size) {
                $btn = (new LinkButton())
                    ->icon('fa-trash')
                    ->dataAttr('item', ".js_item_$size->id")
                    ->class('text-red')
                    ->dataAttr('request', route('dashboard::sizes.destroy', $size))
                    ->dataAttr('method', 'DELETE');

                if($size->combinations->count()){
                    $btn->addClass('disabled');
                    $btn = '<span title="This size has connected combinations">'.$btn->render().'</span>';
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
            ->action(route('dashboard::sizes.store'))
            ->ajax(true)
            ->method('POST')
            ->textInput('name', null, 'Name', true)
            ->numberInput('width', null, 'Width', true, 0.01, 0)
            ->numberInput('height', null, 'Height', true, 0.01, 0)
            ->submitButton('Create size');

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
    public function getFormForEdit(Model $size)
    {
        $formGenerator = (new FormGenerator())
            ->action(route('dashboard::sizes.update', $size->id))
            ->ajax(true)
            ->method('PUT')
            ->textInput('name', $size, 'Name', true)
            ->numberInput('width', $size, 'Width', true, 0.01, 0)
            ->numberInput('height', $size, 'Height', true, 0.01, 0)
            ->submitButton('Update size');

        return $formGenerator;
    }

    /**
     * Get Table of combination sizes
     *
     * @param \Illuminate\Database\Eloquent\Model $combination
     * @param $sizes
     * @param \Webmagic\Dashboard\Dashboard $dashboard
     * @return \Webmagic\Dashboard\Dashboard
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getTable(Model $combination, $sizes, Dashboard $dashboard)
    {
        $sizesTable = (new TableGenerator())
            ->tableTitles('ID', 'Name', 'Width x Height', 'Quantity')
            ->showOnly('id', 'name', 'width_height', 'quantity')
            ->setConfig([
                'width_height' => function (Size $size) {
                    return $size->present()->prepareViewSize;
                },
                'quantity' => function (Size $size) {
                   return $size->pivot->quantity;
                },
            ])
            ->items($sizes)
            ->setEditLinkClosure(function (Size $size) use ($combination) {
                return route('dashboard::combinations.sizes.edit', [$combination, $size]);
            })
            ->setDestroyLinkClosure(function (Size $size) use ($combination) {
                return route('dashboard::combinations.sizes.remove', [$combination, $size]);
            })
            ->toolsInModal(true);

        $addProductBtn = (new LinkButton())
            ->icon('fa fa-plus')
            ->content('Add')
            ->class('pull-right btn btn-flat btn-default')
            ->js()->openInModalOnClick()
            ->regular(route('dashboard::combinations.sizes.add', $combination), 'GET', 'Add sizes to the combination')
            ->dataAttr('reload-after-close-modal', 'true');

        $dashboard->page()->addElement()
            ->h4Title('Associated sizes')
            ->parent()->addElement()
            ->box($sizesTable)
            ->boxHeaderContent($addProductBtn);

        return $dashboard;
    }

    /**
     * Get Table for adding size
     *
     * @param \Illuminate\Database\Eloquent\Model $combination
     * @param $items
     * @return \Webmagic\Dashboard\Components\TableGenerator
     */
    public function getTableForPopup(Model $combination, $items)
    {
        $addonsTable = (new TableGenerator())
            ->showOnly('id', 'name', 'width_height', 'status')
            ->setConfig([
                'width_height' => function (Size $size) {
                    return $size->present()->prepareViewSize;
                },
                'status' => function (Size $size) use ($combination) {
                    if($size->combinations()->allRelatedIds()->contains($combination->id)){
                        return '<i class="fa fa-check-circle text-success"> Added</i>';
                    } else {
                        $form =  (new FormGenerator())
                            ->action(route('dashboard::combinations.sizes.save', [$combination, $size]))
                            ->numberInput('quantity', 1, 'Quantity', true, '1', 1, 999999, ['style' => 'width:90px'])
                            ->method('POST')
                            ->ajax(true)
                            ->replaceBlockClass(".js_item_$size->id")
                            ->addSubmitButton(['data-modal-hide' => 'false'], 'Add');

                        return $form->getForm()->makeInline();
                    }
                }
            ])
            ->items($items)
            ->toolsInModal(true);

        return $addonsTable;
    }

    /**
     * Get Form for edit quantity
     *
     * @param \Illuminate\Database\Eloquent\Model $combination
     * @param \Illuminate\Database\Eloquent\Model $size
     * @return \Webmagic\Dashboard\Components\FormGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getFormForEditQuantity(Model $combination, Model $size)
    {
        $formPageGenerator = (new FormGenerator())
            ->action(route('dashboard::combinations.sizes.update', [$combination, $size]))
            ->ajax(true)
            ->method('PUT')
            ->numberInput('quantity', $combination->pivot->quantity, 'Quantity', true, 1, 0)
            ->submitButton('Update');

        return $formPageGenerator;
    }
}
