<?php


namespace App\Photos\GroupPhotosGeneration;


use App\Photos\People\Person;
use App\Photos\SettingsGroupPhotos\SettingsGroupPhotos;
use Imagick;
use ImagickDraw;
use ImagickException;
use ImagickPixel;
use Laracasts\Presenter\Exceptions\PresenterException;

class PersonalClassPhotoGenerator extends CommonClassPhotoGenerator
{
    /** @var int  */
    protected $photosIndentSize = 25; // px

    /** @var int Max count of small photos in one row */
    protected $smallPhotosRowMaxLength = 6;

    /** @var int Photos signature block height */
    protected $smallPhotoTextLineHeight = 100; // px

    /** @var Person */
    protected $person;

    /** @var  */
    private $personalImageCornersRoundRadius = 100;

    /** @var int Left indent for main photo */
    private $personalPhotoLeftIndent = 155; // px

    /** @var int Personal photo width */
    private $personalPhotoWidth = 698; //px

    /** @var int Indentation from left side for small photos block */
    protected $indentTop = 690; //px

    /** @var int Indentation from left side for small photos block */
    protected $indentLeft = 990; //px

    /**
     * PersonalClassPhotoGenerator constructor.
     *
     * @param string              $galleryName
     * @param string              $year
     * @param SettingsGroupPhotos $settings
     * @param Person              $person
     */
    public function __construct(string $galleryName, string $year, SettingsGroupPhotos $settings, Person $person)
    {
        parent::__construct($galleryName, $person->classroom ?? '', $year, $settings);

        $this->person = $person;
    }

    /**
     * @param string $photoPath
     * @param Person $person
     *
     * @throws PresenterException
     */
    public function addPhoto(string $photoPath, Person $person)
    {
        // Do not add current person to common photos
        if($person->id === $this->person->id){
            return;
        }

        parent::addPhoto($photoPath, $person);
    }

    /**
     * Generate result image
     *
     * @return Imagick
     * @throws ImagickException
     * @throws PresenterException
     */
    protected function generateResultPhoto(): Imagick
    {
        $schoolDraw = $this->generateSchoolDraw();
        $classroomDraw = $this->generateClassroomDraw();
        $yearDraw = $this->generateYearDraw();
        $mainImageDraw = $this->generateMainImageDraw();

        return $this->generateIndividualClassPhoto(
            $mainImageDraw,
            $schoolDraw,
            $classroomDraw,
            $yearDraw,
            $this->settings->present()->classBackgroundPath()
        );
    }

    /**
     * Generate main image draw
     *
     * @throws ImagickException
     * @throws PresenterException
     */
    protected function generateMainImageDraw()
    {
        return $this->preparePortraitImageWithText(
            $this->person->croppedPhoto()->present()->originalUrl(),
            $this->person->present()->name(),
            $this->settings->school_name_font_size,
            $this->settings->present()->fontPath()
        );
    }

    /**
     * Set all elements class background and generate image composition
     *
     * @param Imagick     $mainImage
     * @param ImagickDraw $schoolDraw
     * @param ImagickDraw $classroomDraw
     * @param ImagickDraw $yearDraw
     * @param string      $backgroundPath
     *
     * @return Imagick
     * @throws ImagickException
     * @throws PresenterException
     * @throws \Exception
     */
    protected function generateIndividualClassPhoto(
        Imagick $mainImage,
        ImagickDraw $schoolDraw,
        ImagickDraw $classroomDraw,
        ImagickDraw $yearDraw,
        string $backgroundPath
    ): Imagick {
        $imagick = $this->prepareBackgroundImage($backgroundPath);

        // Small photos block preparing
        $this->updateSmallPhotoSizesIfNeeded($this->indentTop);

        $imagick->compositeImage($mainImage, Imagick::COMPOSITE_ATOP, $this->personalPhotoLeftIndent, 690);

        $imagick->drawImage($schoolDraw);
        $imagick->drawImage($classroomDraw);
        $imagick->drawImage($yearDraw);


        $preparedImages = $this->preparePhotos();

        $imagick = $this->composeSmallPhotos(
            $imagick,
            $preparedImages,
            $this->indentLeft,
            $this->indentTop
        );

        return $imagick->getImage();
    }

    /**
     * Generate big image with name by config
     *
     * @param string $image_path
     * @param string $name
     * @param $nameFontSize
     * @param $nameFont
     *
     * @return Imagick
     * @throws ImagickException
     */
    protected function preparePortraitImageWithText(string $image_path, string $name, $nameFontSize, $nameFont): Imagick
    {
        $imagePhoto = new Imagick($image_path);
        $personalPhotoHeight = 150;

        // Calculate rough text block size
        $textBlockHeight = $nameFontSize * 3;
        $personalPhotoCanvasHeight = 900;
        $imagePhoto->resizeimage($this->personalPhotoWidth, $personalPhotoCanvasHeight, Imagick::FILTER_LANCZOS, 1.0, true);

        // Function is deprecated
        @$imagePhoto->roundCorners($this->personalImageCornersRoundRadius, $this->personalImageCornersRoundRadius);

        $imagePhoto->newImage($this->personalPhotoWidth, $personalPhotoCanvasHeight, new ImagickPixel('transparent'));
        $imagePhoto->setImageFormat('png');

        $imagePhoto = $this->addName(
            $imagePhoto,
            $nameFont,
            $name,
            $this->personalPhotoWidth,
            true,
            $nameFontSize,
            null,
            $this->personalPhotoWidth
        );

        $imagePhoto->resetIterator();
        $combined = $imagePhoto->appendImages(true);

        return $combined;
    }

    /**
     * Update left indent based on current params
     */
    protected function updateSmallPhotosBlockLeftIndent()
    {
        $updatedAllSmallPhotosBlockWidth = $this->allSmallPhotosBlockWidth();

        // Calculate indent between main and small photos for next calculations
        $smallPhotosLeftIndent = $this->indentLeft;
        $indentBetweenMainAndSmallPhotos = $smallPhotosLeftIndent - $this->personalPhotoLeftIndent - $this->personalPhotoWidth;

        // Calculate width of main and small photos block
        $mainAndSmallPhotosBlockWidth = $this->personalPhotoWidth + $indentBetweenMainAndSmallPhotos + $updatedAllSmallPhotosBlockWidth;

        // Calculate indentation for whole block of main and small photos
        $indentWidth = ($this->canvasWidth - $mainAndSmallPhotosBlockWidth) / 2;

        // Calculate deviation of main photo left indentation
        // Needed for updating small photos block indentation
        $indentDeviationValue = $indentWidth - $this->personalPhotoLeftIndent;

        // Update small photos block indentation
        $this->indentLeft += $indentDeviationValue;

        // Update main photo indentation
        $this->personalPhotoLeftIndent += $indentDeviationValue;
    }

    /**
     * Return bottom indent for small photos block
     *
     * @return float|int
     */
    protected function bottomIndent()
    {
        return $this->smallImageHeight;
    }


    /**
     * Check if small images fit normally in their area
     *
     * @param int $initialTopPosition
     *
     * @return bool
     */
    protected function isSmallPhotosFitNormally(int $initialTopPosition)
    {
        // Calc available height based on settings
        $availableHeight = $this->canvasHeight - $initialTopPosition - $this->photosIndentSize*2;

        // Calc needed height for current settings
        $photosCount = count($this->srcPhotos);
        $rowsCount = ceil($photosCount / $this->smallPhotosRowMaxLength);
        $neededHeight = $this->prepareSmallPhotoBlockHeight() * $rowsCount;

        return $availableHeight >= $neededHeight;
    }

}
