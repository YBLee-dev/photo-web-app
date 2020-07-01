<?php

namespace App\Photos\Galleries;


use App\Photos\Photos\Photo;
use App\Photos\Photos\PhotoRepo;
use App\Photos\Schools\SchoolRepo;
use App\Photos\Seasons\SeasonRepo;
use App\Processing\ProcessingStatusesEnum;
use App\Processing\Scenarios\GroupPhotosGenerationScenario;
use App\Users\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Laracasts\Presenter\Exceptions\PresenterException;
use Webmagic\Dashboard\Components\FormGenerator;
use Webmagic\Dashboard\Components\FormPageGenerator;
use Webmagic\Dashboard\Components\TableGenerator;
use Webmagic\Dashboard\Components\TablePageGenerator;
use Webmagic\Dashboard\Components\TilesListGenerator;
use Webmagic\Dashboard\Components\TilesListPageGenerator;
use Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable;
use Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined;
use Webmagic\Dashboard\Dashboard;
use Webmagic\Dashboard\Elements\Boxes\Box;
use Webmagic\Dashboard\Elements\Buttons\DefaultButton;
use Webmagic\Dashboard\Elements\Forms\Elements\Input;
use Webmagic\Dashboard\Elements\Links\Link;
use Webmagic\Dashboard\Elements\Links\LinkButton;
use Webmagic\Dashboard\Pages\BasePage;

class GalleryDashboardPresenter
{
    /**
     * Create Form Page
     *
     * @param string $galleryName
     * @param        $uploadedGalleryOwner
     * @param        $user
     * @param array  $price_lists
     * @param array  $seasons
     * @param null   $photographers
     *
     * @return FormPageGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws Exception
     */
    public function getCreateFormPage(
        string $galleryName,
        $uploadedGalleryOwner,
        $user,
        array $price_lists,
        array $seasons,
        $photographers = null
    ) {
        if(!$seasons){
            $box = (new Box());
            $link = (new LinkButton())->content('To the school page')
                ->class('btn-info')
                ->link(route('dashboard::schools.index'));

            $box->addContent($link);
            $box->addBoxHeaderContent("There aren't empty schools and seasons. 
            All existing schools and seasons are with galleries. Please, create the new school or a new season in an existing school.");
            $dashboard = (new Dashboard());
            $dashboard->page()->setPageTitle('Gallery creating', $galleryName)->addContent($box);

            return $dashboard;
        }

        $formPageGenerator = (new FormPageGenerator())
            ->title('Gallery creating')
            ->action(route('dashboard::unprocessed-photos.convert'))
            ->method('POST')
            ->title('Gallery creating', $galleryName);

        $formPageGenerator->getForm()->content()
            ->addElement()
            ->input([
                'name'  => 'ftp_path',
                'type'  => 'hidden',
                'value' => "$galleryName",
            ])
            ->parent()
            ->addElement()
            ->input([
                'name'  => 'ftp_user_id',
                'type'  => 'hidden',
                'value' => "$uploadedGalleryOwner->id",
            ]);

        $formPageGenerator
            ->select('price_list_id', array_prepend($price_lists, '', ''), null, 'Regular price list', true)
            ->select('staff_price_list_id',  array_prepend($price_lists, '', ''), null, 'Staff price list', true)
            ->hiddenInput('photographer_id', $uploadedGalleryOwner->id)
            ->select('photographer_id', $photographers ?? [$user->id => $user->email], $uploadedGalleryOwner->id,
                'Photographer', true, false, $user->isAdmin() ?[]: ['disabled' => 'disabled'])
            ->selectJS('season_id', $seasons, 1, 'School and Season', true)
            ->datePickerJS('deadline', '', 'Deadline', true)
            ->submitButtonTitle('Create and start processing');

        return $formPageGenerator;
    }

    /**
     * Edit Form Page
     *
     * @param Model $gallery
     * @param       $user
     * @param array $price_lists
     * @param array $seasons
     * @param null  $photographers
     *
     * @return FormPageGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws Exception
     */
    public function getEditGalleryFormPage(
        Model $gallery,
        $user,
        array $price_lists,
        array $seasons,
        $photographers = null
    ) {
        $formPageGenerator = (new FormPageGenerator())
            ->title('Gallery editing')
            ->action(route('dashboard::gallery.update', $gallery->id))
            ->method('PUT');

        $formPageGenerator
            ->select('price_list_id', $price_lists, $gallery->priceList->id ?? false, 'Price list', true)
            ->select('staff_price_list_id', $price_lists, $gallery, 'Staff Price list', true)
            ->select('photographer_id', $photographers ?? [$user->id => $user->email], $gallery->user->id,
                'Photographer', true,false, $user->isAdmin() ?[]: ['disabled' => 'disabled'])
            //->checkbox('choose_season', false, 'Change school and season', false, ['class' => 'js_checkbox-control'])
            //->selectJS('season_id', $seasons, $gallery->season_id, 'School and Season', true, false,
            //    [ 'class' => "form-control js-select2 js_control-state", 'data-control-state' => "disable", 'data-control-el' => ".js_checkbox-control", 'data-state-active-by-empty' => "1"])
            ->textInput('season_id', $gallery->season_id, '', false,
                [ 'class' => "js_control-state", 'data-control-state' => "disable", 'data-control-el' => ".js_checkbox-control", 'type' => 'hidden'])
            ->datePickerJS('deadline', $gallery->deadline, 'Deadline', true)
            ->submitButtonTitle('Update');

        return $formPageGenerator;
    }

    /**
     * Gallery Description List
     *
     * @param           $gallery
     * @param Dashboard $dashboard
     *
     * @return BasePage
     * @throws NoOneFieldsWereDefined
     * @throws Exception
     * @throws Exception
     * @throws Exception
     */
    public function getGalleriesDescriptionList($gallery, Dashboard $dashboard)
    {
        $page = $dashboard->page();
        $box = $page->setPageTitle($gallery->present()->name, ' gallery view')
            ->element()
            ->grid()
            ->lgRowCount(3)
            ->mdRowCount(3)
            ->smRowCount(1)
            ->xsRowCount(1)
            ->addElement()
            ->box()
            ->footerAvailable(false)
            ->boxTitle('Gallery details')
            ->addElement('box_tools')
                ->linkButton()
                ->content('<i class="fa fa-eye "></i> View on website')
                ->link(route('gallery', $gallery->password))
                ->class('btn-default')
                ->icon('')
                ->attrs(['target'=>'_blank'])
                ->parent()
            ->addToolsLinkButton(route('dashboard::gallery.edit', $gallery), '<i class="fa fa-edit "></i> Edit')
            ->addToolsLinkButton(route('dashboard::gallery.subgallery.create', $gallery),
                '<i class="fa fa-plus "></i> Add Sub Gallery')
            ->element()->descriptionList(
                [
                    'data' => [
                        'Status'       => $gallery->controls()->shortStatusElement(),
                        'Regular price list'   => (new Link())->content($gallery->priceList->name)->link(route('dashboard::price-lists.show', $gallery->price_list_id)),
                        'Staff price list'   => (new Link())->content($gallery->staffPriceList->name)->link(route('dashboard::price-lists.show', $gallery->staff_price_list_id)),
                        'Photographer' => $gallery->user->email,
                        'School'       => (new Link())->content($gallery->school->name)->link(route('dashboard::schools.index')),
                        'Season'       => (new Link())->content($gallery->season->name)->link(route('dashboard::schools.seasons.edit',  [$gallery->school, $gallery->season->id])),
                        'Password'     => $gallery->password,
                        'Deadline'     => Carbon::parse($gallery->deadline)->format('F d, Y'),
                    ],
                ])->isHorizontal(true)
            ->parent('grid')
            ->addElement()
            ->box()
            ->footerAvailable(false)
            ->boxTitle('Gallery exports')
            ->element()->descriptionList(
                [
                    'data' => [
                        'Gallery metadata bulk updating' => (new LinkButton())->content('Export metadata')->icon('fa-download')
                                ->link(route('dashboard::gallery.export.clients', $gallery->id))
                                ->class('btn-primary') .
                                (new LinkButton())->content('Import metadata')->icon('fa-upload')
                                    ->class('btn-primary')
                                    ->js()->openInModalOnClick()
                                    ->regular(route('dashboard::gallery.import.clients', $gallery->id), 'GET', 'Import clients metadata'),
                         '-' => '',
                         'Export gallery passwords CSV' => (new LinkButton())->content('Export passwords')->icon('fa-download')
                                                        ->link(route('dashboard::gallery.passwords', $gallery->id))
                                                        ->class('btn-primary'),

                    ],
                ])
            ->parent('grid')
            ->addElement()
            ->box()->boxTitle('Gallery photo processing')
            ->footerAvailable(false)
            ->element()->descriptionList(
                [
                    'data' => [
                        'Download proof photos' => $gallery->dashboardElements()->proofPhotosZipProcessingButton(),
                        '-' => '',
                        'Gallery group photos processing' => $this->getGroupPhotoGenerationButton($gallery)

                    ],
                ])
            ->parent('page');

        return $page;
    }

    /**
     * Prepare gallery process button
     *
     * @param Gallery $gallery
     *
     * @return string
     * @throws NoOneFieldsWereDefined
     * @throws Exception
     */
    public function getGroupPhotoGenerationButton(Gallery $gallery)
    {
        $uniqClass = 'js_autoupdate-' . uniqid();

        $btn = (new DefaultButton());

        if ($gallery->isGroupPhotoGenerationInProgress()) {
            $btn->addIcon('fa-cog fa-spin')
                ->class('btn-danger')
                ->content('Group photo generation ...')
                ->js()->contentAutoUpdate()->replaceCurrentElWithContent(route('dashboard::gallery.get-status-button',
                    [$gallery->id]))
                ->attr('disabled', 'disabled');

            if($gallery->istGroupPhotoGenerationInWait()){
                $reloadBtn = (new LinkButton())
                    ->js()->tooltip()->regular('Manual continue group photos processes if it stopped')
                    ->class('btn-primary '. $uniqClass)
                    ->icon('fa-refresh')
                    ->link(route('dashboard::gallery.continue-group-processing', $gallery->id));
            }
            return $btn .' '. ($reloadBtn ?? '');

        } elseif ($gallery->isGroupPhotosWasGenerated()) {
            $btn->content('Update gallery group photo')
                ->addIcon('fa-cog')
                ->class('btn-success')
                ->js()->sendRequestOnClick()
                ->replaceWithResponse(route('dashboard::gallery.group-photo-generation-start', [$gallery->id]),
                    ".$uniqClass", ['btn-class' => $uniqClass], 'GET', true, true);
        } else {
            $btn->content('Generate gallery group photo')
                ->addIcon('fa-cog')
                ->class('btn-danger')
                ->js()->sendRequestOnClick()
                ->replaceWithResponse(route('dashboard::gallery.group-photo-generation-start', [$gallery->id]),
                    ".$uniqClass", ['btn-class' => $uniqClass], 'GET', true, true);
        }

        return $btn->addClass($uniqClass);
    }

    /**
     *  Prepare gallery process statuses box info
     *
     * @param Gallery $gallery
     *
     * @return mixed
     * @throws PresenterException
     * @throws NoOneFieldsWereDefined
     * @throws Exception
     */
    public function getConvertingStatuses(Gallery $gallery)
    {
        $data = $gallery->present()->currentStatuses();

        $uniqClass = 'js_content-update-'.uniqid();

        $table = (new TableGenerator())
            ->items($data)
            ->showOnly('short_name', 'status')
            ->addToConfig('status', function ($item){
                $icon = "<i class='fa fa-cog fa-spin'></i> ";
                if (ProcessingStatusesEnum::FINISHED()->is($item['status'])) {
                    $icon = "<i class='fa fa-check text-green'></i>";
                }
                if (ProcessingStatusesEnum::FAILED()->is($item['status'])) {
                    $icon = "<i class='fa fa-close text-red'></i>";
                }
                return "<div style='width:200px'>$icon {$item['status']}</div>";
            });

        if(array_search('In progress', array_column($data, 'status')) === false){
            $reloadBtn = (new LinkButton())
                ->js()->tooltip()->regular('Manual continue processes if it stopped')
                ->class('btn-primary '. $uniqClass)
                ->icon('fa-refresh')
                ->link(route('dashboard::gallery.continue-initial-processing', $gallery->id));
        }


        $box = (new Box())->addClass($uniqClass)
            ->boxTitle('Gallery not ready yet. Please, wait before all photos will be processed ')
            ->boxFooter(isset($reloadBtn) ? $reloadBtn->render() : '')
            ->addContent($table)
            ->js()
            ->contentAutoUpdate()->apply($uniqClass, route('dashboard::gallery.get-converting-statuses', $gallery->id), 'GET', '3000', true);

        return $box;
    }

    /**
     * Prepare gallery process button
     *
     * @param Gallery $gallery
     *
     * @return DefaultButton
     * @throws NoOneFieldsWereDefined
     * @throws Exception
     */
    public function getProofPhotosExportGenerationButton(Gallery $gallery)
    {
        $uniqClass = 'js_autoupdate-' . uniqid();

        $btn = (new DefaultButton());

        if ($gallery->isGroupPhotoGenerationInProgress()) {
            $btn->addIcon('fa-cog fa-spin')
                ->class('btn-danger')
                ->content('Proof photos zip preparing ...')
                ->js()->contentAutoUpdate()->replaceCurrentElWithContent(route('dashboard::gallery.get-status-button',
                    [$gallery->id]))
                ->attr('disabled', 'disabled');
        } else {
            $btn->content('Prepare proof photos zip')
                ->addIcon('fa-cog')
                ->class($uniqClass)
                ->addClass('btn-danger')
                ->js()->sendRequestOnClick()
                ->replaceWithResponse(route('dashboard::gallery.group-photo-generation-start', [$gallery->id]),
                    ".$uniqClass", ['btn-class' => $uniqClass], 'GET', false, false);
        }

        return $btn;
    }

    public function getGalleryShortStatusBlock(Gallery $gallery)
    {
        $uniqClass = 'js_autoupdate-' . uniqid();
        $route = route('dashboard::gallery.get-converting-status', $gallery->id);

        $spinner = "<i class='fa fa-cog fa-spin'></i> ";
        $status = $gallery->present()->shortStatus();

        if(!$gallery->isAvailableForView() || $gallery->isGroupPhotoGenerationInProgress()){
           $status = $spinner.$status;
        }

        $block = "<div class=' $uniqClass js-update' 
            data-url=$route
            data-timeout='5000' 
            data-class='.$uniqClass'
            data-method='GET' 
            data-replace='1'>
             {$status}
            </div>";


        return $block;
    }

    /**
     * Table Page
     *
     * @param $galleries
     * @param Request $request
     * @param $dashboard
     *
     * @return TablePageGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws Exception
     */
    public function getGalleriesTablePage($galleries, Request $request, $dashboard)
    {
        $tablePageGenerator = (new TablePageGenerator($dashboard->page()))
            ->title('Galleries list')
            ->items($galleries)
            ->withPagination($galleries, route('dashboard::gallery.index', $request->all()))
            ->tableTitles('ID', 'Name', 'Status', 'Photographer', 'School', 'Season', 'Sub galleries count')
            ->showOnly('id', 'name', 'status', 'user_email', 'school', 'season', 'sub_galleries_count')
            ->setConfig([
                'name'                => function (Gallery $gallery) {
                    return (new Link())->content($gallery->present()->name)->link(route('dashboard::gallery.show',
                        $gallery->id));
                },
                'sub_galleries_count' => function (Gallery $gallery) {
                    return $gallery->subgalleries->count();
                },
                'status'              => function (Gallery $gallery) {
                    if($gallery->isAvailableForView() && !$gallery->isGroupPhotoGenerationInProgress()){
                        return $gallery->present()->shortStatus();
                    } else {
                        return $this->getGalleryShortStatusBlock($gallery);
                    }
                },
                'user_email'          => function (Gallery $gallery) {
                    return (new Link())->content($gallery->user->email)->link(route('dashboard::users.show',
                        $gallery->user->id));
                },
                'school'              => function (Gallery $gallery) {
                    return $gallery->school->name ?? '';
                },
                'season'              => function (Gallery $gallery) {
                    return $gallery->season->name ?? '';
                },
            ])
            ->setEditLinkClosure(function (Gallery $gallery) {
                if ($gallery->status) {
                    return route('dashboard::gallery.edit', ['id' => $gallery->id]);
                }
            })
            ->setShowLinkClosure(function (Gallery $gallery) {
                if ($gallery->status) {
                    return route('dashboard::gallery.show', ['id' => $gallery->id]);
                }
            })
            ->addElementsToToolsCollection(function (Gallery $gallery) {
                $btn = (new LinkButton())
                    ->icon('fa-trash')
                    ->dataAttr('item', ".js_item_$gallery->id")
                    ->class('text-red')
                    ->dataAttr('request', route('dashboard::gallery.destroy', $gallery->id))
                    ->dataAttr('method', 'DELETE');

                if (count($gallery->carts)) {
                    $btn->addClass('disabled');
                    $btn = '<span title="This gallery has connected carts">' . $btn->render() . '</span>';
                } elseif (count($gallery->orders)) {
                    $btn->addClass('disabled');
                    $btn = '<span title="This gallery has connected orders">' . $btn->render() . '</span>';
                } else {
                    $btn->addClass('js_delete')->dataAttr('title', 'Delete');;
                }
                return $btn;
            });

        $schools_for_select = (new SchoolRepo())->getForSelect('name', 'name');
        $seasons_for_select = (new SeasonRepo())->getForSelect('name', 'name');

        $tablePageGenerator->addFiltering()
            ->action(route('dashboard::gallery.index'))
            ->method('post')
            ->selectJS('schools[]', $schools_for_select, ['schools[]' => $request->get('schools')], 'School', false, true, ['style'=>'width: 200px'])
            ->selectJS('seasons[]', $seasons_for_select, ['seasons[]' => $request->get('seasons')], 'Season', false, true, ['style'=>'width: 200px'])
            ->addClearButton(['class' => 'btn btn-default margin'], 'Clear')
            ->addSubmitButton(['class' => 'btn btn-info margin'], 'Filter');

        if ($request->ajax()) {
            return $tablePageGenerator->getTable();
        }

        return $tablePageGenerator;
    }

    /**
     * Unprocessed photos Table Page
     *
     * @param           $files
     * @param Dashboard $dashboard
     *
     * @return Dashboard
     */
    public function getUnprocessedPhotosTablePage($files, Dashboard $dashboard)
    {
        (new TablePageGenerator($dashboard->page()))
            ->title('Unprocessed photos')
            ->items($files)
            ->tableTitles('Name', 'Photographer')
            ->showOnly('gallery_name', 'user_email', 'status')
            ->setConfig([
                'user_email' => function ($file) {
                    return (new Link())->content($file['user_email'])->link(route('dashboard::users.show',
                        $file['user_id']));
                },
                'status'     => function ($file) {
                    return (new LinkButton())
                        ->content('Convert to gallery')
                        ->link(route('dashboard::unprocessed-photos.get-popup-for-convert', [$file['gallery_name'], $file['user_id']]))
                        ->js()->openInModalOnClick()
                        ->regular(route('dashboard::unprocessed-photos.get-popup-for-convert', [$file['gallery_name'], $file['user_id']]), 'POST', 'Attention!')
                        ->dataAttr('reload-after-close-modal', 'true');
                },
            ])
            ->setDestroyLinkClosure(function ($file) {
                return route('dashboard::unprocessed-photos.destroy', [$file['gallery_name'], $file['user_id']]);
            });

        return $dashboard;
    }

    /**
     * Form with file input
     *
     * @param Model $gallery
     *
     * @return FormGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     */
    public function getFormForImportFile(Model $gallery)
    {
        $formGenerator = (new FormGenerator())
            ->action(route('dashboard::gallery.import.clients', $gallery->id))
            ->ajax(true)
            ->method('POST')
            ->fileInput('file', '', 'CSV file')
            ->submitButton('Import');

        return $formGenerator;
    }

    /**
     * Get Tabs Page
     *
     * @param Dashboard $dashboard
     * @param           $subgalleriesTab
     * @param           $croppedPhotosListTab
     *
     * @param $miniWalletCollagesListPage
     * @param $classPhotos
     * @param $staffCommonPhoto
     * @param $schoolPhoto
     * @param $proofPhotosTiles
     * @param $iDCardsTiles
     * @return Dashboard
     * @throws NoOneFieldsWereDefined
     * @throws Exception
     * @throws Exception
     * @throws Exception
     * @throws Exception
     * @throws Exception
     * @throws Exception
     * @throws Exception
     */
    public function prepareTabsPage(
        Dashboard $dashboard,
        $subgalleriesTab,
        $croppedPhotosListTab,
        $miniWalletCollagesListPage,
        $classPhotos,
        $staffCommonPhoto,
        $schoolPhoto,
        $proofPhotosTiles,
        $iDCardsTiles
    ) {
        $dashboard->page()
            ->addElement()->tabs()
            ->addElement('tabs')->tab()->title('Sub-galleries')->active(true)
            ->content($subgalleriesTab->render())
            ->parent('tabs')
            ->addElement('tabs')->tab()->title('Cropped photos')
            ->content($croppedPhotosListTab)
            ->parent('tabs')
            ->addElement('tabs')->tab()->title('Proof photos')
            ->content($proofPhotosTiles->render())
            ->parent('tabs')
            ->addElement('tabs')->tab()->title('Wallet photos')
            ->content($miniWalletCollagesListPage->render())
            ->parent('tabs')
            ->addElement('tabs')->tab()->title('ID cards')
            ->content($iDCardsTiles->render())
            ->parent('tabs')
            ->addElement('tabs')->tab()->title('Common classes photos')
            ->content($classPhotos->render())
            ->parent('tabs')
            ->addElement('tabs')->tab()->title('Staff common photo')
            ->content($staffCommonPhoto->render())
            ->parent('tabs')
            ->addElement('tabs')->tab()->title('School photo')
            ->content($schoolPhoto->render());

        return $dashboard;
    }

    /**
     * Prepare tiles for class photos
     *
     * @param Gallery $gallery
     *
     * @return TilesListGenerator|string
     * @throws Exception
     */
    public function prepareClassPhotosTiles(Gallery $gallery, bool $groupPhotoInProgress = false)
    {
        if ($groupPhotoInProgress) {
            return $this->getGroupPhotosConvertingStatuses($gallery);
        }

        /** @var Photo [] $classPhotos */
        $classPhotos = $gallery->classesCommonPhotos;

        if (!count($classPhotos)) {
            return (new Box())->addClass('text-danger')
                ->boxTitle('Gallery group photos are not generated yet. Please, generate gallery group photos');
        }

        //todo optimize
        $preparedClassPhotosPathsArray = [];
        foreach ($classPhotos as $photo) {
            $people = $photo->people;


            $preparedClassPhotosPathsArray[] = [
                'path' => $photo->present()->originalUrl(),
                'classroom' => count($people) ? $people->last()->classroom : ''
            ];
        }

        $tilesList = (new TilesListGenerator())
            ->setItems($preparedClassPhotosPathsArray)
            ->setItemRenderingClosure(function ($classPhotoPath) {
                $box = (new Box())
                    ->footerAvailable(false)
                    ->boxTitle($classPhotoPath['classroom'])
                    ->addElement()
                    ->imagePreview()
                    ->imgUrl($classPhotoPath['path']);
                return $box->parent();
            });

        $tilesList->getGrid()
                ->lgRowCount(4)
                ->mdRowCount(2)
                ->smRowCount(1)
                ->xsRowCount(1)->render();

        return $tilesList;
    }

    /**
     * @param Gallery $gallery
     *
     * @return TilesListGenerator|string
     * @throws PresenterException
     * @throws Exception
     */
    public function prepareIDCardsTiles(Gallery $gallery, bool $groupPhotoInProgress = false)
    {
        if ($groupPhotoInProgress) {
            return $this->getGroupPhotosConvertingStatuses($gallery);
        }
        $subGalleries = $gallery->subGalleries;
        $proofPhotosPaths = [];
        foreach ($subGalleries as $subGallery) {
            // Have not ID cards for children
            $person = $subGallery->person;
            if(!$person->isStaff()){
                continue;
            }

            $proofPhotosPaths[] = [
                'path' => $person->present()->IDCardLandscapeUrl()
            ];
            $proofPhotosPaths[] = [
                'path' => $person->present()->IDCardPortraitUrl()
            ];
        }
        // Don't show if we have not ID cards yet
        if(!count($proofPhotosPaths) || data_get( array_first($proofPhotosPaths), 'path') == ''){
            return (new Box())->addClass('text-danger')
                ->boxTitle('Gallery group photos are not generated yet. Please, generate gallery group photos');
        }

        $tilesList = (new TilesListGenerator())
            ->setItems($proofPhotosPaths)
            ->setItemRenderingClosure(function ($proofPhotoUrl) {
                $box = (new Box())->addElement()->imagePreview()
                    ->imgUrl($proofPhotoUrl['path']);
                return $box->parent();
            });

        $tilesList->getGrid()
            ->lgRowCount(4)
            ->mdRowCount(2)
            ->smRowCount(1)
            ->xsRowCount(1)->render();

        return $tilesList;
    }

    /**
     * Prepare tiles for class photos
     *
     * @param Gallery $gallery
     *
     * @return string
     * @throws PresenterException
     * @throws Exception
     */
    public function prepareStaffCommonPhotoTiles(Gallery $gallery, bool $groupPhotoInProgress = false)
    {
        if ($groupPhotoInProgress) {
            return $this->getGroupPhotosConvertingStatuses($gallery);
        }

        $staffCommonPhotoUrl = $gallery->present()->staffPhotoUrl();

        if(!$staffCommonPhotoUrl){
            return (new Box())->addClass('text-danger')
                ->boxTitle('Gallery group photos are not generated yet. Please, generate gallery group photos');
        }

        //todo optimize
        $staffPhotoPath = [
            [
                'path' => $staffCommonPhotoUrl
            ]
        ];

        $tilesList = (new TilesListGenerator())
            ->setItems($staffPhotoPath)
            ->setItemRenderingClosure(function ($staffPhotoPath) {
                $box = (new Box())->addElement()->imagePreview()
                    ->imgUrl($staffPhotoPath['path']);
                return $box->parent();
            });

        $tilesList->getGrid()
            ->lgRowCount(4)
            ->mdRowCount(2)
            ->smRowCount(1)
            ->xsRowCount(1)->render();

        return $tilesList;
    }

    /**
     * @param Gallery $gallery
     *
     * @return mixed
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws PresenterException
     */
    public function getGroupPhotosConvertingStatuses(Gallery $gallery)
    {
        $cacheKey = 'groupPhotosConvertingStatuses_gallery_'.$gallery->id;

        if(!Cache::has($cacheKey)){
            $data = $gallery->present()->getGalleryGroupProcesses();

            $table = (new TableGenerator())
                ->items($data)
                ->showOnly('short_name', 'status')
                ->addToConfig('status', function ($item){
                    $icon = "<i class='fa fa-cog fa-spin'></i> ";
                    if (ProcessingStatusesEnum::FINISHED()->is($item['status'])) {
                        $icon = "<i class='fa fa-check text-green'></i>";
                    }
                    if (ProcessingStatusesEnum::FAILED()->is($item['status'])) {
                        $icon = "<i class='fa fa-close text-red'></i>";
                    }
                    return "<div style='width:200px'>$icon {$item['status']}</div>";
                });

            Cache::put($cacheKey, $table->render(), 0.5);
        }

        $uniqClass = 'js_group-photo-content-update_'.uniqid();

        $box = new Box();
        $box->addClass($uniqClass)
            ->boxTitle('Gallery group photos not ready yet. Please, wait before all photos will be processed')
            ->addContent(Cache::get($cacheKey))
            ->js()
            ->contentAutoUpdate()->apply($uniqClass, route('dashboard::gallery.get-group-photo-converting-statuses', $gallery->id), 'GET', '5000', true);

        return $box;
    }

    /**
     * Prepare tiles for class photos
     *
     * @param Gallery $gallery
     *
     * @return string
     * @throws PresenterException
     * @throws Exception
     */
    public function prepareSchoolPhotoTiles(Gallery $gallery, bool $groupPhotoInProgress = false)
    {
        if ($groupPhotoInProgress) {
            return $this->getGroupPhotosConvertingStatuses($gallery);
        }

        $schoolPhotoUrl = $gallery->present()->schoolPhotoUrl();

        if (!$schoolPhotoUrl){
           return (new Box())->addClass('text-danger')
                ->boxTitle('Gallery group photos are not generated yet. Please, generate gallery group photos');
        }

        //todo optimize
        $schoolPhotoPath = [
            [
                'path' => $schoolPhotoUrl
            ]
        ];

        $tilesList = (new TilesListGenerator())
            ->setItems($schoolPhotoPath)
            ->setItemRenderingClosure(function ($schoolPhotoPath) {
                $box = (new Box())->addElement()->imagePreview()
                    ->imgUrl($schoolPhotoPath['path']);
                return $box->parent();
            });

        return $tilesList;
    }

    /**
     * Get tiles with mini wallet colages images
     *
     * @param Gallery $gallery
     *
     * @return string
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws PresenterException
     */
    public function getMiniWalletCollagesTiles(Gallery $gallery, bool $groupPhotoInProgress = false)
    {
        if ($groupPhotoInProgress) {
            return $this->getGroupPhotosConvertingStatuses($gallery);
        }

        $items = $gallery->miniWalletCollages;

        if(!count($items)){
            return (new Box())->addClass('text-danger')
                ->boxTitle('Gallery group photos are not generated yet. Please, generate gallery group photos');
        }

        $preparedItems = [];
        foreach ($items as $item) {
            $preparedItems[] = [
                'path' => $item->present()->originalUrl(),
            ];
        }

        $tilesList = (new TilesListGenerator())
            ->setItems($preparedItems)
            ->setOnly('path')
            ->setItemRenderingClosure(function ($item) {
                $box = (new Box())->addElement()->imagePreview()
                    ->imgUrl($item['path']);
                return $box->parent();
            });

        $tilesList->getGrid()
            ->lgRowCount(4)
            ->mdRowCount(2)
            ->smRowCount(1)
            ->xsRowCount(1)->render();

        return $tilesList;
    }

    /**
     * Get info for popup for converting gallery
     *
     * @param string $directory_name
     * @param User   $user
     *
     * @return Box
     * @throws NoOneFieldsWereDefined
     */
    public function getPopupForStartingConvertingProcess(string $directory_name, User $user)
    {
        $box = (new Box());

        $link1 = (new LinkButton())->content('Cancel')->dataAttr('dismiss', 'modal');
        $link2 = (new LinkButton())
            ->content('Analyse')
            ->addClass('pull-right')
            ->dataAttr('modal-hide', "false")
            ->addClass(' js_ajax-by-click-btn')
            ->dataAttr('replace-blk', ".js_base_modal_empty .box")
            ->dataAttr('action', route('dashboard::unprocessed-photos.check-directory-structure',[$directory_name, $user->id]));

        $box->addContent($link1);
        $box->addContent($link2);

        $box->addBoxHeaderContent('Please, make sure that you uploaded all photos you want before we start gallery converting process.
        Also we should analyse a directories structure. ');

        return $box;
    }

    /**
     * Get errors info for popup about directories errors before convert gallery
     *
     * @param array $errors
     *
     * @return Box
     * @throws NoOneFieldsWereDefined
     */
    public function getErrorsOfDirectoryStructure(array $errors)
    {
        $box = (new Box())->footerAvailable(true);
        $box->boxTitle('<p class="text-danger">Structure errors</p>');

        $prepared_str = '';
        $counter = 1;

        foreach ($errors as $error){
            if(isset($error['files'])){
                $prepared_str .= $counter.'. '.$error['name'].PHP_EOL;
                foreach ($error['files'] as $fileName) {
                    $prepared_str .= ' - '.$fileName.PHP_EOL;
                }
                $prepared_str .= $error['solution'].PHP_EOL;
                $counter++;
            }
        }

        $link1 = (new LinkButton())->content('Ok')->dataAttr('dismiss', 'modal');


        $box->addContent(nl2br(e($prepared_str)));

        $box->footerAvailable(true);
        $box->addBoxFooter(nl2br(e('Sorry, with this error we cannot continue a gallery creating process'. PHP_EOL . PHP_EOL)));
        $box->addBoxFooter($link1);

        return $box;
    }

}
