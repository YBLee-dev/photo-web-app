<?php

namespace App\Http\Controllers\Dashboard;


use App\Events\SeasonUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Photos\Schools\SchoolRepo;
use App\Photos\Seasons\Season;
use App\Photos\Seasons\SeasonDashboardPresenter;
use App\Photos\Seasons\SeasonRepo;
use App\Photos\SettingsGroupPhotos\SettingsGroupPhotosPathsManager;
use App\Photos\SettingsGroupPhotos\SettingsGroupPhotosService;
use App\Processing\Scenarios\SeasonZipPreparingScenario;
use App\Services\DashboardImagesPrepare;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Laracasts\Presenter\Exceptions\PresenterException;
use Webmagic\Core\Entity\Exceptions\EntityNotExtendsModelException;
use Webmagic\Core\Entity\Exceptions\ModelNotDefinedException;
use Webmagic\Dashboard\Components\FormGenerator;
use Webmagic\Dashboard\Components\TableGenerator;
use Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable;
use Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined;
use Webmagic\Dashboard\Core\Content\JsActionsApplicable;
use Webmagic\Dashboard\Elements\Links\LinkButton;

class SeasonDashboardController extends Controller
{
    /**
     * Show page with seasons by school with paginations
     *
     * @param int                      $school_id
     * @param SeasonDashboardPresenter $dashboardPresenter
     * @param SeasonRepo               $seasonRepo
     * @param SchoolRepo               $schoolRepo
     *
     * @return TableGenerator
     * @throws Exception
     */
    public function index(
        int $school_id,
        SeasonDashboardPresenter $dashboardPresenter,
        SeasonRepo $seasonRepo,
        SchoolRepo $schoolRepo
    ) {
        if (! $school = $schoolRepo->getByID($school_id)) {
            abort(404, 'School not found');
        };
        $seasons = $seasonRepo->getAll(10);

        return $dashboardPresenter->getTable($seasons, $school);
    }

    /**
     * Show form in popup for creating season
     *
     * @param int $school_id
     * @param SeasonDashboardPresenter $dashboardPresenter
     *
     * @param \App\Photos\Schools\SchoolRepo $schoolRepo
     * @return FormGenerator
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function create(int $school_id, SeasonDashboardPresenter $dashboardPresenter, SchoolRepo $schoolRepo)
    {
        if (! $school = $schoolRepo->getByID($school_id)) {
            abort(404, 'School not found');
        };

        return $dashboardPresenter->getCreateForm($school);
    }

    /**
     * Show form in popup for editing season
     *
     * @param int                      $school_id
     * @param int                      $season_id
     * @param SeasonRepo               $seasonRepo
     * @param SeasonDashboardPresenter $dashboardPresenter
     *
     * @return FormGenerator
     * @throws PresenterException
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     */
    public function edit(
        int $school_id,
        int $season_id,
        SeasonRepo $seasonRepo,
        SeasonDashboardPresenter $dashboardPresenter
    ) {
        if (! $season = $seasonRepo->getByID($season_id)) {
            abort(404, 'School not found');
        };

        $groupSettings = $settings = $season->groupSettings;
        if(!$groupSettings){
            /** @var SettingsGroupPhotosService $settingsService */
            $settingsService = app()->make(SettingsGroupPhotosService::class);
            $settings = $settingsService->getDefaultSettings();
        }

        return $dashboardPresenter->getEditForm($school_id, $season, $settings);
    }

    /**
     * Create season
     *
     * @param int $school_id
     * @param Request $request
     * @param SeasonRepo $seasonRepo
     * @return JsonResponse|RedirectResponse|Redirector
     * @throws Exception
     */
    public function store(int $school_id, Request $request, SeasonRepo $seasonRepo, SchoolRepo $schoolRepo)
    {
        if (! $school = $schoolRepo->getByID($school_id)) {
            abort(404, 'School not found');
        }

        $data = $request->validate([
            'name'                          => 'unique:seasons,name,NULL,id,school_id,'.$school_id,
            'year'                          => 'required',
            'logo'                          => 'required|string',
            'use_school_logo'               => 'nullable',
            'naming_structure'              => 'required',
            'teacher_prefix'                => 'nullable',
            'font_file'                     => 'required',
            'school_name_font_size'         => 'required',
            'class_name_font_size'          => 'required',
            'year_font_size'                => 'required',
            'name_font_size'                => 'required',
            'school_name_font_size_school_photo' => 'required',
            'year_font_size_school_photo'   => 'required',
            'name_font_size_school_photo'   => 'required',
            'school_background'             => 'nullable|image:jpeg,png',
            'class_background'              => 'nullable|image:jpeg,png',
            'id_cards_background_portrait'  => 'nullable|image:jpeg,png',
            'id_cards_background_landscape' => 'nullable|image:jpeg,png',
            'id_cards_portrait_name_size'   => 'required',
            'id_cards_portrait_title_size'  => 'required',
            'id_cards_portrait_year_size'   => 'required',
            'id_cards_landscape_name_size'  => 'required',
            'id_cards_landscape_title_size' => 'required',
            'id_cards_landscape_year_size'  => 'required',
        ]);

        // Prepare images
        $data = $this->saveFiles($data, [
            'school_background',
            'class_background',
            'id_cards_background_portrait',
            'id_cards_background_landscape',
            'school_logo'
        ], (new SettingsGroupPhotosPathsManager())->templatesDirPath());

        // Prepare settings data
        $settingsService = app()->make(SettingsGroupPhotosService::class);
        $defaultSettings = $settingsService->getDefaultSettings();

        $settings = [
            'school_name'                   => $school->name,
            'year'                          => $data['year'],
            //'school_logo'                   => data_get($data, 'school_logo', $school->school_logo ?: $defaultSettings['school_logo']),
            //'use_school_logo'               => data_get($data, 'use_school_logo', false),

            'naming_structure'              => $data['naming_structure'],
            'use_teacher_prefix'            => data_get($data, 'use_teacher_prefix', false),
            'font_file'                     => $data['font_file'],
            'school_name_font_size'         => $data['school_name_font_size'],
            'class_name_font_size'          => $data['class_name_font_size'],
            'year_font_size'                => $data['year_font_size'],
            'name_font_size'                => $data['name_font_size'],
            'school_name_font_size_school_photo' => $data['year_font_size'],
            'year_font_size_school_photo'   => $data['school_name_font_size_school_photo'],
            'name_font_size_school_photo'   => $data['name_font_size_school_photo'],
            'school_background'             => data_get($data, 'school_background', $defaultSettings['school_background']),
            'class_background'              => data_get($data, 'class_background', $defaultSettings['class_background']),
            'id_cards_background_portrait'  => data_get($data, 'id_cards_background_portrait',$defaultSettings['id_cards_background_portrait']),
            'id_cards_background_landscape' => data_get($data, 'id_cards_background_landscape', $defaultSettings['id_cards_background_landscape']),
            'id_cards_portrait_name_size'   => $data['id_cards_portrait_name_size'],
            'id_cards_portrait_title_size'  => $data['id_cards_portrait_title_size'],
            'id_cards_portrait_year_size'   => $data['id_cards_portrait_year_size'],
            'id_cards_landscape_name_size'  => $data['id_cards_landscape_name_size'],
            'id_cards_landscape_title_size' => $data['id_cards_landscape_title_size'],
            'id_cards_landscape_year_size'  => $data['id_cards_landscape_year_size'],
        ];

        switch ($data['logo']){
            case 'school_logo':
                $settings['use_school_logo'] = true;
                $settings['school_logo'] =  $school->school_logo ?? $defaultSettings['school_logo'];
                break;
            case 'season_logo':
                $settings['use_school_logo'] = true;
                $settings['school_logo'] = data_get($data, 'school_logo', $defaultSettings['school_logo']);
                break;
            case 'without_logo':
                $settings['use_school_logo'] = false;
                $settings['school_logo'] = $defaultSettings['school_logo'];
                break;
        }

        if (! $season = $seasonRepo->create([
            'school_id' => $school_id,
            'name' => $data['name']
        ])) {
            abort(500, 'Error on season creating');
        }

        $season->groupSettings()->create($settings);

        return $this->redirect(route('dashboard::schools.show', ['school' => $school_id]));
    }

    /**
     * Update season
     *
     * @param int        $school_id
     * @param int        $season_id
     * @param Request    $request
     * @param SeasonRepo $seasonRepo
     *
     * @return JsonResponse|RedirectResponse|Redirector
     * @throws Exception
     */
    public function update(int $school_id, int $season_id, Request $request, SeasonRepo $seasonRepo, SchoolRepo $schoolRepo)
    {
        if (! $previewSeason = $seasonRepo->getByID($season_id)) {
            abort(404, 'Season not found');
        }
        if (! $school = $schoolRepo->getByID($school_id)) {
            abort(404, 'School not found');
        }

        $data = $request->validate([
            'name'                          => "required|unique:seasons,name,$school_id,school_id,id,$season_id",
            'year'                          => 'required',
            'school_logo'                   => 'nullable|image:jpeg,png',
            'logo'                          => 'required|string',

            'naming_structure'              => 'required',
            'use_teacher_prefix'            => 'nullable',
            'font_file'                     => 'required',
            'school_name_font_size'         => 'required',
            'class_name_font_size'          => 'required',
            'year_font_size'                => 'required',
            'name_font_size'                => 'required',
            'school_name_font_size_school_photo' => 'required',
            'year_font_size_school_photo'   => 'required',
            'name_font_size_school_photo'   => 'required',
            'school_background'             => 'nullable|image:jpeg,png',
            'class_background'              => 'nullable|image:jpeg,png',
            'id_cards_background_portrait'  => 'nullable|image:jpeg,png',
            'id_cards_background_landscape' => 'nullable|image:jpeg,png',
            'id_cards_portrait_name_size'   => 'required',
            'id_cards_portrait_title_size'  => 'required',
            'id_cards_portrait_year_size'   => 'required',
            'id_cards_landscape_name_size'  => 'required',
            'id_cards_landscape_title_size' => 'required',
            'id_cards_landscape_year_size'  => 'required',
        ]);

        // Prepare images
        $data = $this->saveFiles($data, [
            'school_background',
            'class_background',
            'id_cards_background_portrait',
            'id_cards_background_landscape',
            'school_logo'
        ], (new SettingsGroupPhotosPathsManager())->templatesDirPath());

        // Get default settings if season is old and no settings
        //if(! $groupSettings = $previewSeason->groupSettings) {
            // Prepare settings data
            $settingsService = app()->make(SettingsGroupPhotosService::class);
            $groupSettings = $settingsService->getDefaultSettings();
        //}

        $settings = [
            'school_name'                   => $school->name,
            'year'                          => $data['year'],
            //'school_logo'                   => data_get($data, 'school_logo', $groupSettings['school_logo']),
            //'use_school_logo'               => data_get($data, 'use_school_logo', false),

            'naming_structure'              => $data['naming_structure'],
            'use_teacher_prefix'            => data_get($data, 'use_teacher_prefix', false),
            'font_file'                     => $data['font_file'],
            'school_name_font_size'         => $data['school_name_font_size'],
            'class_name_font_size'          => $data['class_name_font_size'],
            'year_font_size'                => $data['year_font_size'],
            'name_font_size'                => $data['name_font_size'],
            'school_name_font_size_school_photo' => $data['school_name_font_size_school_photo'],
            'year_font_size_school_photo'   => $data['year_font_size_school_photo'],
            'name_font_size_school_photo'   => $data['name_font_size_school_photo'],
            'school_background'             => data_get($data, 'school_background', $groupSettings['school_background']),
            'class_background'              => data_get($data, 'class_background', $groupSettings['class_background']),
            'id_cards_background_portrait'  => data_get($data, 'id_cards_background_portrait',$groupSettings['id_cards_background_portrait']),
            'id_cards_background_landscape' => data_get($data, 'id_cards_background_landscape', $groupSettings['id_cards_background_landscape']),
            'id_cards_portrait_name_size'   => $data['id_cards_portrait_name_size'],
            'id_cards_portrait_title_size'  => $data['id_cards_portrait_title_size'],
            'id_cards_portrait_year_size'   => $data['id_cards_portrait_year_size'],
            'id_cards_landscape_name_size'  => $data['id_cards_landscape_name_size'],
            'id_cards_landscape_title_size' => $data['id_cards_landscape_title_size'],
            'id_cards_landscape_year_size'  => $data['id_cards_landscape_year_size'],
        ];

        switch ($data['logo']){
            case 'school_logo':
                $settings['use_school_logo'] = true;
                $settings['school_logo'] =  $school->school_logo ?? $groupSettings['school_logo'];
                break;
            case 'season_logo':
                $settings['use_school_logo'] = true;
                $settings['school_logo'] = data_get($data, 'school_logo', $groupSettings['school_logo']);
                break;
            case 'without_logo':
                $settings['use_school_logo'] = false;
                $settings['school_logo'] = $groupSettings['school_logo'];
                break;
        }

        $updatedSeason = $previewSeason;
        $updatedSeason->update(['name' => $data['name']]);

        event(new SeasonUpdatedEvent($previewSeason, $updatedSeason));

        if(! $updatedSeason->groupSettings) {
            $updatedSeason->groupSettings()->create($settings);

            return $this->redirect(route('dashboard::schools.show', $school_id));
        }

        $updatedSeason->groupSettings()->update($settings);

        return $this->redirect(route('dashboard::schools.show', $school_id));
    }

    /**
     * Destroy season
     *
     * @param int        $school_id
     * @param int        $season_id
     * @param SeasonRepo $seasonRepo
     *
     * @throws EntityNotExtendsModelException
     * @throws ModelNotDefinedException
     */
    public function destroy(int $school_id, int $season_id, SeasonRepo $seasonRepo)
    {
        if (! $season = $seasonRepo->getByID($season_id)) {
            abort(404, 'Season not found');
        };

        $season->groupSettings()->delete();

        if (! $seasonRepo->destroy($season_id)) {
            abort(500, 'Error on season destroying');
        }
    }

    /**
     * @param int $seasonId
     * @param SeasonRepo $seasonRepo
     *
     * @return mixed|JsActionsApplicable|LinkButton
     *
     * @throws NoOneFieldsWereDefined
     * @throws PresenterException
     */
    public function zipPreparingStatus(int $seasonId, SeasonRepo $seasonRepo)
    {
        /** @var Season $season */
        $season = $seasonRepo->getByID($seasonId);

        if(!$season) {
            abort(404);
        }

        return $season->dashboardElements()->zipProcessingButton();
    }

    /**
     * @param int $seasonId
     * @param SeasonRepo $seasonRepo
     *
     * @return mixed|JsActionsApplicable|LinkButton
     *
     * @throws NoOneFieldsWereDefined
     * @throws PresenterException
     */
    public function zipPreparingStart(int $seasonId, SeasonRepo $seasonRepo)
    {
        /** @var Season $season */
        $season = $seasonRepo->getByID($seasonId);

        if (!$season) {
            abort(404);
        }

        (new SeasonZipPreparingScenario($seasonId))->start();

        return $season->dashboardElements()->zipProcessingButton();
    }

    /**
     * @param int $seasonId
     * @param SeasonRepo $seasonRepo
     * @param SeasonDashboardPresenter $dashboardPresenter
     * @return \Webmagic\Dashboard\Core\Content\ContentFieldsUsable|\Webmagic\Dashboard\Elements\Factories\ElementsCreateAbleContract
     * @throws NoOneFieldsWereDefined
     */
    public function zipPreparingChoice(int $seasonId, SeasonRepo $seasonRepo, SeasonDashboardPresenter $dashboardPresenter)
    {
        /** @var Season $season */
        $season = $seasonRepo->getByID($seasonId);

        if (!$season) {
            abort(404);
        }

        return $dashboardPresenter->getChoiceModal($season);
    }
}
