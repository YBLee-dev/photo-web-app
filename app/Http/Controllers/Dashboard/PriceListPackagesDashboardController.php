<?php

namespace App\Http\Controllers\Dashboard;

use App\Ecommerce\Packages\Package;
use App\Ecommerce\Packages\PackageRepo;
use App\Ecommerce\PriceLists\PriceListRepo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Webmagic\Core\Controllers\AjaxRedirectTrait;
use Webmagic\Dashboard\Components\FormGenerator;
use Webmagic\Dashboard\Components\TableGenerator;
use Webmagic\Dashboard\Elements\Links\Link;

class PriceListPackagesDashboardController extends Controller
{
    use AjaxRedirectTrait;

    public function getPopupForAddingPackages($price_list_id, PackageRepo $packageRepo, Request $request)
    {
        if($request->get('name')){
            $packages = $packageRepo->getByName($request->get('name'));
        } else {
            $packages = $packageRepo->getAll();
        }
        $packageTable = $this->getPackagesTableForPopup($price_list_id,$packages);

        $packageTable->addFiltering()
            ->action(route('dashboard::price-lists.package.add', $price_list_id))
            ->textInput('name', $request->get('name'), 'Name');

        $packageTable->getFilter()->addElement()->button('Filter')->type('submit')->dataAttr('modal-hide', 'false');
        $packageTable->getFilter()->content()->getItem(0)->content()->attr('style', 'width:200px;');

        return $packageTable;
    }

    protected function getPackagesTableForPopup(int $price_list_id, $items)
    {
        $packagesTable = (new TableGenerator())
            ->showOnly('id', 'name', 'status')
            ->setConfig([
                'status' => function (Package $package) use($price_list_id) {
                    if($package->priceLists()->allRelatedIds()->contains($price_list_id)){
                        return '<i class="fa fa-check-circle text-success"></i> <span class="text-success">Added</span>';
                    } else {
                        $form =  (new FormGenerator())
                            ->action(route('dashboard::price-lists.package.save', [$price_list_id, $package->id]))
                            ->numberInput("price_$package->id", $package->price, 'Price', true, '0.01', 0, 999999, ['style' => 'width:90px'])
                            ->method('POST')
                            ->ajax(true)
                            ->replaceBlockClass(".js_item_$package->id")
                            ->addSubmitButton(['data-modal-hide' => 'false'], 'Add', 'btn btn-default');

                        return $form->getForm()->makeInline();
                    }
                }
            ])
            ->items($items)
            ->toolsInModal(true);

        return $packagesTable;
    }

    public function getPackagesTable($price_list_id, PackageRepo $packageRepo, Request $request)
    {
        $packages = $packageRepo->getByPriceListIdWithFilter(
            $price_list_id,
            $request->get('per_page', 10),
            $request->get('page', 1)
        );

        $addonsTable = (new TableGenerator())
            ->tableTitles('ID', 'Name (reference)', 'Price', 'Taxable', 'Poses', 'After deadline')
            ->showOnly('id', 'name', 'price', 'taxable', 'limit_poses', 'after_deadline')
            ->setConfig([
                'name' => function (Package $package) use ($price_list_id) {
                    $content = $package->name . ($package->reference ? " ($package->reference)" : '');
                    return (new Link())->content($content)->link( route('dashboard::packages.show', $package));
                },
                'price' => function (Package $package) {
                    return '$ '.$package->price;
                },
                'taxable' => function (Package $package) {
                    if($package->taxable){
                        return '<i class="fa fa-check-circle text-success"></i>';
                    }
                },
                'after_deadline' => function (Package $package) {
                    if($package->available_after_deadline){
                        return '<i class="fa fa-check-circle text-success"></i>';
                    }
                    return '<i class="fa fa-circle text-red"></i>';
                }
            ])
            ->items($packages)
            ->withPagination($packages, route('dashboard::price-lists.package.list',[$price_list_id]) )
            ->setEditLinkClosure(function (Package $package) use ($price_list_id) {
                return route('dashboard::price-lists.package.edit', [$price_list_id, $package->id]);
            })->setDestroyLinkClosure(function (Package $package) use ($price_list_id) {
                return route('dashboard::price-lists.package.remove', [$price_list_id, $package->id]);
            })
            ->toolsInModal(true);

        return $addonsTable;
    }

    public function addPackage($price_list_id, $package_id, Request $request, PriceListRepo $priceListRepo, PackageRepo $packageRepo)
    {
        if (! $price_list = $priceListRepo->getByID($price_list_id)) {
            abort(404, 'Price list not found');
        };
        if (! $package = $packageRepo->getByID($package_id)) {
            abort(404, 'Product not found');
        };

        $price_list->packages()->attach($package->id, ['price' => $request->get("price_$package->id", $package->price)]);

        return "<tr class=' js_item_$package->id' data-original-title='' title=''>
                    <td data-original-title='' title='' style='vertical-align: middle;'>$package->id</td>
                    <td data-original-title='' title='' style='vertical-align: middle;'>$package->name</td>
                    <td data-original-title='' title='' style='vertical-align: middle;'><i class='fa fa-check-circle text-success'></i> <span class='text-success'>Added</span></td>
            </tr>";
    }

    public function getPopupFotEditPackage($price_list_id, $package_id, PackageRepo $packageRepo)
    {
        if (! $package = $packageRepo->getByID($package_id)) {
            abort(404, 'Price list not found');
        };

        $priceList = $packageRepo->getRelatedPriceListById($price_list_id, $package);

        $formPageGenerator = (new FormGenerator())
            ->action(route('dashboard::price-lists.package.update', [$price_list_id, $package_id]))
            ->ajax(true)
            ->method('PUT')
            ->numberInput('price', $priceList->pivot->price, 'Price', true, '0.01', 0)
            ->submitButton('Update');

        return $formPageGenerator;
    }

    public function updatePackage($price_list_id, $package_Id, Request $request, PriceListRepo $priceListRepo, PackageRepo $packageRepo)
    {
        $request->validate([
            'price' => 'required',
        ]);
        if (! $priceList = $priceListRepo->getByID($price_list_id)) {
            abort(404, 'Price list not found');
        };
        if (! $package = $packageRepo->getByID($package_Id)) {
            abort(404, 'Package not found');
        };
        $priceList->packages()->detach($package->id);
        $priceList->packages()->attach($package->id, ['price' => $request->get('price')]);

        return $this->redirect(route('dashboard::price-lists.show', $price_list_id));
    }

    public function removePackage($price_list_id, $package_id, PriceListRepo $priceListRepo, PackageRepo $packageRepo)
    {
        if (! $priceList = $priceListRepo->getByID($price_list_id)) {
            abort(404, 'Price list not found');
        };
        if (! $package = $packageRepo->getByID($package_id)) {
            abort(404, 'Package not found');
        };
        $priceList->packages()->detach($package->id);
    }
}
