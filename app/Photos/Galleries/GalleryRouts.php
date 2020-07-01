<?php


namespace App\Photos\Galleries;


class GalleryRouts
{
    /** @var Gallery */
    protected $gallery;

    /**
     * GalleryRouts constructor.
     *
     * @param Gallery $gallery
     */
    public function __construct(Gallery $gallery)
    {
        $this->gallery = $gallery;
    }

    /**
     * @return string
     */
    public function proofPhotosExport()
    {
        return route('dashboard::gallery.download.proofing.photos', $this->gallery);
    }

    /**
     * @return string
     */
    public function proofPhotosZipGenerationStart()
    {
        return route('dashboard::gallery.download.proofing.photos.start', $this->gallery);
    }

    /**
     * @return string
     */
    public function proofPhotosZipGenerationStatus()
    {
        return route('dashboard::gallery.download.proofing.photos.status', $this->gallery);
    }
}
