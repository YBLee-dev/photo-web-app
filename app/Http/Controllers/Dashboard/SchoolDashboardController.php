<?php

namespace App\Http\Controllers\Dashboard;

use App\Photos\People\PersonRepo;
use App\Photos\Schools\SchoolDashboardPresenter;
use App\Photos\Schools\SchoolRepo;
use App\Photos\Seasons\SeasonDashboardPresenter;
use App\Photos\SettingsGroupPhotos\SettingsGroupPhotosPathsManager;
use App\Photos\SettingsGroupPhotos\SettingsGroupPhotosService;
use Illuminate\Http\Request;
use Webmagic\Core\Controllers\AjaxRedirectTrait;
use Webmagic\Dashboard\Dashboard;
use App\Http\Controllers\Controller;

class SchoolDashboardController extends Controller
{
    use AjaxRedirectTrait;

    /**
     * Get list of schools with pagination
     *
     * @param \App\Photos\Schools\SchoolRepo               $schoolRepo
     * @param \Webmagic\Dashboard\Dashboard                $dashboard
     * @param \Illuminate\Http\Request                     $request
     * @param \App\Photos\Schools\SchoolDashboardPresenter $dashboardPresenter
     *
     * @return \Webmagic\Dashboard\Dashboard
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function index(
        SchoolRepo $schoolRepo,
        Dashboard $dashboard,
        Request $request,
        SchoolDashboardPresenter $dashboardPresenter
    ) {
        $schools = $schoolRepo->getAll(10);

        return $dashboardPresenter->getTable($schools, $dashboard, $request);
    }

    /**
     * Show form in popup for creating school
     *
     * @param \App\Photos\Schools\SchoolDashboardPresenter $dashboardPresenter
     *
     * @return \Webmagic\Dashboard\Components\FormGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function create(SchoolDashboardPresenter $dashboardPresenter)
    {
        return $dashboardPresenter->getCreateForm();
    }

    /**
     * Show form in popup for editing school
     *
     * @param $school_id
     * @param \App\Photos\Schools\SchoolRepo $schoolRepo
     * @param \App\Photos\Schools\SchoolDashboardPresenter $dashboardPresenter
     *
     * @return \Webmagic\Dashboard\Components\FormGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function edit($school_id, SchoolRepo $schoolRepo, SchoolDashboardPresenter $dashboardPresenter)
    {
        if (! $school = $schoolRepo->getByID($school_id)) {
            abort(404, 'School not found');
        };

        return $dashboardPresenter->getEditForm($school);
    }

    /**
     * Create school
     *
     * @param \Illuminate\Http\Request       $request
     * @param \App\Photos\Schools\SchoolRepo $schoolRepo
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function store(Request $request, SchoolRepo $schoolRepo, SettingsGroupPhotosService $settingsService)
    {
        $data = $request->validate([
            'school_logo' => 'nullable|image:jpeg,png',
            'name' => 'required'
        ]);

        // Prepare images
        $prepared_image = $this->saveFiles($data, [
            'school_logo'
        ], (new SettingsGroupPhotosPathsManager())->templatesDirPath());

        $groupSettings = $settingsService->getDefaultSettings();
        $data['school_logo'] = data_get($prepared_image, 'school_logo', $groupSettings['school_logo']);


        if (! $schoolRepo->create($data)) {
             abort(500, 'Error on school creating');
        }

        return $this->redirect(route('dashboard::schools.index'));
    }

    /**
     * Update school
     *
     * @param $school_id
     * @param \Illuminate\Http\Request $request
     * @param \App\Photos\Schools\SchoolRepo $schoolRepo
     * @param \App\Photos\SettingsGroupPhotos\SettingsGroupPhotosService $settingsService
     * @param \App\Photos\People\PersonRepo $personRepo
     * @return void
     * @throws \Exception
     */
    public function update(
        $school_id,
        Request $request,
        SchoolRepo $schoolRepo,
        SettingsGroupPhotosService $settingsService,
        PersonRepo $personRepo
    ) {
        if (! $school = $schoolRepo->getByID($school_id)) {
            abort(404, 'School not found');
        }

        $data = $request->validate([
            'school_logo' => 'nullable|image:jpeg,png',
            'name' => 'required'
        ]);

        // Prepare images
        $prepared_image = $this->saveFiles($data, [
            'school_logo'
        ], (new SettingsGroupPhotosPathsManager())->templatesDirPath());

        $groupSettings = $settingsService->getDefaultSettings();
        $data['school_logo'] = data_get($prepared_image, 'school_logo', $groupSettings['school_logo']);

        if (! $schoolRepo->update($school_id, $data)) {
            abort(500, 'Error on school updating');
        }
        foreach ($school->seasons as $season)
        {
          if($season->groupSettings->use_school_logo && $school->school_logo == $season->groupSettings->school_logo){
              $season->groupSettings->school_logo = $data['school_logo'];
              $season->groupSettings->save();
          }
        }

        foreach ($school->galleries as $gallery)
        {
            foreach ($gallery->people as $person){
                $personRepo->update($person->id, ['school_name' => $data['name']]);
            }
        }
    }

    /**
     * Destroy school
     *
     * @param $school_id
     * @param \App\Photos\Schools\SchoolRepo $schoolRepo
     */
    public function destroy($school_id, SchoolRepo $schoolRepo)
    {
        if (! $schoolRepo->getByID($school_id)) {
            abort(404, 'School not found');
        };

        if (! $schoolRepo->destroy($school_id)) {
            abort(500, 'Error on school destroying');
        }
    }

    /**
     * Show school page with adding seasons in table
     *
     * @param $school_id
     * @param \App\Photos\Schools\SchoolRepo $schoolRepo
     * @param \App\Photos\Schools\SchoolDashboardPresenter $dashboardPresenter
     * @param \Webmagic\Dashboard\Dashboard $dashboard
     *
     * @return \Webmagic\Dashboard\Dashboard
     * @throws \Exception
     */
    public function show(
        $school_id,
        SchoolRepo $schoolRepo,
        SchoolDashboardPresenter $dashboardPresenter,
        SeasonDashboardPresenter $seasonDashboardPresenter,
        Dashboard $dashboard
    ) {
        if (! $school = $schoolRepo->getByID($school_id)) {
            abort(404, 'School not found');
        };

        $seasons = $school->seasons()->paginate(10);

        $dashboardPresenter->addEditBtnOnPage($school, $dashboard);

        return $seasonDashboardPresenter->getTablePage($seasons, $school, $dashboard);
    }
}
