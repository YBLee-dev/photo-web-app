<?php


namespace App\Photos\TemplatedPhotosGeneration;

use App\Photos\Galleries\Gallery;
use App\Photos\People\Person;
use App\Photos\Photos\Photo;
use App\Photos\Photos\PhotosFactory;
use App\Photos\Photos\PhotoStorageManager;
use App\Photos\Photos\PhotoTypeEnum;
use App\Photos\SubGalleries\SubGallery;
use Carbon\Carbon;
use ImagickException;
use Laracasts\Presenter\Exceptions\PresenterException;

class TemplatedPhotoGenerator
{
    /**
     * @param Person $person
     *
     * @throws PresenterException
     */
    public function freeGiftGenerate(Person $person)
    {
        // Person photo for gift
        /** @var Photo $photoForGift */
        $photoForGift = $person->subgallery->mainPhoto();

        // Prepare image
        $photoFactory = new PhotosFactory();
        $freeGiftPhoto = $photoFactory->createEmptyPhoto(PhotoTypeEnum::FREE_GIFT, 'png');

        $path = $freeGiftPhoto->present()->originalLocalPath();

        // Generate and save
        (new PhotoStorageManager())->freeGiftsPrepareLocalDir();
        (new FreeGiftGenerator($photoForGift->present()->originalUrl()))
            ->generateAndSave($path);

        // Update photo info
        $freeGiftPhoto = $photoFactory->updatePhotoFromFile($freeGiftPhoto, $path);

        // Attach photo
        $person->photos()->attach($freeGiftPhoto->id);
    }

    /**
     * @param SubGallery  $subGallery
     *
     * @param string|null $newPhotoUrl
     *
     * @return string|bool
     * @throws ImagickException
     * @throws PresenterException
     */
    public function generateProofPhoto(SubGallery $subGallery, string $newPhotoUrl = null)
    {
        // Do nothing if Person already has proof photo
        if($subGallery->person->proofPhoto() && !$newPhotoUrl){
            return ;
        }

        //jt added
        if ($subGallery->mainPhoto() == null) {
            return;
        }

        // Generate
        $generator = (new ProofPhotoGenerator(
            $subGallery->person->school_name,
            $subGallery->person->present()->name(),
            $subGallery->person->classroom != 'without classroom' ? $subGallery->person->classroom : '',
            $subGallery->password,
            Carbon::parse($subGallery->gallery->deadline)->format('F d, Y'),
            $newPhotoUrl ?: $subGallery->mainPhoto()->present()->previewUrl(),
            $subGallery->person->isStaff() ? $subGallery->person->title ?: 'Teacher' : ''
        ));

        // Prepare directory
        (new PhotoStorageManager())->proofsPrepareLocalDir();

        // Prepare photo
        $photoFactory = new PhotosFactory();
        $photo = $photoFactory->createEmptyPhoto(PhotoTypeEnum::PROOF);
        $path = $photo->present()->originalLocalPath();

        $generator->saveResultImage($path);

        // Update photo date
        $photo = $photoFactory->updatePhotoFromFile($photo, $path);

        // Attache photo to person
        $subGallery->person->photos()->attach($photo->id);

        return $photo;
    }

    /**
     * Generate all ID cards for whole gallery
     *
     * @param Gallery $gallery
     *
     * @throws ImagickException
     * @throws PresenterException
     */
    public function generateIdCardsForAllStaffInGallery(Gallery $gallery)
    {
        /** @var Person [] $teachers */
        $teachers = $gallery->teachers;

        foreach ($teachers as $teacher) {
            $this->generateIDCards($teacher);
        }
    }

    /**
     * @param Person $person
     *
     * @throws ImagickException
     * @throws PresenterException
     */
    public function generateIDCards(Person $person)
    {
        // Prepare directory
        $photoStorageManager = new PhotoStorageManager();
        $photoStorageManager->iDCardsLandscapePrepareLocalDir();
        $photoStorageManager->iDCardsPortraitPrepareLocalDir();

        // Prepare photos
        $photoFactory = new PhotosFactory();
        $portraitPhoto = $photoFactory->createEmptyPhoto(PhotoTypeEnum::ID_CARD_PORTRAIT);
        $landscapePhoto = $photoFactory->createEmptyPhoto(PhotoTypeEnum::ID_CARD_LANDSCAPE);
        $portraitPhotoPath = $portraitPhoto->present()->originalLocalPath();
        $landscapePhotoPath = $landscapePhoto->present()->originalLocalPath();

        // Generate images
        $generator = (new IDCardsGenerator($person));
        $generator->generateAndSavePortraitImage($portraitPhotoPath);
        $generator->generateAndSaveLandscapeImage($landscapePhotoPath);

        // Update photos data
        $photoFactory->updatePhotoFromFile($portraitPhoto, $portraitPhotoPath);
        $photoFactory->updatePhotoFromFile($landscapePhoto, $landscapePhotoPath);

        // Attach to person
        $person->photos()->attach($portraitPhoto->id);
        $person->photos()->attach($landscapePhoto->id);
    }
}
