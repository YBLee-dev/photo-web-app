<?php

namespace App\Ecommerce\Packages;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;
use Webmagic\Dashboard\Components\FormPageGenerator;
use Webmagic\Dashboard\Components\TablePageGenerator;
use Webmagic\Dashboard\Dashboard;
use Webmagic\Dashboard\Elements\Links\Link;
use Webmagic\Dashboard\Elements\Links\LinkButton;

class PackageDashboardPresenter
{
    /**
     * Table Page
     *
     * @param \Illuminate\Pagination\AbstractPaginator $packages
     * @param \Webmagic\Dashboard\Dashboard $dashboard
     * @param \Illuminate\Http\Request $request
     * @param $sort
     * @param $sortBy
     * @return \Webmagic\Dashboard\Dashboard
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getTablePage(AbstractPaginator $packages, Dashboard $dashboard, Request $request, $sort, $sortBy)
    {
        $sorting = [$sortBy => $sort];
        (new TablePageGenerator($dashboard->page()))
            ->title('Packages')
            ->tableTitles('ID')
            ->addTitleWithSorting('Name', 'name', data_get($sorting, 'name', ''), true)
            ->addTitleWithSorting('Price', 'price', data_get($sorting, 'price', ''), true)
            ->addTitleWithSorting('Taxable', 'taxable', data_get($sorting, 'taxable', ''), true)
            ->addTitleWithSorting('Poses', 'limit_poses', data_get($sorting, 'limit_poses', ''), true)
            ->addTitleWithSorting('Available after deadline', 'available_after_deadline', data_get($sorting, 'available_after_deadline', ''), true)
            ->showOnly('id', 'name', 'price', 'taxable', 'limit_poses', 'available_after_deadline')
            ->setConfig([
                'name' => function (Package $package) {
                    return (new Link())->content($package->name)->link(route('dashboard::packages.edit', $package));
                },
                'taxable' => function (Package $package) {
                    if ($package->taxable) {
                        return '<i class="fa fa-check-circle text-success"></i>';
                    }
                },
                'price' => function (Package $package) {
                    return '$ '.$package->price;
                },
                'available_after_deadline' => function (Package $package) {
                    if($package->available_after_deadline){
                        return '<i class="fa fa-check-circle text-success"></i>';
                    }
                    return '<i class="fa fa-circle text-red"></i>';
                }
            ])
            ->items($packages)
            ->withPagination($packages, route('dashboard::packages.index',[
                'sort' => $sort,
                'sortBy' => $sortBy
            ]))
            ->createLink(route('dashboard::packages.create'))
            ->setShowLinkClosure(function (Package $package) {
                return route('dashboard::packages.show', $package);
            })
            ->setEditLinkClosure(function (Package $package) {
                return route('dashboard::packages.edit', $package);
            })
            ->addElementsToToolsCollection(function (Package $package) {
                $btn = (new LinkButton())
                    ->icon('fa-trash')
                    ->dataAttr('item', ".js_item_$package->id")
                    ->class('text-red')
                    ->dataAttr('request', route('dashboard::packages.destroy', $package))
                    ->dataAttr('method', 'DELETE');

                if(count($package->cart_items)){
                    $btn->addClass('disabled');
                    $btn = '<span title="This package has connected carts">'.$btn->render().'</span>';
                }  elseif(count($package->order_items)){
                    $btn->addClass('disabled');
                    $btn = '<span title="This package has connected orders">'.$btn->render().'</span>';
                } else {
                    $btn->addClass('js_delete')->dataAttr('title', 'Delete');;
                }
                return $btn;
            })
            ->addElementsToToolsCollection(function (Package $package) {
                $createBtn = (new LinkButton())
                    ->icon('fa-clone')
                    ->class('btn-light')
                    ->link(route('dashboard::packages.copy', $package))
                    ->dataAttr('title', 'Create copy');
                return $createBtn;
            });

        if ($request->ajax()) {
            return $dashboard->page()->content()->toArray()['box_body'];
        }

        return $dashboard;
    }

    /**
     * Create Form Page
     *
     * @return \Webmagic\Dashboard\Components\FormPageGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getCreateForm()
    {
        $formPageGenerator = (new FormPageGenerator())
            ->title('Package creating')
            ->action(route('dashboard::packages.store'))
            ->method('POST')
            ->textInput('name', false, 'Name', true)
            ->textInput('reference_name', false, 'Reference')
            ->numberInput('price', 0, 'Price', true, 0.01, 0)
            ->checkbox('taxable', false, 'Taxable')
            ->checkbox('available_after_deadline', false, 'Available after deadline')
            ->numberInput('limit_poses', 0, 'Limit Poses', true, 1, 0)
            ->visualEditor('description', false, 'Description')
            ->submitButtonTitle('Create');

        $formPageGenerator->getBox()->addElement()
            ->imageInput()
            ->title('Image (max size 10M')
            ->addClass('col-md-2')
            ->name('image');


        return $formPageGenerator;
    }

    /**
     * Edit Form Page
     *
     * @param \Illuminate\Database\Eloquent\Model $package
     * @return \Webmagic\Dashboard\Components\FormPageGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getEditForm(Model $package)
    {
        $formPageGenerator = (new FormPageGenerator())
            ->title('Package editing')
            ->action(route('dashboard::packages.update', $package))
            ->method('PUT')
            ->textInput('name', $package, 'Name', true)
            ->textInput('reference_name', $package, 'Reference')
            ->numberInput('price', $package, 'Price', false, 0.01, 0)
            ->checkbox('taxable', $package->taxable, 'Taxable')
            ->checkbox('available_after_deadline', $package->available_after_deadline, 'Available after deadline')
            ->numberInput('limit_poses', $package, 'Limit Poses', false, 1, 0)
            ->visualEditor('description', $package, 'Description')
            ->submitButtonTitle('Update');

        $formPageGenerator->getBox()->addElement()
            ->imageInput()
            ->title('Image (max size 10M)')
            ->addClass('col-md-2')
            ->imgUrl($package->present()->image)
            ->fileName('Current image')
            ->name('image');

        $formPageGenerator->getForm()->sendAllCheckbox(true);

        return $formPageGenerator;
    }

    /**
     * Description List
     *
     * @param \Illuminate\Database\Eloquent\Model $package
     * @param \Webmagic\Dashboard\Dashboard $dashboard
     * @return \Webmagic\Dashboard\Dashboard
     * @throws \Exception
     */
    public function getDescriptionList(Model $package, Dashboard $dashboard)
    {
        $page = $dashboard->page();
        $page->setPageTitle("$package->name")
            ->element()
            ->box()->addToolsLinkButton(route('dashboard::packages.edit', $package->id), '<i class="fa fa-edit "></i> Edit')
            ->element()->imagePreview()->imgUrl($package->present()->image)
            ->fileName('Current image')
            ->parent()
            ->addElement()->descriptionList(
                ['data' => [
                    'Name:' => $package->name,
                    'Reference' => $package->reference_name,
                    'Price' => '$ '.$package->price,
                    'Taxable' => $package->taxable ? 'Yes' : 'No',
                    'Available after deadline' => $package->available_after_deadline ? 'Yes' : 'No',
                    'Limited poses' => $package->limit_poses,
                    'Description' => $package->description,
                ],
                ]);

        return $dashboard;
    }
}
