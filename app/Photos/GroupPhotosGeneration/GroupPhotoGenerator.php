<?php


namespace App\Photos\GroupPhotosGeneration;


use App\Photos\Galleries\Gallery;
use App\Photos\People\Person;
use App\Photos\Photos\PhotosFactory;
use App\Photos\Photos\PhotoStorageManager;
use App\Photos\Photos\PhotoTypeEnum;
use ImagickException;
use Laracasts\Presenter\Exceptions\PresenterException;

class GroupPhotoGenerator
{
    /**
     * @param Gallery $gallery
     *
     * @throws ImagickException
     * @throws PresenterException
     */
    public function generateMiniWalletPhotos(Gallery $gallery)
    {
        // Prepare paths
        $children = $gallery->children;
        $childrenCroppedPhotos = [];


        foreach ($children as $child)
        {
            //todo check why failed
            if (! $child->croppedPhoto()) {
                continue;
            }
            $childrenCroppedPhotos[$child->classroom ?: 'No classroom'][] = $child->croppedPhoto()->present()->originalUrl();
        }

        $count_rows = 1;
        $collagePhotoCount = 0;
        $rows = [];
        foreach ($childrenCroppedPhotos as $classname => $photos)
        {
            $class_rows = array_chunk($photos, 6);

            foreach ($class_rows as $classRow)
            {
                if($count_rows > 5){
                    $collagePhotoCount++;
                    $rows = [];
                    $count_rows = 1;
                }
                $rows = array_merge($rows, $classRow);
                $prepared_data[$collagePhotoCount][$classname] = $rows;

                $count_rows++;
            }
            $rows = [];
        }

        // Generate photos
        (new PhotoStorageManager())->miniWalletCollagesPrepareLocalDir();

        $generator = new MiniWalletSizedCollageGenerator();
        $photoFactory = new PhotosFactory();
        foreach ($prepared_data as $key => $oneCollagePhotos)
        {
            // Prepare empty photo
            $photo = $photoFactory->createEmptyPhoto(PhotoTypeEnum::MINI_WALLET_COLLAGE);

            // Generate mini wallet collage
            $miniWalletCollageLocalPath = $photo->present()->originalLocalPath();
            $result = $generator->create($oneCollagePhotos);

            imagejpeg($result['image'], $miniWalletCollageLocalPath,100);
            // Cleanup resources
            imagedestroy($result['image']);

            //Update photo data
            $photo = $photoFactory->updatePhotoFromFile($photo, $miniWalletCollageLocalPath);

            //Attach photo to Gallery
            $gallery->photos()->attach($photo->id);
        }
    }

    /**
     * Create all class photos  for gallery and store it locally
     *
     * @param Gallery $gallery
     *
     * @return mixed
     * @throws ImagickException
     * @throws PresenterException
     */
    public function allCommonClassPhotosGenerate(Gallery $gallery): array
    {
        $classRoomsList = $gallery->getClassroomsList();
        $classPhotosLocalPaths = [];

        foreach ($classRoomsList as $classRoom) {
            $classPhotosLocalPaths[] = $this->commonClassPhotoGenerate($gallery, $classRoom);
        }

        return $classPhotosLocalPaths;
    }

    /**
     * Create all class photos  for gallery and store it locally
     *
     * @param Gallery $gallery
     *
     * @return mixed
     * @throws ImagickException
     * @throws PresenterException
     */
    public function personalClassPhotosGenerateForAllClasses(Gallery $gallery): array
    {
        $people = $gallery->people;
        $classPhotosLocalPaths = [];

        foreach ($people as $person) {
            // Don't generate for people not available for class photo
            if(!$person->shouldBeOnClassPhoto()) {
                continue;
            }
            $classPhotosLocalPaths[] = $this->personalClassPhotoGenerate($gallery, $person);
        }

        return $classPhotosLocalPaths;
    }

    /**
     * Generate class photo for class in  gallery and store it locally
     *
     * @param Gallery $gallery
     * @param string  $classRoom
     *
     * @return mixed
     * @throws ImagickException
     * @throws PresenterException
     */
    public function commonClassPhotoGenerate(Gallery $gallery, string $classRoom)
    {
        /** @var Person [] $people */
        $people = $gallery->classRoomPeople($classRoom);
        $year = $gallery->season->groupSettings->year;
        $settings = $gallery->season->groupSettings;

        // Add photos
        $classPhotoGenerator = new CommonClassPhotoGenerator($gallery->present()->schoolName, $classRoom, $year, $settings);

        foreach ($people as $person) {
            // Don't add person not available for class photo
            if(!$person->shouldBeOnClassPhoto() && !$person->all_class_photo) {
                continue;
            }
            if($person->croppedPhoto()) {
                $classPhotoGenerator->addPhoto($person->croppedPhoto()->present()->originalUrl(), $person);
            }
        }

        // Generate and save result
        (new PhotoStorageManager())->classCommonPhotosPrepareLocalDir(); // make dir in local - name: class-common
        $photoFactory = new PhotosFactory();
        $photo = $photoFactory->createEmptyPhoto(PhotoTypeEnum::COMMON_CLASS_PHOTO); //create new record (none path) in photo table
        $path = $photo->present()->originalLocalPath();

        $classPhotoGenerator->saveResultPhoto($path);

        // Update photo info
        $photo = $photoFactory->updatePhotoFromFile($photo, $path);

        // Attache to gallery
        $gallery->photos()->attach($photo->id);

        // Attache photo to persons
        foreach ($people as $person) {
            $person->photos()->attach($photo->id);
        }

        return $photo;
    }

    /**
     * Generate class photo for class in  gallery and store it locally
     *
     * @param Gallery $gallery
     *
     * @return mixed
     * @throws ImagickException
     * @throws PresenterException
     */
    public function staffPhotoGenerate(Gallery $gallery)
    {
        /** @var Person [] $teachers */
        $teachers = $gallery->teachers;
        if(count($teachers) == 0){
            //Don't generate stuff photos if there are no teachers in gallery
            return false;
        }

        $year = $gallery->season->groupSettings->year;
        $title = "Staff" ;
        $settings = $gallery->season->groupSettings;

        // Add photos
        $classPhotoGenerator = new StaffCommonPhotoGenerator($gallery->present()->schoolName, $title, $year, $settings);
        foreach ($teachers as $person) {
            // Don't add person not available for class photo
            if(!$person->shouldBeOnClassPhoto()) {
                continue;
            }
            if($person->croppedPhoto()) {
                $classPhotoGenerator->addPhoto($person->croppedPhoto()->present()->originalUrl(), $person);
            }
        }

        // Generate and save result
        (new PhotoStorageManager())->staffPhotosPrepareLocalDir();
        $photoFactory = new PhotosFactory();
        $photo = $photoFactory->createEmptyPhoto(PhotoTypeEnum::STAFF_PHOTO);
        $path = $photo->present()->originalLocalPath();

        $classPhotoGenerator->saveResultPhoto($path);

        // Update photo info
        $photoFactory->updatePhotoFromFile($photo, $path);

        // Attache to gallery
        $gallery->photos()->attach($photo->id);

        // Attache photo to persons
        foreach ($teachers as $person) {
            $person->photos()->attach($photo->id);
        }

        return $photo;
    }

    /**
     * Generate class photo for class in  gallery and store it locally
     *
     * @param Gallery $gallery
     *
     * @return mixed
     * @throws ImagickException
     * @throws PresenterException
     */
    public function schoolPhotoGenerate(Gallery $gallery)
    {
        /** @var Person [] $people */
        $people = $gallery->people;
        $year = $gallery->season->groupSettings->year;
        $schoolName = $gallery->school->name;
        $settings = $gallery->season->groupSettings;

        // Add photos
        $schoolPhotoGenerator = new CommonSchoolPhotoGenerator($gallery->present()->schoolName, $schoolName, $year, $settings);
        foreach ($people as $person) {
            // Don't add person not available for class photo
            if(!$person->shouldBeOnSchoolPhoto()) {
                continue;
            }
            if($person->croppedPhoto()) {
                $schoolPhotoGenerator->addPhoto($person->croppedPhoto()->present()->originalUrl(), $person);
            }
        }

        // Generate and save result
        (new PhotoStorageManager())->schoolPhotosPrepareLocalDir();
        $photoFactory = new PhotosFactory();
        $photo = $photoFactory->createEmptyPhoto(PhotoTypeEnum::SCHOOL_PHOTO);
        $path = $photo->present()->originalLocalPath();

        $schoolPhotoGenerator->saveResultPhoto($path);

        // Update photo info
        $photoFactory->updatePhotoFromFile($photo, $path);

        // Attache photo to gallery
        $gallery->photos()->attach($photo->id);

        return $photo;
    }

    /**
     * @param Gallery $gallery
     * @param Person  $person
     *
     * @return bool
     * @throws ImagickException
     * @throws PresenterException
     */
    public function personalClassPhotoGenerateIfNeeded(Gallery $gallery, Person $person)
    {
        // Don't generate for people not available for class photo
        if($person->shouldBeOnClassPhoto()) {
            return $this->personalClassPhotoGenerate($gallery, $person);
        }

        return false;
    }

    /**
     * Generate class photo for class in  gallery and store it locally
     *
     * @param Gallery $gallery
     * @param Person  $person
     *
     * @return mixed
     * @throws ImagickException
     * @throws PresenterException
     */
    public function personalClassPhotoGenerate(Gallery $gallery, Person $person)
    {
        if(! $person->croppedPhoto() || ! $person->croppedPhoto()->isReadAble()) {
            return '';
        }

            /** @var Person [] $people */
        $people = $gallery->classRoomPeople($person->classroom);
        $year = $gallery->season->groupSettings->year;
        $settings = $gallery->season->groupSettings;

        // Add photos
        $classPhotoGenerator = new PersonalClassPhotoGenerator($gallery->present()->schoolName, $year, $settings, $person);
        foreach ($people as $otherPerson) {
            // Don't add person not available for class photo
            if(!$otherPerson->shouldBeOnClassPhoto()) {
                continue;
            }
            if($otherPerson->croppedPhoto() && $otherPerson->croppedPhoto()->isReadAble()) {
                $classPhotoGenerator->addPhoto($otherPerson->croppedPhoto()->present()->originalUrl(), $otherPerson);
            }
        }

        // Generate and save result
        (new PhotoStorageManager())->classPersonalPhotosPrepareLocalDir();
        $photoFactory = new PhotosFactory();
        $photo = $photoFactory->createEmptyPhoto(PhotoTypeEnum::PERSONAL_CLASS_PHOTO);
        $path = $photo->present()->originalLocalPath();

        $classPhotoGenerator->saveResultPhoto($path);

        // Update photo info
        $photoFactory->updatePhotoFromFile($photo, $path);

        // Attache photo to person
        $person->photos()->attach($photo->id);

        return $photo;
    }

}
