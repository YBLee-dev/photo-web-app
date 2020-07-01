<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\DashboardImagesPrepare;
use App\Settings\SettingsRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Webmagic\Core\Controllers\AjaxRedirectTrait;
use Webmagic\Dashboard\Components\FormPageGenerator;
use Webmagic\Dashboard\Elements\Files\ImagePreview;

class SettingsDashboardController extends Controller
{
    use AjaxRedirectTrait;

    /**
     * Settings page
     *
     * @param SettingsRepo $settingsRepo
     * @return FormPageGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function settingsPage(SettingsRepo $settingsRepo)
    {
        $settings = $settingsRepo->getAll()->first();

        $formPageGenerator = (new FormPageGenerator())
            ->title('Settings page')
            ->method('PUT')
            ->action(route('dashboard::settings.main.update'))
            ->ajax(true)
            ->textInput('admin_email', $settings['admin_email'], 'Admin e-mail', true)
            ->visualEditor('email_signature', $settings['email_signature'] ?: [], 'E-mail signature')
            ->submitButtonTitle('Update settings');

        $formPageGenerator->getBox()->addElement()
            ->imageInput()
            ->title('Image (max size 10M)')
            ->addClass('col-md-2')
            ->imgUrl($settings->present()->image)
            ->name('email_signature_image');

        return $formPageGenerator;
    }

    /**
     * Update settings
     *
     * @param Request $request
     * @param SettingsRepo $settingsRepo
     * @return \Illuminate\Database\Eloquent\Model|int
     * @throws \Exception
     */
    public function updateSettings(Request $request, SettingsRepo $settingsRepo, DashboardImagesPrepare $imagesPrepare)
    {
        $validData = $request->validate([
            'admin_email' => 'required|email',
            'email_signature' => 'nullable|string',
            'email_signature_image' => 'image|max:10000'
        ]);

        $request->validate([
            'image_file' => 'image|max:10000'
        ]);

        $path = config('project.settings_img_path');
        $data = $imagesPrepare->saveImagesInDirectory($validData, ['email_signature_image'], $path);

        $settings = $settingsRepo->getAll()->first();
        if(! $settings){
            return $settingsRepo->create($data);
        }

        if(! $settingsRepo->update($settings['id'], $data)){
            return abort(500, 'Cannot store settings!');
        }

        return response('Ok', 200);
    }

    /**
     * Get page for updating watermark
     *
     * @return \Webmagic\Dashboard\Components\FormPageGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function watermark()
    {
        $formPageGenerator = (new FormPageGenerator())
            ->title('Watermark editing', 'max file size 800*800 px')
            ->method('PUT')
            ->action(route('dashboard::settings.watermark.update'))
            ->ajax(true)
            ->fileInput('file', request(), 'New watermark')
            ->submitButtonTitle('Update watermark');

        $formPageGenerator->getForm()->addResultReplaceBlockClass('.js-submit .mailbox-attachments')->content()
            ->addElement()->imagePreview()
            ->imgUrl(asset('img/watermark.png'))
            ->fileName('Current watermark');

        return $formPageGenerator;
    }

    /**
     * Update watermark file
     *
     * @param \Illuminate\Http\Request $request
     * @return \Webmagic\Dashboard\Elements\Files\ImagePreview
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function watermarkUpdate(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:png|dimensions:max_width=800,max_height=800'
        ]);

        request()->file('file')->move(public_path('/img'), 'watermark.png');

        $img = (new ImagePreview())
            ->imgUrl(asset('img/watermark.png').'?v='.uniqid())
            ->fileName('Current watermark');

        return $img;
    }

    /**
     * Get form for updating specification file about directories structure
     *
     * @return \Webmagic\Dashboard\Components\FormPageGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function specification()
    {
        $formPageGenerator = (new FormPageGenerator())
            ->title('Specification updating', 'description about the correct directories structure')
            ->method('PUT')
            ->action(route('dashboard::settings.specification.update'))
            ->ajax(true)
            ->fileInput('file', request(), 'New file')
            ->submitButtonTitle('Update file');

        $formPageGenerator->getForm()->content()
            ->addElement()
            ->linkButton('Files structure guide')
            ->link(route('dashboard::users.download.rules'))
            ->class('btn-primary margin');

        return $formPageGenerator;
    }

    /**
     * Update specification file
     *
     * @param \Illuminate\Http\Request $request
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function specificationUpdate(Request $request)
    {
        $request->validate([
            'file' => 'required|file'
        ]);

        $path = public_path('/files');

        if (is_dir($path) && file_exists($path)){
            $files = File::allFiles($path);
            File::delete($files);
        }

        $ext = request()->file('file')->getClientOriginalExtension();

        request()->file('file')->move(public_path('/files'), 'specification.'.$ext);
    }

    /**
     * Get form for updating content texts for order and delivery page
     *
     * @return \Webmagic\Dashboard\Components\FormPageGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function text()
    {
        $texts = DB::table('order_pages_text')->find(1);

        $formPageGenerator = (new FormPageGenerator())
            ->title('Content for order page editing')
            ->method('PUT')
            ->action(route('dashboard::settings.text.update'))
            ->ajax(true)
            ->visualEditor('delivery', $texts->delivery ?? false, 'Delivery text')
            //->visualEditor('successful_order', $texts->successful_order ?? false, 'Successful order text')
            ->submitButtonTitle('Update');

        return $formPageGenerator;
    }

    /**
     * Update or create info about text in db
     *
     * @param \Illuminate\Http\Request $request
     */
    public function textUpdate(Request $request)
    {
        DB::table('order_pages_text')
            ->updateOrInsert(['id' => 1], $request->only('delivery'));
    }

    /**
     * Get form for set tax for packages/products which marked as taxable
     *
     * @return \Webmagic\Dashboard\Components\FormPageGenerator
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function tax()
    {
        $tax = DB::table('settings_tax')->find(1);

        $formPageGenerator = (new FormPageGenerator())
            ->title('Tax editing')
            ->method('PUT')
            ->action(route('dashboard::settings.tax.update'))
            ->ajax(true)
            ->numberInput('value', $tax->value ?? false, 'tax value in %', false, 0.01)
            ->submitButtonTitle('Update');

        return $formPageGenerator;
    }

    /**
     * Update or create tax value in db
     *
     * @param \Illuminate\Http\Request $request
     */
    public function taxUpdate(Request $request)
    {
        DB::table('settings_tax')
            ->updateOrInsert(['id' => 1], $request->only('value'));
    }
}
