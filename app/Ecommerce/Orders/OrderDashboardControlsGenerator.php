<?php


namespace App\Ecommerce\Orders;


use Laracasts\Presenter\Exceptions\PresenterException;
use Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined;
use Webmagic\Dashboard\Core\Content\JsActionsApplicable;
use Webmagic\Dashboard\Elements\Buttons\DefaultButton;
use Webmagic\Dashboard\Elements\Links\LinkButton;

class OrderDashboardControlsGenerator
{
    /** @var Order */
    protected $order;

    /**
     * OrderDashboardControlsGenerator constructor.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Prepare Order zip control
     *
     * @return mixed|JsActionsApplicable|LinkButton
     * @throws NoOneFieldsWereDefined
     * @throws PresenterException
     */
    public function zipProcessingButton()
    {
        // Show process
        if ($this->order->isZipPreparingInProgress()) {
            return (new DefaultButton())
                ->class('btn-danger')
                ->addIcon('fa-cog fa-spin')
                ->content('Order zip preparing ...')
                ->js()->contentAutoUpdate()->replaceCurrentElWithContent($this->order->routs()->zipPreparingStatus())
                ->attr('disabled', 'disabled')
                ;
        };

        // Start processing button
        $uniqClass = 'js_autoupdate-' . uniqid();
        $startProcessingBtn = (new DefaultButton())
            ->class('btn-danger')
            ->js()->tooltip()->regular('All printable photos will be updated with new order data')
            ->js()->sendRequestOnClick()
            ->replaceWithResponse($this->order->routs()->zipPreparingStart(),".$uniqClass", ['btn-class' => $uniqClass]);

        // Show zip download link
        if ($this->order->isZipPrepared()) {
            $downloadBtn = (new LinkButton())
                ->js()->tooltip()->regular('Download previously generated ZIP archive')
                ->class('btn-success')
                ->icon('fa-download')
                ->content('Download ZIP for print')
                ->link($this->order->present()->zipUrl());

            $startProcessingBtn->content('Update ZIP archive');

            return "<span class='$uniqClass'>$downloadBtn $startProcessingBtn</span>";
        }

        // Start preparing process
        return  $startProcessingBtn
            ->addClass($uniqClass)
            ->content('Prepares photos and download zip');
    }
}
