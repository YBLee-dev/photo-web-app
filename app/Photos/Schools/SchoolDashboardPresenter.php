<?php

namespace App\Photos\Schools;

use App\Photos\SettingsGroupPhotos\SettingsGroupPhotosService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Webmagic\Dashboard\Components\FormGenerator;
use Webmagic\Dashboard\Components\TablePageGenerator;
use Webmagic\Dashboard\Dashboard;
use Webmagic\Dashboard\Elements\Links\Link;
use Webmagic\Dashboard\Elements\Links\LinkButton;

class SchoolDashboardPresenter
{
    /**
     * Table Page
     *
     * @param $schools
     * @param \Webmagic\Dashboard\Dashboard $dashboard
     * @param \Illuminate\Http\Request $request
     * @return \Webmagic\Dashboard\Dashboard
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getTable($schools, Dashboard $dashboard, Request $request)
    {
        (new TablePageGenerator($dashboard->page()))
            ->title('Schools')
            ->tableTitles('ID', 'Name', 'Seasons Count', 'Galleries Count', '')
            ->showOnly('id', 'name', 'seasons', 'galleries')
            ->setConfig([
                'name' => function (School $school) {
                    return (new Link())->content($school->name)
                        ->link(route('dashboard::schools.show', $school->id));
                },
                'seasons' => function (School $school) {
                    return $school->seasons->count();
                },
                'galleries' => function (School $school) {
                    return $school->galleries->count();
                },
            ])
            ->withPagination($schools, route('dashboard::schools.index', $request->all()) )
            ->createLink(route('dashboard::schools.create'))
            ->setShowLinkClosure(function (School $school) {
                return route('dashboard::schools.show', $school);
            })
            ->addElementsToToolsCollection(function (School $school) {
                $btn = (new LinkButton())
                    ->icon('fa-pencil-square-o')
                    ->class('')
                    ->js()->tooltip()->regular('Edit');
                    $btn->js()->openInModalOnClick()
                        ->regular( route('dashboard::schools.edit', $school), 'GET', 'Editing')
                        ->dataAttr('reload-after-close-modal', 'true');

               return $btn;
            })
            ->addElementsToToolsCollection(function (School $school) {
                $btn = (new LinkButton())
                    ->icon('fa-trash')
                    ->dataAttr('item', ".js_item_$school->id")
                    ->class('text-red')
                    ->dataAttr('request', route('dashboard::schools.destroy', $school))
                    ->dataAttr('method', 'DELETE');

                if($school->seasons->count()){
                    $btn->addClass('disabled');
                    $btn = '<span title="This school has connected seasons">'.$btn->render().'</span>';
                } else {
                    $btn->addClass('js_delete')->dataAttr('title', 'Delete');;
                }
                return $btn;
            })
            ->items($schools)
            ->toolsInModal('true');

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
        $groupPhotosService = new SettingsGroupPhotosService();
        $settings = $groupPhotosService->getDefaultSettings();

        $formGenerator = (new FormGenerator())
            ->action(route('dashboard::schools.store'))
            ->ajax(true)
            ->method('POST')
            ->textInput('name', false, 'Name', true);

        $formGenerator->imageInput('school_logo', $settings->present()->schoolLogoUrl(), 'Default school logo');

        return $formGenerator->submitButton('Create school');
    }

    /**
     * Edit Form Popup
     *
     * @param \Illuminate\Database\Eloquent\Model $school
     * @return \Webmagic\Dashboard\Components\FormGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getEditForm(Model $school)
    {
        $groupPhotosService = new SettingsGroupPhotosService();
        $settings = $groupPhotosService->getDefaultSettings();

        $formGenerator = (new FormGenerator())
            ->action(route('dashboard::schools.update', $school))
            ->ajax(true)
            ->method('PUT')
            ->textInput('name', $school, 'Name', true);

        $formGenerator->imageInput('school_logo', $school->school_logo ? $school->present()->schoolLogoUrl(): $settings->present()->schoolLogoUrl(), 'Default school logo');

        return $formGenerator->submitButton('Update school');
    }

    /**
     * Return page with edit js button
     *
     * @param \Illuminate\Database\Eloquent\Model $school
     * @param \Webmagic\Dashboard\Dashboard $dashboard
     * @return \Webmagic\Dashboard\Dashboard
     */
    public function addEditBtnOnPage(Model $school, Dashboard $dashboard)
    {
        $dashboard->page()->addElement()
            ->linkButton()
            ->content('Edit')
            ->icon('fa-pencil-square-o')
            ->class('btn-default margin pull-right')
            ->js()->tooltip()->regular('Edit')
            ->js()->openInModalOnClick()
            ->regular(route('dashboard::schools.edit', $school), 'GET', 'Editing')
            ->dataAttr('reload-after-close-modal', 'true')
        ;

        $dashboard->page()->setPageTitle($school->name);

        return $dashboard;
    }
}
