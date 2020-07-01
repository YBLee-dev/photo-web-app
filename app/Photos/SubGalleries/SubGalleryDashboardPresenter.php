<?php

namespace App\Photos\SubGalleries;


use App\Photos\Galleries\Gallery;
use App\Photos\Galleries\GalleryDashboardPresenter;
use App\Photos\Photos\Photo;
use App\Processing\ProcessingStatusesEnum;
use App\Utils;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Laracasts\Presenter\Exceptions\PresenterException;
use Webmagic\Dashboard\Components\FormGenerator;
use Webmagic\Dashboard\Components\FormPageGenerator;
use Webmagic\Dashboard\Components\TableGenerator;
use Webmagic\Dashboard\Components\TilesListGenerator;
use Webmagic\Dashboard\Components\TilesListPageGenerator;
use Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable;
use Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined;
use Webmagic\Dashboard\Dashboard;
use Webmagic\Dashboard\Elements\Boxes\Box;
use Webmagic\Dashboard\Elements\Forms\Elements\NumberInput;
use Webmagic\Dashboard\Elements\Forms\Elements\Switcher;
use Webmagic\Dashboard\Elements\Links\Link;
use Webmagic\Dashboard\Elements\Links\LinkButton;

class SubGalleryDashboardPresenter
{
    /**
     * Table page
     *
     * @param $gallery
     * @param $subgalleries
     * @param array $classrooms
     * @param Request $request
     * @return TableGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     */
    public function getTable(Gallery $gallery, $subgalleries, array $classrooms, Request $request, $sort, $sortBy)
    {
        $params = Utils::prepareRequestParamsString('classrooms', 'staff', 'sort', 'sortBy');

        $sorting = [$sortBy => $sort];
        $subgalleryTable = (new TableGenerator())
            ->items($subgalleries)
            ->withPagination($subgalleries, route('dashboard::gallery.subgallery.filter',$gallery->id).$params)
            ->tableTitles('ID', '', 'Classroom', 'Sub gallery name', 'Person name', 'Password', 'Class photo', 'General photo', 'All class photos', 'Position')
            ->showOnly('id', 'preview_image', 'classroom', 'name', 'person_name', 'password', 'available_on_class_photo', 'available_on_general_photo', 'all_class_photos', 'position', 'title')
            ->addTitleWithSorting('Title', 'title', data_get($sorting, 'title', ''), true, route('dashboard::gallery.subgallery.filter',$gallery->id).$params)
            ->setConfig([
                'preview_image' => function (SubGallery $subgallery) {
                    if($subgallery->isSubGalleryPhotoGenerationInProgress()){
                        return ' <div class="text-center"><i class="fa fa-spinner fa-spin text-muted"></i></div>';
                    }
                    return "<img class='js-lazy' data-src='".$subgallery->present()->mainPhotoPreviewUrl()."' src='".asset('webmagic/dashboard/img/default-image-png.png')."' width='100'>";
                },
                'classroom' => function (SubGallery $subgallery) {
                    return(new LinkButton())->content( $subgallery->person ? $subgallery->person->classroom : '')
                        ->class("t-item-{$subgallery->id}")
                        ->js()->tooltip()->regular('Edit meta data')
                        ->js()->openInModalOnClick()
                        ->regular(route('dashboard::gallery.subgallery.client.edit', $subgallery->person->id), 'GET', 'Client editing', true)
                        ->dataAttr('reload-after-close-modal', 'true');
                },
                'name' => function (SubGallery $subgallery) {
                    return (new Link())->content($subgallery->name)->link(route('dashboard::gallery.subgallery.show', $subgallery->id));
                },
                'person_name' => function (SubGallery $subgallery) {
                    return $subgallery->person->present()->name();
                },
                'title' => function (SubGallery $subgallery) {
                    return $subgallery->person->title;
                },
                'available_on_class_photo' => function (SubGallery $subgallery) {
                    return (new Switcher())->checked((bool)$subgallery['available_on_class_photo'])
                        ->classes('js_ajax-by-change')->name('available_on_class_photo')
                        ->attr('subgallery_id', $subgallery['id'])
                        ->attr('data-action', route('dashboard::gallery.subgallery.change-availability-group-photo', $subgallery['id']))
                        ->attr('data-method', 'PUT');
                },
                'available_on_general_photo' => function (SubGallery $subgallery) {
                    return (new Switcher())->checked((bool)$subgallery['available_on_general_photo'])
                        ->classes('js_ajax-by-change')->name('available_on_general_photo')
                        ->attr('subgallery_id', $subgallery['id'])
                        ->attr('data-action', route('dashboard::gallery.subgallery.change-availability-group-photo', $subgallery['id']))
                        ->attr('data-method', 'PUT');
                },
                 'all_class_photos' => function (SubGallery $subgallery) {
                      if($subgallery->person->teacher){
                          return (new Switcher())->checked((bool)$subgallery->person->all_class_photos)
                              ->classes('js_ajax-by-change')->name('all_class_photos')
                              ->attr('data-action', route('dashboard::gallery.subgallery.change-all-class-photos', $subgallery->person->id))
                              ->attr('data-method', 'PUT');
                      }
                },
                'position' => function (SubGallery $subgallery) {
                    if($subgallery->person->teacher){
                        return (new NumberInput())->value($subgallery->person->position)
                            ->step(1)
                            ->addClass('js_ajax-by-change wfix-50')->name('position')
                            ->attr('data-action', route('dashboard::gallery.subgallery.update-position', $subgallery->person->id))
                            ->attr('data-method', 'PUT');
                    }
                }
            ])
            ->setShowLinkClosure(function (SubGallery $subGallery) {
                return route('dashboard::gallery.subgallery.show', $subGallery->id);
            })
            ->addElementsToToolsCollection(function (SubGallery $subGallery) {
                return (new LinkButton())
                    ->icon('fa-location-arrow')
                    ->class('btn-light')
                    ->js()->openInModalOnClick()
                    ->smallModal(route('dashboard::gallery.subgallery.move', $subGallery->id), 'GET', 'Move sub-gallery', true);
            })
            ->addElementsToToolsCollection(function (SubGallery $subGallery) {
                $btn = (new LinkButton())
                    ->icon('fa-trash')
                    ->dataAttr('item', ".js_item_$subGallery->id")
                    ->class('text-red')
                    ->dataAttr('request', route('dashboard::gallery.subgallery.delete', $subGallery->id))
                    ->dataAttr('method', 'DELETE');

                if($subGallery->orders->count()){
                    $btn->addClass('disabled');
                    $btn = '<span title="This sub gallery has connected orders">'.$btn->render().'</span>';
                } elseif ($subGallery->carts->count()){
                    $btn->addClass('disabled');
                    $btn = '<span title="This sub gallery has connected carts">'.$btn->render().'</span>';
                } else {
                    $btn->addClass('js_delete')->dataAttr('title', 'Delete');
                }
                return $btn;
            })

        ;

        $subgalleryTable->addFiltering()
            ->action(route('dashboard::gallery.subgallery.filter', [$gallery->id]))
            ->selectJS('classrooms[]', $classrooms, ['classrooms[]' => $request->get('classrooms')], 'Classroom', false, true)
            ->checkbox('staff', $request->get('staff'), 'Staff only');

        $subgalleryTable->getFilter()->addElement()->button('Filter')->type('submit');


        return $subgalleryTable;
    }

    /**
     * Description List
     *
     * @param SubGallery $subgallery
     * @param Dashboard  $dashboard
     *
     * @return Dashboard
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     */
    public function getDescriptionList(SubGallery $subgallery, Dashboard $dashboard)
    {
        $page = $dashboard->page();
        $page->setPageTitle($subgallery->name, 'sub gallery view')
            ->addElement()->grid()
            ->lgRowCount(2)
            ->mdRowCount(2)
            ->smRowCount(1)
            ->xsRowCount(1)
            ->addElement()
            ->box()->boxTitle('Gallery details')->footerAvailable(false)
            ->addToolsLinkButton(route('subgallery', $subgallery->password), '<i class="fa fa-eye "></i> View on site')
            ->addElement('box_tools')->linkButton()
                ->content('Add Photo')
                ->icon('fa-edit')->class('btn-default')
                ->js()->tooltip()->regular('Add Photo')
                ->js()->openInModalOnClick()->regular(route('dashboard::gallery.subgallery.add.photo', $subgallery->id), 'GET', 'Add photo to sub gallery', true)
                ->dataAttr('reload-after-close-modal', 'true')->parent()
            ->element()->descriptionList(
                ['data' => [
                    'Sub-gallery name:' => $subgallery->name,
                    'Gallery name:' => (new Link())->content($subgallery->gallery->present()->name)->link(route('dashboard::gallery.show', $subgallery->gallery)),
                    'Password:' => $subgallery->password,
                    'Present on class photo' => (new Switcher())->checked((bool)$subgallery['available_on_class_photo'])
                        ->classes('js_ajax-by-change')->name('available_on_class_photo')
                        ->attr('subgallery_id', $subgallery['id'])
                        ->attr('data-action', route('dashboard::gallery.subgallery.change-availability-group-photo', $subgallery['id']))
                        ->attr('data-method', 'PUT'),

                    'Present on general photo' => (new Switcher())->checked((bool)$subgallery['available_on_general_photo'])
                        ->classes('js_ajax-by-change')->name('available_on_general_photo')
                        ->attr('subgallery_id', $subgallery['id'])
                        ->attr('data-action', route('dashboard::gallery.subgallery.change-availability-group-photo', $subgallery['id']))
                        ->attr('data-method', 'PUT'),
                    $subgallery->person->teacher ? 'All class photos' : '' =>
                        $subgallery->person->teacher ? (new Switcher())->checked((bool) $subgallery->person->all_class_photos)
                            ->classes('js_ajax-by-change')
                            ->name('all_class_photos')
                            ->attr('subgallery_id', $subgallery['id'])
                            ->attr('data-action', route('dashboard::gallery.subgallery.change-all-class-photos', $subgallery->person->id))
                            ->attr('data-method', 'PUT') : '',
                    'Viewed:' => $subgallery->customers->pluck('email')->implode(', ') ?: 'No',
                    'Price list' => $subgallery->person->teacher ?
                        (new Link())->content($subgallery->gallery->staffPriceList->name)->link(route('dashboard::price-lists.show', $subgallery->gallery->staff_price_list_id)) :
                        (new Link())->content($subgallery->gallery->priceList->name)->link(route('dashboard::price-lists.show', $subgallery->gallery->price_list_id))
                ],
                ])->isHorizontal(true)
            ->parent('grid')
            ->addElement()->box()->boxTitle('Person data')->footerAvailable(false)
            ->addElement('box_tools')->linkButton()
                    ->content('Edit')
                    ->icon('fa-edit')->class('btn-default')
                    ->js()->tooltip()->regular('Edit meta data')
                    ->js()->openInModalOnClick()->regular(route('dashboard::gallery.subgallery.client.edit', $subgallery->person->id), 'GET', 'Client editing', true)
                    ->dataAttr('reload-after-close-modal', 'true')
            ->parent('box')
            ->addElement()->descriptionList(['data' => [
                'First name:' => $subgallery->person->first_name ?? '',
                'Second name:' => $subgallery->person->last_name ?? '',
                'Main Classroom:' => $subgallery->person->classroom ?? '',
                'Additional Classrooms:' => $subgallery->person->present()->additionalClassroomsAsString() ?? '',
                'School:' => $subgallery->person->school_name ?? '',
                'Graduate:' => $subgallery->person ? $subgallery->person->graduate ? 'Yes' : 'No' : '',
                'Staff:' => $subgallery->person ? $subgallery->person->teacher ? 'Yes' : 'No' : '',
                $subgallery->person ? $subgallery->person->teacher ? 'Title:' : '' : '' => $subgallery->person->title ?? ''

            ]])->isHorizontal(true)


            ->parent('page');

        return $dashboard;
    }

    /**
     * Get images components
     *
     * @param SubGallery $subGallery
     * @param Dashboard  $dashboard
     *
     * @return Dashboard
     * @throws NoOneFieldsWereDefined
     * @throws PresenterException
     */
    public function getImageComponents(SubGallery $subGallery,  Dashboard $dashboard)
    {
        $dashboard->addElement()->h4Title('Photos');

        foreach ($subGallery->photos as $photo){
            $component = $dashboard->addElement()
                ->imageComponent()
                ->addClass('col-md-2')
                ->imgUrl($photo->present()->previewUrl())
                ->downloadUrl($photo->present()->originalUrl())
                ->title($subGallery->name)
                ->fileName(basename($photo->present()->previewUrl()));

                if($photo->canBeDeleted()) {
                    $component
                        ->addClass('js_delete')
                        ->deleteAction(route('dashboard::gallery.subgallery.delete.photo', $photo->id))
                        ->deleteMethod('GET');
                } else {
                    $component->addClass('delete-btn-disable');
                }
        }

        $dashboard->page()->addElement()->h4Title('Other photos')->addClass('col-md-12');

        $tilesGrid = $dashboard->page()->addElement()->grid();

        // Cropped photo
        $tilesGrid->addContent($this->getCroppingPhotoComponent($subGallery), 'elements');

        $proofPhoto = $subGallery->person->present()->proofPhotoUrl();
        $tilesGrid->addElement()
            ->box()->footerAvailable(false)
            ->boxTitle('Proof photo')
            ->addElement()
            ->imagePreview()
            ->imgUrl($proofPhoto)
            ->downloadUrl($proofPhoto)
            ->fileName(basename($proofPhoto));

        // Personal class photo
        $personalClassPhotoUrl = $subGallery->person->present()->classPersonalPhotoUrl();
        $tilesGrid->addElement()
            ->box()->footerAvailable(false)
            ->boxTitle('Personal class photo')
            ->addElement()
            ->imagePreview()
            ->imgUrl($personalClassPhotoUrl)
            ->downloadUrl($personalClassPhotoUrl)
            ->fileName(basename($personalClassPhotoUrl));

        // School photo
        $schoolPhotoUrl = $subGallery->gallery->present()->schoolPhotoUrl();
        $tilesGrid->addElement()
            ->box()->footerAvailable(false)
            ->boxTitle('School photo')
            ->addElement()
            ->imagePreview()
            ->imgUrl($schoolPhotoUrl)
            ->downloadUrl($schoolPhotoUrl)
            ->fileName(basename($schoolPhotoUrl));

        if($subGallery->person->isStaff()){
            $iDCardPortraitUrl = $subGallery->person->present()->iDCardPortraitUrl();
            $tilesGrid->addElement()
                ->box()->footerAvailable(false)
                ->boxTitle('ID Card portrait')
                ->addElement()
                ->imagePreview()
                ->imgUrl($iDCardPortraitUrl)
                ->downloadUrl($iDCardPortraitUrl)
                ->fileName(basename($iDCardPortraitUrl));

            $iDCardLandscapeUrl = $subGallery->person->present()->iDCardLandscapeUrl();
            $dashboard->addElement()
                ->imageComponent()
                ->title('ID Card landscape')
                ->addClass('col-md-2')
                ->imgUrl($iDCardLandscapeUrl)
                ->downloadUrl($iDCardLandscapeUrl)
                ->fileName(basename($iDCardLandscapeUrl))
                ->deleteMethod('GET');
        }

        return $dashboard;
    }

    /**
     * Form
     *
     * @param SubGallery $subGallery
     * @param array      $galleries
     *
     * @return FormGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     */
    public function getFormForMoveSubgallery(SubGallery $subGallery, array $galleries)
    {
        $form = (new FormGenerator())
            ->action(route('dashboard::gallery.subgallery.move', $subGallery))
            ->method('POST')
            ->select('gallery_id', $galleries, 1, 'Choose gallery', true)
            ->submitButton('Move')
            ->ajax(true);
        ;

        return $form;
    }

    /**
     * @param SubGallery $subGallery
     *
     * @param Photo      $photo
     *
     * @return FormGenerator
     * @throws PresenterException
     */
    public function getFormForUpdateCroppedInfo(SubGallery $subGallery)
    {
        $originalImageUrl = $subGallery->mainPhoto()->present()->originalUrl();
        $croppedFaceWidth = config('project.cropped_face_size.width');
        $croppedFaceHeight = config('project.cropped_face_size.height');
        $croppingTopIndent = config('project.cropped_face_size.top_indent');
        $croppingBottomIndent = config('project.cropped_face_size.bottom_indent');

        $croppedPhoto = $subGallery->person->croppedPhoto();
        $cropX = $croppedPhoto->crop_x;
        $cropY = $croppedPhoto->crop_y;
        $cropWidth = $croppedPhoto->crop_original_width ? : $croppedFaceWidth;
        $cropHeight = $croppedPhoto->crop_original_height ? : $croppedFaceHeight;

        return view('dashboard._form-cropping', compact(
            'subGallery',
            'originalImageUrl',
            'croppedFaceHeight',
            'croppedFaceWidth',
            'croppingTopIndent',
            'croppingBottomIndent',
            'cropX',
            'cropY',
            'cropWidth',
            'cropHeight'
        ));
    }

    /**
     * Form for edit client
     *
     * @param Model $client
     *
     * @return FormGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getFormForEditClient(Model $client, array $classrooms)
    {
        $formGenerator = (new FormGenerator())
            ->action(route('dashboard::gallery.subgallery.client.update', $client))
            ->ajax(true)
            ->method('PUT')
            ->textInput('first_name', $client, 'First name', true)
            ->textInput('last_name', $client, 'Second name')
            ->selectJS('classroom', $classrooms, $client->classroom, 'Classroom', false, false)
            //->textInput('classroom', $client, 'Classroom')
            ->textInput('school_name', $client, 'School')
            ->checkbox('graduate', $client->graduate ? true : false, 'Graduate')
            ->checkbox('teacher', $client->teacher ? true : false, 'Staff')
            ->textInput('title', $client, 'Title (for staff only)')
            ->submitButton('Update');

        return $formGenerator;
    }

    /**
     * Get Tiles fir cropped photos tab
     *
     * @param Gallery $gallery
     * @param array $items
     * @param array $classrooms
     * @param Request $request
     *
     * @return TilesListGenerator|TilesListPageGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     * @throws \Exception
     */
    public function getCroppedPhotosTiles($subGalleries, array $classrooms, Request $request, int $galleryId)
    {
        $classroomsParam = Utils::prepareRequestParamsString('classrooms');
        $staffParam = Utils::prepareRequestParamsString('staff');

        $subGalleryDashboardPresenter = $this;
        $tilesListPage = (new TilesListGenerator())
            ->setItems($subGalleries->items())
            ->withPagination($subGalleries, route('dashboard::gallery.subgallery.filter',$galleryId).'?croppedPhotos=1'.$classroomsParam.$staffParam)
            ->setOnly('subGallery')
            ->addToConfig('subGallery', function($item){
                return $item;
            })
            ->setItemRenderingClosure(function ($item) use ($subGalleryDashboardPresenter) {
                if($item['subGallery'] instanceof SubGallery){
                    return $subGalleryDashboardPresenter->getCroppingPhotoComponent($item['subGallery']);
                }
            })
        ;
        $tilesListPage->addFiltering()
            ->action(route('dashboard::gallery.subgallery.filter', [$galleryId, 'tiles']))
            ->selectJS('classrooms[]', $classrooms, ['classrooms[]' => $request->get('classrooms')], 'Classroom', false, true)
            ->checkbox('staff', $request->get('staff'), 'Staff only')
            ->hiddenInput('croppedPhotos', '1');

        $tilesListPage->getFilter()->addElement()->button('Filter')->type('submit');
        $tilesListPage->getFilter()->content()->getItem(0)->content()->attr('style', 'width:135px;');

        $tilesListPage->getGrid()
            ->lgRowCount(4)
            ->mdRowCount(2)
            ->smRowCount(1)
            ->xsRowCount(1)->render();

        return $tilesListPage;
    }

    /**
     * Get Tiles fir proof photos tab
     *
     * @param $subGalleries
     * @param array $classrooms
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Model $gallery
     * @return \Webmagic\Dashboard\Components\TilesListGenerator|\Webmagic\Dashboard\Components\TilesListPageGenerator|\Webmagic\Dashboard\Elements\Boxes\Box
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     * @throws \Exception
     */
    public function getProofPhotosTiles($subGalleries, array $classrooms, Request $request, Gallery $gallery, bool $groupPhotoInProgress = false)
    {
        if ($groupPhotoInProgress) {
            return (new GalleryDashboardPresenter())->getGroupPhotosConvertingStatuses($gallery);
        }

        if(!$gallery->isGroupPhotosWasGenerated()){
            return (new Box())->addClass('text-danger')
                ->boxTitle('Proof photos are not generated yet. Please, generate gallery group photos');
        }

        $classroomsParam = Utils::prepareRequestParamsString('classrooms');
        $staffParam = Utils::prepareRequestParamsString('staff');

        $subGalleryDashboardPresenter = $this;
        $tilesListPage = (new TilesListGenerator())
            ->setItems($subGalleries->items())
            ->withPagination($subGalleries, route('dashboard::gallery.subgallery.filter',$gallery->id).'?proofPhotos=1'.$classroomsParam.$staffParam)
            ->setOnly('subGallery')
            ->addToConfig('subGallery', function($item){
                return $item;
            })
            ->setItemRenderingClosure(function ($item) use ($subGalleryDashboardPresenter) {
                if($item['subGallery'] instanceof SubGallery){
                    if ($item['subGallery']->isProofPhotosUpdatingInProgress()) {
                       return $this->proofPhotoUpdatingProcessingBox($item['subGallery']);
                    }
                    return $subGalleryDashboardPresenter->getProofPhotoComponent($item['subGallery']);
                }
            })
        ;
        $tilesListPage->addFiltering()
            ->action(route('dashboard::gallery.subgallery.filter', [$gallery->id, 'tiles']))
            ->selectJS('classrooms[]', $classrooms, ['classrooms[]' => $request->get('classrooms')], 'Classroom', false, true)
            ->checkbox('staff', $request->get('staff'), 'Staff only')
            ->hiddenInput('proofPhotos', '1');

        $tilesListPage->getFilter()->addElement()->button('Filter')->type('submit');
        $tilesListPage->getFilter()->content()->getItem(0)->content()->attr('style', 'width:135px;');

        $tilesListPage->getGrid()
            ->lgRowCount(4)
            ->mdRowCount(2)
            ->smRowCount(1)
            ->xsRowCount(1)->render();

        return $tilesListPage;
    }

    /**
     * @param SubGallery $subGallery
     *
     * @return mixed
     * @throws NoOneFieldsWereDefined
     * @throws PresenterException
     */
    public function getCroppingPhotoComponent(SubGallery $subGallery)
    {
        $box = (new Box())->addElement()->imagePreview()
            ->imgUrl($subGallery->person->present()->croppedPhotoUrl())
            ->fileName($subGallery->person->present()->name)
            ->addSize($subGallery->person->classroom);

//                $box = $box->parent()->addElement('box_tools')
//                    ->linkButton()
//                    ->icon('fa-trash')
//                    ->class('btn-danger btn-xs js_delete')
//                    ->content('Delete')
//                    ->dataAttr('request', route('dashboard::gallery.subgallery.delete-cropped-photo', $item['id']))
//                    ->dataAttr('method', 'DELETE');


        $box = $box->parent()->addElement('box_tools')
            ->linkButton()
            ->icon('fa-pencil-square-o')
            ->class("btn-primary btn-xs item-{$subGallery->id}")
            ->content('Edit')
            ->js()->tooltip()->regular('Edit')
            ->js()->openInModalOnClick()
            ->regular(route('dashboard::gallery.subgallery.change-cropped-info', $subGallery), 'GET', 'Editing')
            ->dataAttr('replace-blk', ".item-{$subGallery->id}");

        return $box->parent();
    }

    public function getProofPhotoComponent(SubGallery $subGallery)
    {
        $box = (new Box())->addClass("proof-item-{$subGallery->id}")->addElement()->imagePreview()
            ->imgUrl($subGallery->person->present()->proofPhotoUrl());

        $box = $box->parent()->addElement('box_tools')
            ->linkButton()
            ->icon('fa-pencil-square-o')
            ->class("btn-primary btn-xs")
            ->content('Edit')
            ->js()->tooltip()->regular('Edit')
            ->js()->openInModalOnClick()
            ->regular(route('dashboard::gallery.subgallery.change-proof-photo', $subGallery), 'GET', 'Editing')
            ->dataAttr('replace-blk', ".proof-item-{$subGallery->id}");

        return $box->parent();
    }

    /**
     * @param \App\Photos\SubGalleries\SubGallery $subGallery
     * @return mixed
     */
    public function proofPhotoUpdatingProcessingBox(SubGallery $subGallery)
    {
        $route = route('dashboard::gallery.subgallery.check-proof-photo-status', $subGallery->id);
        $spinner = '<div class="overlay js-update"
            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
            data-url="'.$route.'" data-class=".proof-item-'.$subGallery->id.'" data-timeout="3000" data-replace="1">
            <i class="fa fa-spinner fa-spin text-muted"></i>
            </div>';

        return $this->getProofPhotoComponent($subGallery)->addContent($spinner);
    }


    /**
     * Create form for sub gallery & person
     *
     * @param Model $gallery
     * @return FormPageGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     */
    public function getFormForCreateSubGallery(Model $gallery)
    {
        $formPageGenerator = (new FormPageGenerator())
            ->title('Create Sub gallery & person')
            ->action(route('dashboard::gallery.subgallery.store', $gallery['id']))
            ->ajax(true)
            ->method('POST');

        $formPageGenerator->getBox()
            ->addElement()->grid()
            ->lgRowCount(3)
            ->mdRowCount(3)
            ->smRowCount(1)
            ->xsRowCount(1)
            ->addElement('elements')->imageInput([
                'name' => 'image',
                'title' => 'Person Image'
            ]);

        $classrooms = $gallery->getClassroomsList(true);;
            // Person form data
        $formPageGenerator
            ->textInput('first_name', '', 'Person First name', true)
            ->textInput('last_name', '', 'Person Second name', true)
            ->selectJS('classroom', $classrooms, '', 'Person Classroom', true, false,
                ['class' => 'form-control js-select2-dynamic-options'] )
            //->checkbox('graduate', false, 'Graduate')
            ->checkbox('teacher', false, 'Staff')
            ->textInput('title', '', 'Title (for teacher only)')

            // Sub gallery form data
            ->checkbox('available_on_class_photo', true, 'Class photo')
            ->checkbox('available_on_general_photo', true, 'General photo')
            ->submitButtonTitle('Create')
        ;

        return $formPageGenerator;
    }

    /**
     * Form for photo on sub gallery
     *
     * @param Model $subGallery
     * @return FormPageGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     */
    public function getFormForAddPhoto(Model $subGallery)
    {
        $formGenerator = (new FormGenerator())
            ->action(route('dashboard::gallery.subgallery.store.photo', $subGallery['id']))
            ->ajax(true)
            ->method('POST')
            ->imageInput('photo', '', 'Photo')
            ->submitButton('Add')
        ;

        return $formGenerator;
    }

    public function getFormForUpdateProofPhoto(Model $subGallery, $items)
    {
        $table = (new TableGenerator())
            ->showOnly('image', 'status')
            ->setConfig([
                'image' => function ($item) {
                    $url =  $item['image'];
                    $id = 'js_proof_image_'.$item['id'];
                    return "<img src='$url' width='100' id='$id'>";
                },
                'status' => function ($item) use ($subGallery, $items) {
                    //if(count($items) == 1 || ($subGallery->person->proofPhoto()->id == $item['id'])){
                    //    return '<i class="fa fa-check-circle text-success"></i>';
                    //}
                    $form =  (new FormGenerator())
                        ->action(route('dashboard::gallery.subgallery.update-proof-photo', $subGallery->id))
                        ->method('POST')
                        ->input('photo_id',  $item['id'], '', '', false, '', [], 'hidden')
                        ->addSubmitButton(['data-modal-hide' => 'true'], 'Choose');

                    return $form;
                }
            ])
            ->items($items)
            ->toolsInModal(true);

        return $table;
    }

    /**
     * @param \App\Photos\SubGalleries\SubGallery $subGallery
     * @return mixed
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getPhotoUpdatingStatuses(SubGallery $subGallery)
    {
        $data = $subGallery->present()->currentStatuses();

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

        $box = (new Box())->addClass($uniqClass)
            ->boxTitle('Sub gallery not ready yet. Please, wait before photos will be processed')
            ->addContent($table)
            ->js()
            ->contentAutoUpdate()->apply($uniqClass, route('dashboard::gallery.subgallery.get-photo-converting-statuses', $subGallery->id), 'GET', '3000', true);

        return $box;
    }
}
