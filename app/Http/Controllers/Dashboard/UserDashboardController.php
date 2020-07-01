<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Jobs\CreateFtpUser;
use App\Jobs\DeleteFtpUser;
use App\Mail\SendCredentialsForPhotographers;
use App\Users\PhotographerService;
use App\Users\Roles\RoleRepo;
use App\Users\User;
use App\Users\UserRepo;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Webmagic\Core\Controllers\AjaxRedirectTrait;
use Webmagic\Core\Entity\Exceptions\EntityNotExtendsModelException;
use Webmagic\Core\Entity\Exceptions\ModelNotDefinedException;
use Webmagic\Dashboard\Components\FormGenerator;
use Webmagic\Dashboard\Components\FormPageGenerator;
use Webmagic\Dashboard\Components\TableGenerator;
use Webmagic\Dashboard\Components\TablePageGenerator;
use Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable;
use Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined;
use Webmagic\Dashboard\Dashboard;
use Webmagic\Dashboard\Elements\Forms\Elements\Switcher;
use Webmagic\Dashboard\Elements\Links\Link;
use Webmagic\Dashboard\Elements\Links\LinkButton;
use Webmagic\Dashboard\Pages\BasePage;

class UserDashboardController extends Controller
{
    use AjaxRedirectTrait;

    /**
     * Display a listing of the resource.
     *
     * @param Dashboard $dashboard
     * @param UserRepo  $userRepo
     * @param Request   $request
     *
     * @return Dashboard
     * @throws NoOneFieldsWereDefined
     */
    public function index(Dashboard $dashboard, UserRepo $userRepo, Request $request)
    {
        $this->authorize('only_admin', Auth::user());

        $users = $userRepo->getAll(10);

        (new TablePageGenerator($dashboard->page()))
            ->tableTitles('ID', 'Email', 'Type', 'Status')
            ->showOnly([
                'id',
                'email',
                'type',
                'status',
            ])->setConfig([
                'type' => function (User $user) {
                    return $user->role->name;
                },
                'status' => function (User $user) {
                    return  (new Switcher())->checked((bool)$user->status)
                        ->classes('js_ajax-by-change')->name('status')
                        ->attr('id', $user->id)
                        ->attr('data-action', route('dashboard::users.update.status', $user->id))
                        ->attr('data-method', 'PUT');
                },
            ])
            ->items($users)
            ->withPagination($users, route('dashboard::users.index'))
            ->createLink(route('dashboard::users.create'))
            ->setShowLinkClosure(function (User $user) {
                return route('dashboard::users.show', $user);
            })->setEditLinkClosure(function (User $user) {
                return route('dashboard::users.edit', $user);
            })
            ->addElementsToToolsCollection(function (User $user) {
                $btn = (new LinkButton())
                    ->icon('fa-trash')
                    ->dataAttr('item', ".js_item_$user->id")
                    ->class('text-red')
                    ->dataAttr('request', route('dashboard::users.destroy', $user))
                    ->dataAttr('method', 'DELETE');

                if(count($user->galleries) > 0){
                    $btn->addClass('disabled');
                    $btn = '<span title="The user has connected galleries">'.$btn->render().'</span>';
                } else {
                    $btn->addClass('js_delete')->dataAttr('title', 'Delete');;
                }
                return $btn;
            });

        // Return content part only for pagination
        if ($request->ajax()) {
            return $dashboard->page()->content()->toArray()['box_body'];
        }

        return $dashboard;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param RoleRepo $roleRepo
     *
     * @return FormPageGenerator
     * @throws Exception
     */
    public function create(RoleRepo $roleRepo)
    {
        $this->authorize('create', Auth::user());
        $roles = $roleRepo->getForSelect('name', 'id');

        $formPageGenerator = (new FormPageGenerator())
            ->title('User creating')
            ->action(route('dashboard::users.store'))
            ->method('POST')

            ->emailInput('email', null, 'Email', true)
            ->passwordInput('password', '', 'Password', true)
            ->passwordInput('password_confirmation', '', 'Confirm password', true)
            ->select('role_id', $roles, 2, 'Type', true)
            ->checkbox('status', true, 'Set user active initially')
            ->checkbox('send_credentials', true, 'Send credentials after creating')
            ->submitButtonTitle('Create');;

        return $formPageGenerator;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return JsonResponse|RedirectResponse|Redirector
     */
    public function store(Request $request, UserRepo $userRepo)
    {
        $this->authorize('create', Auth::user());

        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        $request['credential_password'] = $request->get('password');
        $request['password'] = bcrypt($request->get('password'));

        $user = $userRepo->create($request->all());

        CreateFtpUser::dispatch($user, $request->get('send_credentials'))->onQueue('ftp_user_create');

        return $this->redirect(route('dashboard::users.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param null                $user_id
     * @param UserRepo            $userRepo
     * @param Dashboard           $dashboard
     * @param PhotographerService $photographerService
     *
     * @return BasePage
     * @throws AuthorizationException
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     */
    public function show($user_id = null, UserRepo $userRepo, Dashboard $dashboard, PhotographerService $photographerService)
    {
        $auth_user = Auth::user();
        if($user_id){
            $user = $userRepo->getByID($user_id);
        } else{
            $user = $auth_user;
        }

        $this->authorize('edit', $user);

        $page = $dashboard->page();
        $page->setPageTitle("User view")
            ->element()
            ->box()
            ->addToolsLinkButton(route('dashboard::users.edit', $user), '<i class="fa fa-edit "></i> Edit')
            ->addElement('box_tools')
            ->linkButton('Send credentials')->link(route('dashboard::users.send.credentials', $user->id))->class('btn-primary')
            ->icon('fa-envelope-o')
            ->js()->tooltip()->regular('Send the dashboard and FTP login data on user email')
            ->parent('box')
            ->addElement('box_tools')
            ->linkButton('Files structure guide')->link(route('dashboard::users.download.rules'))->class('btn-primary')
            ->icon('fa-download')
            ->js()->tooltip()->regular('Download the uploading gallery structure rules')
            ->parent('box')
            ->footerAvailable(false)
            ->element()->descriptionList(
                ['data' => [
                    'Email:' => $user->email,
                    'Type:' => $user->role->name,
                    'Status:' => (new Switcher())->checked((bool)$user->status)
                        ->classes('js_ajax-by-change')
                        ->name('status')
                        ->attr('id', $user->id)
                        ->attr('data-action', route('dashboard::users.update.status', $user->id))
                        ->attr('data-method', 'PUT')
                ],
            ])
            ->isHorizontal(true);


        $files = $photographerService->getUnprocessedGallery($user);

        if(count($files)){
            $unprocessedPhotosTable = (new TableGenerator($dashboard->page()))
                ->items($files)
                ->showOnly('gallery_name', 'status')
                ->setConfig([
                    'status' => function ($file) use($user) {
                        return (new LinkButton())
                            ->content('Convert to gallery')
                            ->link(route('dashboard::unprocessed-photos.get-popup-for-convert', [$file['gallery_name'], $file['user_id']]))
                            ->js()->openInModalOnClick()
                            ->regular(route('dashboard::unprocessed-photos.get-popup-for-convert', [$file['gallery_name'], $file['user_id']]), 'POST', 'Attention!')
                            ->dataAttr('reload-after-close-modal', 'true');
                    },
                ])
                ->setDestroyLinkClosure(function ($file) use($user) {
                    return route('dashboard::unprocessed-photos.destroy', [$file['gallery_name'], $user->id]);
                });


            $dashboard->page()->addElement()
                ->h4Title('Unprocessed photos')
                ->parent()->addElement()
                ->box()->content($unprocessedPhotosTable);
        }

        if(!$user->galleries->isEmpty()){
            $galleriesTable = (new TableGenerator($dashboard->page()))
                ->items($user->galleries)
                ->tableTitles('ID', 'Name', 'Status')
                ->showOnly('id','name', 'status')
                ->setConfig([
                    'name' => function ($gallery) {
                        return (new Link())->content($gallery->present()->name)->link(route('dashboard::gallery.show', $gallery->id));
                    },
                    'status' => function ($gallery) {
                        return $gallery->status;
                    },
                ])
                ->setEditLinkClosure(function ($gallery) {
                    if($gallery->status) {
                        return route('dashboard::gallery.edit', ['id' => $gallery['id']]);
                    }
                })
                ->setShowLinkClosure(function ($gallery) {
                    if($gallery->status) {
                        return route('dashboard::gallery.show', ['id' => $gallery['id']]);
                    }
                })
                ->setDestroyLinkClosure(function ($gallery) {
                    if($gallery->status) {
                        return route('dashboard::gallery.destroy', $gallery->id);
                    }
                });

            $dashboard->page()->addElement()
                ->h4Title('Galleries')
                ->parent()->addElement()
                ->box()->content($galleriesTable);
        }

        return $page;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int      $user_id
     * @param UserRepo  $userRepo
     * @param RoleRepo  $roleRepo
     * @param Dashboard $dashboard
     *
     * @return Dashboard
     * @throws Exception
     */
    public function edit($user_id, UserRepo $userRepo, RoleRepo $roleRepo, Dashboard $dashboard)
    {
        if (! $user = $userRepo->getByID($user_id)) {
            abort(404);
        };

        $this->authorize('edit', $user);

        $roles = $roleRepo->getForSelect('name', 'id');

        $form = (new FormGenerator())
            ->action(route('dashboard::users.update', $user))
            ->method('PUT')
            ->switcher('status', $user->status, 'Status')
            ->emailInput('email', $user, 'Email', true)
            ->select('role', $roles, $user->role->id, 'Type', true)
            ->submitButton('Save');

        $passwordForm = (new FormGenerator())
            ->action(route('dashboard::users.update.password', $user))
            ->method('PUT')
            ->passwordInput('password', '', 'Password', true)
            ->passwordInput('password_confirmation', '', 'Confirm password', true)
            ->checkbox('send_credentials', false, 'Send credentials after creating')
            ->submitButton('Update');

        $dashboard->page()->title('User editing')
            ->element()
            ->grid()
            ->lgRowCount(2)
            ->mdRowCount(2)
            ->smRowCount(1)
            ->xsRowCount(1)
            ->addElement()
            ->box()
            ->headerAvailable(false)
            ->footerAvailable(false)
            ->content($form->render())
            ->parent('grid')
            ->addElement()->box()
            ->headerAvailable(true)
            ->footerAvailable(false)
            ->content($passwordForm->render())
            ->addBoxHeaderContent('<span class="text-orange">Warning! The password will be changed only for the admin panel access</span>');

        return $dashboard;
    }

    /**
     * Update user.
     *
     * @param Request                   $request
     * @param                           $user_id
     * @param UserRepo                  $userRepo
     *
     * @return void
     * @throws Exception
     */
    public function update(Request $request, $user_id, UserRepo $userRepo)
    {
        $this->authorize('edit', Auth::user());
        $request->validate([
            'email' => 'required|unique:users,email,'.$user_id,
            'role' => 'required|exists:roles,id',
            'status' => 'boolean',
        ]);

        if (! $user = $userRepo->getByID($user_id)) {
            abort(404);
        };
        $status = $request->get('status') ? true : false;

        $userRepo->update($user_id, [
            'email' => $request->get('email'),
            'status' => $status,
            'role_id' => $request->get('role')
        ]);
    }

    /**
     * Update password for user
     *
     * @param Request                  $request
     * @param                          $user_id
     * @param UserRepo                 $userRepo
     *
     * @throws Exception
     */
    public function updatePassword(Request $request, $user_id, UserRepo $userRepo)
    {
        $this->authorize('edit', Auth::user());

        $request->validate([
            'password' => 'required|confirmed|min:6',
        ]);

        if (! $user = $userRepo->getByID($user_id)) {
            abort(404);
        };

        if (! $userRepo->update($user_id, [
            'password' => bcrypt($request->get('password')),
            'credential_password' => $request->get('password'),
        ])) {
            abort(500, 'Error on updating password');
        }

        if($request->get('send_credentials')){
            $this->sendCredentials($user_id, $userRepo);
        }
    }

    /**
     * Updating user status
     *
     * @param Request  $request
     * @param $user_id
     * @param UserRepo $userRepo
     *
     * @throws Exception
     */
    public function updateStatus(Request $request, $user_id, UserRepo $userRepo)
    {
        if (! $user = $userRepo->getByID($user_id)) {
            abort(404);
        };

        if (! $userRepo->update($user_id, [
            'status' => (bool)$request->get('status'),
        ])) {
            abort(500, 'Error on updating status');
        }
    }

    /**
     * Destroy user
     *
     * @param $user_id
     * @param UserRepo $userRepo
     *
     * @return void
     * @throws EntityNotExtendsModelException
     * @throws ModelNotDefinedException
     */
    public function destroy($user_id, UserRepo $userRepo)
    {
        $this->authorize('delete', Auth::user());

        if (! $user = $userRepo->getByID($user_id)) {
            abort(404);
        };

        $userRepo->destroy($user_id);

        DeleteFtpUser::dispatch($user->ftp_login)->onQueue('ftp_user_delete');
    }

    /**
     * @param $user_id
     * @param UserRepo $userRepo
     *
     * @return JsonResponse|RedirectResponse|Redirector
     * @throws AuthorizationException
     */
    public function sendCredentials($user_id, UserRepo $userRepo)
    {
        if (! $user = $userRepo->getByID($user_id)) {
            abort(404);
        };

        $this->authorize('edit', Auth::user());

        Mail::send(new SendCredentialsForPhotographers(
            $user->email,
            $user->email,
            $user->ftp_login,
            $user->ftp_password
        ));

        return $this->redirect(route('dashboard::users.show', $user->id));
    }

    /**
     * Download file with rules about correct directories structure
     *
     * @return string|BinaryFileResponse
     */
    public function downloadRules()
    {
        $files = File::allFiles(public_path('/files'));
        try {
            $pathToFile = $files[0]->getRealPath();
            return response()->download($pathToFile);
        }
        catch (Exception $e) {
            return "file doesn't exist";
        }
    }
}
