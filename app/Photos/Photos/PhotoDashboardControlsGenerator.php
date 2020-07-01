<?php


namespace App\Photos\Photos;


class PhotoDashboardControlsGenerator
{
    /** @var Photo */
    protected $photo;

    /**
     * PhotoDashboardControlsGenerator constructor.
     *
     * @param Photo $photo
     */
    public function __construct(Photo $photo)
    {
        $this->photo = $photo;
    }
}
