<?php


namespace App\Photos\Seasons;


use Laracasts\Presenter\Exceptions\PresenterException;
use Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined;
use Webmagic\Dashboard\Core\Content\JsActionsApplicable;
use Webmagic\Dashboard\Elements\Buttons\DefaultButton;
use Webmagic\Dashboard\Elements\Links\LinkButton;

class SeasonDashboardControlsGenerator
{
    /** @var Season */
    protected $season;

    /**
     * SeasonDashboardControlsGenerator constructor.
     *
     * @param Season $season
     */
    public function __construct(Season $season)
    {
        $this->season = $season;
    }

    /**
     * Prepare Season orders zip control
     *
     * @return mixed|JsActionsApplicable|LinkButton
     * @throws NoOneFieldsWereDefined
     * @throws PresenterException
     * @throws \Exception
     */
    public function zipProcessingButton()
    {
        // Show process
        if ($this->season->isZipPreparingInProgress()) {
            return (new DefaultButton())
                ->class('btn-danger')
                ->addIcon('fa-cog fa-spin')
                ->content('Orders zip preparing ...')
                ->js()->contentAutoUpdate()->replaceCurrentElWithContent($this->season->routs()->zipPreparingStatus())
                ->attr('disabled', 'disabled')
            ;
        };

        // Start processing button
        $uniqClass = 'js_autoupdate-' . uniqid();
        $startProcessingBtn = (new DefaultButton())
            ->class('btn-danger')
            ->js()->tooltip()->regular('All printable photos will be updated with new order data');

        if($this->season->isRetouchProductsCountPresent() > 0){
            $startProcessingBtn->js()->openInModalOnClick()
                ->regular($this->season->routs()->zipPreparingChoice(),'GET', 'Choose action', '', true);
        } else {
            $startProcessingBtn->js()->sendRequestOnClick()
            ->replaceWithResponse($this->season->routs()->zipPreparingStart(),".$uniqClass", ['btn-class' => $uniqClass]);
        }

        // Show zip download link
        if ($this->season->isZipPrepared()) {
            $downloadBtn = (new LinkButton())
                ->js()->tooltip()->regular('Download previously generated ZIP archive')
                ->class('btn-success')
                ->icon('fa-download')
                ->content('Download ZIP for print')
                ->link($this->season->present()->zipUrl())
            ;

            $startProcessingBtn->content('Update ZIP archive');

            return "<span class='$uniqClass'>$downloadBtn $startProcessingBtn</span>";
        }

        // Start preparing process
        return  $startProcessingBtn
            ->addClass($uniqClass)
            ->content('Prepares photos and download zip');
    }
}
