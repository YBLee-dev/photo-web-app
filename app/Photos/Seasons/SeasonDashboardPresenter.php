<?php

namespace App\Photos\Seasons;

use App\Photos\Schools\School;
use App\Photos\Schools\SchoolRepo;
use App\Photos\SettingsGroupPhotos\SettingsGroupPhotos;
use App\Photos\SettingsGroupPhotos\SettingsGroupPhotosService;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\Exceptions\PresenterException;
use Webmagic\Dashboard\Components\FormGenerator;
use Webmagic\Dashboard\Components\FormPageGenerator;
use Webmagic\Dashboard\Components\TableGenerator;
use Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable;
use Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined;
use Webmagic\Dashboard\Dashboard;
use Webmagic\Dashboard\Elements\Boxes\Box;
use Webmagic\Dashboard\Elements\Forms\Form;
use Webmagic\Dashboard\Elements\Links\Link;
use Webmagic\Dashboard\Elements\Links\LinkButton;
use Webmagic\Dashboard\Pages\BasePage;
use Webmagic\Dashboard\Pages\Page;

class SeasonDashboardPresenter
{
    /**
     * Get Table Page
     *
     * @param $seasons
     * @param Model $school
     * @param Dashboard $dashboard
     *
     * @return Dashboard
     * @throws NoOneFieldsWereDefined
     */
    public function getTablePage($seasons, School $school, Dashboard $dashboard)
    {
        $seasonsTable = $this->getTable($seasons, $school);

        $addSeasonBtn = (new LinkButton())
            ->icon('fa fa-plus')
            ->content('Add')
            ->class('pull-right btn btn-flat btn-default')
            ->link(route('dashboard::schools.seasons.create', $school))
            ->js()->tooltip()->regular('Create season for school');

        $dashboard->page()
            ->setPageTitle($school->name, 'Seasons')
            ->addElement()
            ->box($seasonsTable)
            ->boxHeaderContent($addSeasonBtn);

        return $dashboard;
    }

    /**
     * Get Table with pagination
     *
     * @param $seasons
     * @param Model $school
     *
     * @return TableGenerator
     */
    public function getTable($seasons, Model $school)
    {
        $seasonsTable = (new TableGenerator())
            ->tableTitles('ID', 'Name', 'Year',  'Sub-galleries count')
            ->showOnly('id', 'name', 'year', 'galleries')
            ->setConfig([
                'name' => function (Season $season) {
                    if($season->gallery){
                        return (new Link())->content($season->name)->link(route('dashboard::gallery.show', $season->gallery->id));
                    }
                    return $season->name;
                },
                'year' => function (Season $season){
                    return $season->groupSettings->year;
                },
                'galleries' => function (Season $season) {
                    return $season->gallery ? $season->gallery->subgalleries->count() : 0;
                },
            ])
            ->items($seasons)
            ->withPagination($seasons, route('dashboard::schools.seasons.index', $school))
            ->setEditLinkClosure(function (Season $season) use ($school) {
                return route('dashboard::schools.seasons.edit', [$school, $season]);
            })
            ->addElementsToToolsCollection(function (Season $season) {
                $btn = ((new LinkButton())
                    ->icon('fa-eye')
                    ->class('')
                    ->link(route('dashboard::gallery.show', $season->gallery->id ?? ''))
                    ->js()->tooltip()->regular('View gallery')
                );
                if (! $season->gallery) {
                    $btn->addClass('disabled');
                }
                return $btn;
            })
            ->addElementsToToolsCollection(function (Season $season) use ($school) {
                $btn = (new LinkButton())
                    ->icon('fa-trash')
                    ->dataAttr('item', ".js_item_$season->id")
                    ->class('text-red')
                    ->dataAttr('request', route('dashboard::schools.seasons.destroy', [$school, $season]))
                    ->dataAttr('method', 'DELETE');

                if($season->gallery){
                    $btn->addClass('disabled');
                    $btn = '<span title="This season has connected galleries">'.$btn->render().'</span>';
                } else {
                    $btn->addClass('js_delete')->dataAttr('title', 'Delete');;
                }
                return $btn;
            })
            ->toolsInModal(false);

        return $seasonsTable;
    }

    /**
     * Create Form Popup
     *
     * @param \App\Photos\Schools\School $school
     * @return FormGenerator
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getCreateForm(School $school)
    {
        $formPageGenerator = (new FormPageGenerator())
            ->title($school->name, 'creating season')
            ->action(route('dashboard::schools.seasons.store', $school->id))
            ->ajax(true)
            ->method('POST');

        $formPageGenerator = $this->prepareFormBody($formPageGenerator, $school->id);

        $formPageGenerator->submitButtonTitle('Create season');

        return $formPageGenerator;
    }

    /**
     * Prepare form body
     *
     * @param FormPageGenerator $formPageGenerator
     * @param int               $schoolId
     * @param Season|null       $season
     *
     * @return FormPageGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws PresenterException
     */
    protected function prepareFormBody(FormPageGenerator $formPageGenerator, int $schoolId, Season $season = null)
    {
        $groupPhotosService = new SettingsGroupPhotosService();
        $settings = is_null($season) ? $groupPhotosService->getDefaultSettings() : $season->groupSettings;
        $naming_structure = $groupPhotosService->getAvailableNamingStructures();
        $fonts = $groupPhotosService->getAvailableFonts();
        $season = $season ?? new Season(['name' => null, 'school_id' => $schoolId]);

        $formPageGenerator
            ->textInput('name', $season->name, 'Season Name', true)
            ->textInput('year', $settings->year ?: now()->format('Y'), 'Year', true);

        $radioButtons = '
            <input name="logo" type="radio" id="checkbox-id-1" class="radio" value="school_logo" '. (!$season->id || $settings->use_school_logo ? 'checked' : '' ) .'>
            <label for="checkbox-id-1" class="radio-lbl" 
                title="There will be a school logo on the group photos. You can load it on the school editing page">Use school logo</label>            
            <input name="logo" type="radio" id="checkbox-id-3" class="radio" value="without_logo" '. (!$settings->use_school_logo ?'checked':'' ) .'>
            <label for="checkbox-id-3" class="radio-lbl ml-2"
                title="Group photos will be generated without a logo">Without logo</label>';

        $formPageGenerator->getBox()->addElement()->formGroup()->content($radioButtons);
        $formPageGenerator->getBox()
            ->addElement()->grid()
            ->lgRowCount(3)
            ->mdRowCount(3)
            ->smRowCount(1)
            ->xsRowCount(1)
            ->addElement('elements')->imageInput([
                'name' => 'school_background',
                'img_url' => $settings->present()->schoolBackground(),
                'title' => 'School Background Image'
            ])
            ->parent('grid')->addElement('elements')->imageInput([
                'name' => 'class_background',
                'img_url' => $settings->present()->classBackground(),
                'title' => 'Class Background Image'
            ]);

        $formPageGenerator
            ->select('naming_structure', $naming_structure, $settings['naming_structure'], 'Name format')
            ->checkbox('use_teacher_prefix', $settings['use_teacher_prefix'], 'Using prefix for a teacher')
            ->select('font_file', $settings['all_font_files'] ?: $fonts, $settings['font_file'], 'Font settings')

            ->numberInput('school_name_font_size', $settings['school_name_font_size'], 'Class Photo: School Name Font Size', true, 1)
            ->numberInput('class_name_font_size', $settings['class_name_font_size'], 'Class Photo: Class Name Font Size', true, 1)
            ->numberInput('year_font_size', $settings['year_font_size'], 'Class Photo: Year Font Size', true, 1)
            ->numberInput('name_font_size', $settings['name_font_size'], 'Class Photo: Name Font Size', true, 1)

            ->numberInput('school_name_font_size_school_photo', $settings['school_name_font_size_school_photo'], 'School Photo: School Name Font Size', true, 1)
            ->numberInput('year_font_size_school_photo', $settings['year_font_size_school_photo'], 'School Photo: Year Font Size', true, 1)
            ->numberInput('name_font_size_school_photo', $settings['name_font_size_school_photo'], 'School Photo: Name Font Size', true, 1)

            ->numberInput('id_cards_portrait_name_size', $settings['id_cards_portrait_name_size'], 'ID Cards Portrait Name Size')
            ->numberInput('id_cards_portrait_title_size', $settings['id_cards_portrait_title_size'], 'ID Cards Portrait Title Size')
            ->numberInput('id_cards_portrait_year_size', $settings['id_cards_portrait_year_size'], 'ID Cards Portrait Year Size')
            ->numberInput('id_cards_landscape_name_size', $settings['id_cards_landscape_name_size'], 'ID Cards Landscape Name Size')
            ->numberInput('id_cards_landscape_title_size', $settings['id_cards_landscape_title_size'], 'ID Cards Landscape Title Size')
            ->numberInput('id_cards_landscape_year_size', $settings['id_cards_landscape_year_size'], 'ID Cards Landscape Year Size');

        $formPageGenerator->getBox()
            ->addElement()->grid()
            ->lgRowCount(4)
            ->mdRowCount(4)
            ->smRowCount(1)
            ->xsRowCount(1)
            ->addElement('elements')->imageInput([
                'name' => 'id_cards_background_portrait',
                'img_url' => $settings->present()->idCardPortraitBackground(),
                'title' => 'ID Cards Background Image (Portrait)'
            ])
            ->parent('grid')->addElement('elements')->imageInput([
                'name' => 'id_cards_background_landscape',
                'img_url' => $settings->present()->idCardLandscapeBackground(),
                'title' => 'ID Cards Background Image (Landscape)'
            ]);

        return $formPageGenerator;
    }

    /**
     * Edit Form Popup
     *
     * @param int                 $schoolId
     * @param Season              $season
     *
     * @return FormGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws PresenterException
     */
    public function getEditForm(int $schoolId, Season $season)
    {
        $formPageGenerator = (new FormPageGenerator())
            ->title($season->school->name, $season->name)
            ->action(route('dashboard::schools.seasons.update', [$schoolId, $season]))
            ->ajax(true)
            ->method('PUT');

        $formPageGenerator = $this->prepareFormBody($formPageGenerator, $schoolId, $season);

        $formPageGenerator->submitButtonTitle('Update season');

        return $formPageGenerator;
    }

    /**
     * @param Season $season
     * @return \Webmagic\Dashboard\Core\Content\ContentFieldsUsable|\Webmagic\Dashboard\Elements\Factories\ElementsCreateAbleContract
     * @throws NoOneFieldsWereDefined
     * @throws FieldUnavailable
     */
    public function getChoiceModal(Season $season)
    {
        $box = (new Box());

        $link1 = (new LinkButton())
            ->content('No')
            ->addClass(' btn-danger ')
            ->dataAttr('dismiss', 'modal');

        $link2 = (new LinkButton())
            ->content('Yes')
            ->class(' btn-success pull-right ')
            ->dataAttr('method', "POST")
            ->addClass(' js_ajax-by-click-btn ')
            ->dataAttr('action', $season->routs()->zipPreparingStart());

        $box->addContent($link1);
        $box->addContent($link2);

        $box->addBoxHeaderContent("<h4>There is a retouching product in ZIP. Would you like to continue?</h4>");

        return $box;
    }
}
