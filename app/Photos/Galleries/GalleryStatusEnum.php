<?php

namespace App\Photos\Galleries;

use MadWeb\Enum\Enum;

/**
 * @method static GalleryStatusEnum PREPARING()
 * @method static GalleryStatusEnum ORIENTATION()
 * @method static GalleryStatusEnum PREVIEWS()
 * @method static GalleryStatusEnum PASSWORDS()
 * @method static GalleryStatusEnum PROOFING()
 * @method static GalleryStatusEnum UPLOADING()
 * @method static GalleryStatusEnum CLEANING()
 * @method static GalleryStatusEnum READY()
 */
final class GalleryStatusEnum extends Enum
{
    const PREPARING = 'Preparing';
    const ORIENTATION = 'Orientation';
    const PREVIEWS = 'Previews generation';
    const PASSWORDS = 'Passwords generation';
    const PROOFING = 'Proofing photos generation';
    const CROPPING = 'Cropping';
    const UPLOADING = 'Uploading to S3';
    const CLEANING = 'Cleaning up';
    const READY = 'Ready';
    const DELETING = 'Deleting';
}
