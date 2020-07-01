<?php


namespace App\Photos\Galleries;


use App\Processing\ProcessingStatusesEnum;
use Laracasts\Presenter\Exceptions\PresenterException;
use Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined;
use Webmagic\Dashboard\Core\SimpleStringRenderableElement;
use Webmagic\Dashboard\Elements\Buttons\DefaultButton;
use Webmagic\Dashboard\Elements\Links\LinkButton;

class GalleriesDashboardControlsGenerator
{
    /** @var Gallery */
    protected $gallery;

    /**
     * GalleriesDashboardControlsGenerator constructor.
     *
     * @param Gallery $gallery
     */
    public function __construct(Gallery $gallery)
    {
        $this->gallery = $gallery;
    }

    /**
     * @return string
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function shortStatusElement()
    {
//        $shortStatus = $this->gallery->present()->shortStatus();
//        if(ProcessingStatusesEnum::FINISHED()->is()) {
//            return  $shortStatus;
//        }

        return $this->gallery->present()->shortStatus();
    }

    /**
     * Prepare gallery proof photos zip control
     *
     * @return mixed|JsActionsApplicable|LinkButton
     * @throws NoOneFieldsWereDefined
     * @throws PresenterException
     * @throws \Exception
     */
    public function proofPhotosZipProcessingButton()
    {
        // Show process
        if ($this->gallery->isProofPhotosZipInProgress()) {
            return (new DefaultButton())
                ->class('btn-danger')
                ->addIcon('fa-cog fa-spin')
                ->content('Gallery proof photos zip preparing ...')
                ->js()->contentAutoUpdate()->replaceCurrentElWithContent($this->gallery->routs()->proofPhotosZipGenerationStatus())
                ->attr('disabled', 'disabled')
                ;
        };

        // Start processing button
        $uniqClass = 'js_autoupdate-' . uniqid();
        $startProcessingBtn = (new DefaultButton())
            ->class('btn-danger')
            ->addIcon('fa-cog')
            ->js()->tooltip()->regular('Gallery proof photos ZIP will be updated')
            ->js()->sendRequestOnClick()
            ->replaceWithResponse($this->gallery->routs()->proofPhotosZipGenerationStart(),".$uniqClass", ['btn-class' => $uniqClass]);

        // Show zip download link
        if ($this->gallery->isProofPhotosZipReady()) {
            $downloadBtn = (new LinkButton())
                ->js()->tooltip()->regular('Download previously generated ZIP archive')
                ->class('btn-success')
                ->icon('fa-download')
                ->content('Download gallery proof photos ZIP')
                ->link($this->gallery->routs()->proofPhotosExport());

            $startProcessingBtn->content('Update gallery proof photos ZIP');

            return "<span class='$uniqClass'>$downloadBtn $startProcessingBtn</span>";
        }

        if(!$this->gallery->isGroupPhotosWasGenerated()){
            $startProcessingBtn->addClass('disabled')
                ->js()->tooltip()->regular('At first generate gallery group photo');
        }
        // Start preparing process
        return  $startProcessingBtn
            ->addClass($uniqClass)
            ->content('Prepares photos and download zip');
    }
}
