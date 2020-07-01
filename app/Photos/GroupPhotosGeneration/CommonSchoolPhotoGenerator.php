<?php


namespace App\Photos\GroupPhotosGeneration;


use App\Photos\SettingsGroupPhotos\SettingsGroupPhotos;
use App\Photos\SettingsGroupPhotos\SettingsGroupPhotosPathsManager;
use App\Photos\SettingsGroupPhotos\SettingsGroupPhotosService;
use Imagick;
use ImagickDraw;
use ImagickException;
use ImagickPixel;
use Laracasts\Presenter\Exceptions\PresenterException;

class CommonSchoolPhotoGenerator extends CommonClassPhotoGenerator
{
    /** @var int Small image width in pixels */
    protected $smallImageWidth = 600;

    /** @var int Small image width in pixels */
    protected $smallImageHeight = 750;

    /** @var int  */
    protected $photosIndentSize = 60; // px

    /** @var int Max count of small photos in one row */
    protected $smallPhotosRowMaxLength;

    /** @var int Max count of small staff photos in one row */
    protected $smallPhotosStaffRowMaxLength;

    protected $smallPhotosLogoLength = 3;

    protected $staffPartsAroundLogo = 4;
    protected $staffRowsWithLogo = 2;

    protected $smallPhotoTextLineHeight; //px

    /** @var int canvas width */
    protected $canvasWidth = 6000; //px

    /** @var int  */
    protected $canvasHeight = 4800; //px
    protected $botoom_y;

    /**
     * ClassPhotoGenerator constructor.
     *
     * @param string              $galleryName
     * @param string              $classroomName
     * @param string              $year
     * @param SettingsGroupPhotos $settings
     */
    public function __construct(string $galleryName, string $classroomName, string $year, SettingsGroupPhotos $settings)
    {
        parent::__construct($galleryName, $classroomName, $year, $settings);

        $this->photoNameFontSize = $settings->name_font_size_school_photo;
        $this->smallPhotoTextLineHeight = $settings->name_font_size_school_photo * 2;

        if($settings->use_school_logo){
            $this->smallPhotosRowMaxLength = 7;
            $this->smallPhotosStaffRowMaxLength = 4;
        } else {
            $this->smallPhotosRowMaxLength = 7;
            $this->smallPhotosStaffRowMaxLength = 7;
        }
    }

    /**
     * @return float|int
     */
    protected function prepareSmallPhotoBlockHeight()
    {
        return $this->smallImageHeight + $this->smallPhotoTextBlockHeight();
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
        $yearDraw = $this->generateYearDraw();

        return $this->generateSchoolPhoto($schoolDraw, $yearDraw, $this->settings->present()->schoolBackgroundPath());
    }

    /**
     * Generate school draw
     *
     * @return ImagickDraw
     * @throws PresenterException
     */
    protected function generateSchoolDraw(): ImagickDraw
    {
        return $this->generateTextDraw(
            $this->galleryName,
            $this->settings->school_name_font_size_school_photo,
            $this->settings->present()->fontPath(),
            3000,
            300
        );
    }

    /**
     * Generate year draw
     *
     * @return ImagickDraw
     * @throws PresenterException
     */
    protected function generateYearDraw(): ImagickDraw
    {
        return $this->generateTextDraw(
            $this->year,
            $this->settings->year_font_size_school_photo,
            $this->settings->present()->fontPath(),
            3000,
            425
        );
    }


    /**
     * Set all images and text on school background and generate image composition
     *
     * @param ImagickDraw $schoolDraw
     * @param ImagickDraw $yearDraw
     * @param string      $backgroundPath
     *
     * @return Imagick
     * @throws ImagickException
     * @throws PresenterException
     * @throws \Exception
     */
    protected function generateSchoolPhoto(
        ImagickDraw $schoolDraw,
        ImagickDraw $yearDraw,
        string $backgroundPath
    ): Imagick {

        // Small images compose
        $indentLeft = 540;
        $indentTop = 575;

        $this->updateSmallPhotoSizesIfNeeded($indentTop);

        $preparedImages = $this->preparePhotos();


        $preparedSplitImages['children'] = array_filter($preparedImages, function($photo) {
            return !$photo['staff'];
        });

        $staff = array_filter($preparedImages, function($photo) {
            return $photo['staff'];
        });

        if($this->settings->use_school_logo && count($staff)){
            $preparedSplitImages['staff'] = $this->prepareStaffAreaForLogo($staff);
        } else {
            $preparedSplitImages['staff'] =  array_chunk($staff, $this->smallPhotosStaffRowMaxLength);;
        }

        $image = new Imagick();
        $image->newImage(6000, 4800, new ImagickPixel('white'));
        $image->drawImage($schoolDraw);
        $image->drawImage($yearDraw);


        $this->composeSmallPhotos($image, $preparedSplitImages, $indentLeft, $indentTop);

        if($this->settings->use_school_logo) {
            $this->setLogo($image, $indentTop);
        }

        $image->cropImage($image->getImageWidth() - $indentLeft * 2, $this->botoom_y, $indentLeft, 0);

        $collage = $this->setOnCenterPreparedImage($backgroundPath, $image);

        return $collage->getImage();
    }

    protected function setOnCenterPreparedImage(string $backgroundPath, $image)
    {
        $imagick = new Imagick($backgroundPath);

        $y = ($imagick->getImageHeight() - $image->getImageHeight()) / 2;
        $x = ($imagick->getImageWidth() - $image->getImageWidth()) / 2;

        $imagick->compositeImage($image, Imagick::COMPOSITE_ATOP, $x, $y);

        return $imagick;
    }

    /**
     * @param int $initialTopPosition
     * @throws \Exception
     */
    protected function updateSmallPhotoSizesIfNeeded(int $initialTopPosition)
    {
        $maxOptimizationIterations = 10;

        $counter = 0;

        $children = array_filter($this->srcPhotos, function($photo) {
            return !$photo['staff'];
        });

        while (! $this->isSmallPhotosFitNormally($initialTopPosition, count($children)) || (count($children) % $this->smallPhotosRowMaxLength) == 1) {

            $this->makeOneRowLessByAddedToRows();

            if ($counter > $maxOptimizationIterations) {
                throw new \Exception("Small photo sizes calculation processed more than $maxOptimizationIterations times");
            }
        }

    }

    /**
     * Create small white image like small photo
     *
     * @param $isStaff
     * @return array
     * @throws \ImagickException
     */
    protected function prepareSmallWhiteImage($isStaff): array
    {
        $imagic = new Imagick();

        $imagic->newImage($this->smallImageWidth, $this->smallImageHeight, new ImagickPixel('white'));
        $imagic->setImageFormat('png');

        $imagic->resetIterator();
        $combined['image'] = $imagic->appendImages(true);
        $combined['staff'] = $isStaff;

        return $combined;
    }

    /**
     * Composite logo on school photo
     *
     * @param \Imagick $canvas
     * @param int      $initialTopIndent
     *
     * @return \Imagick
     * @throws ImagickException
     * @throws PresenterException
     */
    protected function setLogo(Imagick $canvas, int $initialTopIndent)
    {
        $logoWidth =  $this->smallPhotosLogoLength * $this->prepareSmallPhotoBlockWidth();
        $logoHeight = $this->staffRowsWithLogo * $this->prepareSmallPhotoBlockHeight() - $this->smallPhotoTextBlockHeight();

        $logoUrl = $this->settings->present()->schoolLogoPath();

        //Set default logo if image doesn't exist
        if(!$logoUrl){
            $groupPhotosService = new SettingsGroupPhotosService();
            $fileName = $groupPhotosService->getDefaultSettings()['school_logo'];
            $logoUrl = (new SettingsGroupPhotosPathsManager())->templateTmpPublicPath($fileName);
        }

        $logo = new Imagick($logoUrl);
        $logo->resizeImage($logoWidth,$logoHeight,imagick::FILTER_POINT,1,true);

        $x = $this->canvasWidth/2 - $logo->getImageWidth()/2;
        $y = $initialTopIndent;

        $canvas->compositeImage($logo, Imagick::COMPOSITE_ATOP, $x, $y);

        return $canvas;
    }

    /**
     * @param Imagick $canvas
     * @param array   $photos
     * @param int     $initialLeftIndent
     * @param int     $initialTopIndent
     *
     * @return Imagick
     */
    protected function composeSmallPhotos(
        Imagick $canvas,
        array $photos,
        int $initialLeftIndent,
        int $initialTopIndent
    ) {
        $x = $initialLeftIndent;
        $widthStep = $this->prepareSmallPhotoBlockWidth();

        $y = $initialTopIndent;
        $heightStep = $this->prepareSmallPhotoBlockHeight();

        foreach ($photos['staff'] as $row) {
            // Prepare position for not full row
            $rowLength = count($row);
            if ($rowLength < $this->smallPhotosStaffRowMaxLength) {

                $x = $x + ($this->smallPhotosRowMaxLength - $rowLength) * $widthStep / 2;
            }

            foreach ($row as $image){
                $canvas->compositeImage($image['image'], Imagick::COMPOSITE_ATOP, $x, $y);
                $x += $widthStep;
            }

            // Move down on one row
            $y += $heightStep;
            $x = $initialLeftIndent;
        }

        $childrenSplitPhotos = array_chunk($photos['children'], $this->smallPhotosRowMaxLength);
        if(count($photos['staff']) == 0 && $this->settings->use_school_logo){
            $y = $y + $heightStep*2;
        }

        if(count($photos['staff']) == 0 && !$this->settings->use_school_logo && count($childrenSplitPhotos) <= 3){
            $y = $y + $heightStep;
        }

        foreach ($childrenSplitPhotos as $row) {
            // Prepare position for not full row
            $rowLength = count($row);
            if ($rowLength < $this->smallPhotosRowMaxLength) {
                $x = $x + ($this->smallPhotosRowMaxLength - $rowLength) * $widthStep/2;
            }

            foreach ($row as $image){
                $canvas->compositeImage($image['image'], Imagick::COMPOSITE_ATOP, $x, $y);
                $x += $widthStep;
            }

            // Move down on one row
            $y += $heightStep;
            $x = $initialLeftIndent;
        }
        $this->botoom_y = $y;
        return $canvas;
    }

    /**
     * Prepare array of staff images with spaces for adding logo
     *
     * @param array $staff
     * @return array
     * @throws \ImagickException
     */
    protected function prepareStaffAreaForLogo(array $staff): array
    {
        $countStaffRows = ceil(count($staff) / $this->smallPhotosStaffRowMaxLength);
        $countStaffByRow = ceil(count($staff) / $countStaffRows);
        $photosByPart = ceil($countStaffByRow / 2);

        // Set spaces for logo
        $logoArea = $this->smallPhotosRowMaxLength - $photosByPart*2;

        while ($logoArea != 0){
            $logoAreaWhiteSpace[] = $this->prepareSmallWhiteImage(true);
            $logoArea--;
        }

        //Split all photos by rows
        $from = 0;
        while ($countStaffRows!= 0)
        {
            $photoByRows[] = array_slice($staff, $from, $photosByPart*2);
            $from += $photosByPart*2;
            $countStaffRows--;
        }

        foreach ($photoByRows as $rowKey => $row)
        {
            $partOfRow = ceil(count($row)/2);

            $staffSplitPhotos = array_chunk($row, $partOfRow);

            foreach ($staffSplitPhotos as $key => $photos)
            {
                //Add spaces like empty photos for alignment
                if(count($photos) < $photosByPart && $rowKey <= 1){
                    $diff = $photosByPart - count($photos);
                    while($diff != 0){
                        if($key%2){
                            array_push($photos, $this->prepareSmallWhiteImage(true));
                        } else{
                            array_unshift($photos,$this->prepareSmallWhiteImage(true));
                        }
                        $diff--;
                    }
                }
                $preparedRow[] = $photos;
                if($rowKey <= 1 && $key == 0){
                    $preparedRow[] = $logoAreaWhiteSpace;
                }
            }

            $preparedStaffImages[$rowKey] = call_user_func_array('array_merge', $preparedRow);
            $preparedRow = [];
        }


        //Add one more row for logo if staff has only one
        if(count($preparedStaffImages) == 1){
            $count =  $this->smallPhotosRowMaxLength;
            while($count != 0){
                $emptyRowForLogo[] = $this->prepareSmallWhiteImage(true);
                $count--;
            }

            $preparedStaffImages[++$rowKey] = $emptyRowForLogo;
        }

        return $preparedStaffImages;
    }

    /**
     * Check if small images fit normally in their area
     *
     * @param int $initialTopPosition
     *
     * @return bool
     */
    protected function isSmallPhotosFitNormally(int $initialTopPosition, $childrenCount = 0)
    {
        // Calc available height based on settings
        $availableHeight = $this->canvasHeight - $initialTopPosition - $this->smallPhotoTextBlockHeight()/2;

        // Calc needed height for current settings
        $teachersCount = count($this->srcPhotos) - $childrenCount;
        $rowsChildrenCount = ceil($childrenCount / $this->smallPhotosRowMaxLength);
        $rowsTeachersCount = ceil($teachersCount / $this->smallPhotosStaffRowMaxLength) ;
        $rowsCount = $rowsChildrenCount + ($rowsTeachersCount < 2 && $this->settings->use_school_logo ? 2 : $rowsTeachersCount);

        $neededHeight = $this->prepareSmallPhotoBlockHeight() * $rowsCount;

        return $availableHeight >= $neededHeight;
    }
}
