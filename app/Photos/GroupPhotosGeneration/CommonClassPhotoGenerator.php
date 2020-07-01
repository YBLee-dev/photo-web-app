<?php


namespace App\Photos\GroupPhotosGeneration;


use App\Photos\People\Person;
use App\Photos\SettingsGroupPhotos\SettingsGroupPhotos;
use Imagick;
use ImagickDraw;
use ImagickException;
use ImagickPixel;
use Laracasts\Presenter\Exceptions\PresenterException;

class CommonClassPhotoGenerator
{
    /** @var string */
    protected $galleryName;

    /** @var string */
    protected $classroomName;

    /** @var string */
    protected $year;

    /** @var SettingsGroupPhotos */
    protected $settings;

    /** @var array Source photos data */
    protected $srcPhotos = [];

    /** @var int Small image width in pixels */
    protected $smallImageWidth = 270;

    /** @var int Small image width in pixels */
    protected $smallImageHeight = 342;

    /** @var int  */
    protected $photosIndentSize = 25; // px

    /** @var int Max count of small photos in one row */
    protected $smallPhotosRowMaxLength = 9;

    /** @var int Photos signature block height */
    protected $smallPhotoTextLineHeight; // px

    /** @var int Indentation from left side for small photos block */
    protected $indentTop = 575; // px

    /** @var int Indentation from left side for small photos block */
    protected $indentLeft = 82; // px

    /** @var array */
    protected $currentPhotosMap;

    /** @var array Maps for photos placing */
    protected $photosMaps = [
        40 => [10, 10, 10, 10],
        39 => [10, 10, 10, 9],
        38 => [10, 10, 9, 9],
        37 => [10, 9, 9, 9],
        36 => [10, 9, 9, 8],
        35 => [10, 9, 8, 8],
        34 => [10, 9, 8, 7],
        33 => [10, 9, 8, 6],
        32 => [10, 8, 7, 7],
        31 => [10, 8, 7, 6],
        30 => [10, 8, 6, 6],
        29 => [10, 8, 6, 5],
        28 => [9, 9, 9],
        27 => [9, 9, 9],
        26 => [9, 9, 8],
        25 => [9, 8, 8],
        24 => [8, 8, 8],
        23 => [8, 8, 7],
        22 => [8, 7, 7],
        21 => [7, 7, 7],
        20 => [7, 7, 6],
        19 => [7, 6, 6],
        18 => [6, 6, 6],
        17 => [6, 6, 5],
        16 => [6, 5, 5],
        15 => [5, 5, 5],
        14 => [5, 5, 4],
        13 => [5, 5, 3],
        12 => [5, 4, 3],
        11 => [5, 4, 2],
        10 => [5, 3, 2],
        9  => [4, 3, 2],
        8 => [4, 4],
        7 => [4, 3],
        6 => [4, 2],
        5 => [3, 2],
        4 => [2, 2],
        3 => [3],
        2 => [2],
        1 => [1],
    ];

    /** @var int Photos signature font height */
    protected $photoNameFontSize; // px

    /** @var int canvas width */
    protected $canvasWidth = 3000; //px

    /** @var int  */
    protected $canvasHeight = 2400; //px

    /** @var int  */
    protected $smallImagesCornersRoundRadius = 35; //px

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
        $this->galleryName = $galleryName;
        $this->classroomName = $classroomName;
        $this->year = $year;
        $this->settings = $settings;
        $this->smallPhotoTextLineHeight = $settings->name_font_size * 2;
        $this->photoNameFontSize = $settings->name_font_size;
    }

    /**
     * Add person photo to collection
     *
     * @param string $photoPath
     * @param Person $person
     *
     * @throws PresenterException
     */
    public function addPhoto(string $photoPath, Person $person)
    {
        $this->srcPhotos[] = [
            'client' => $person, //person class
            'prepared_name' => $person->teacher ? $person->present()->nameWithTitle() : $person->present()->name(), //person first name + last name
            'name_for_sort' => $person->present()->firstNameClear(), // clear Mr, Miss in firt name of person
            'photo_path' => $photoPath,
            'staff' => $person->isStaff(), //is teacher
            'position' => $person->position
        ];
    }

    /**
     * Save result photo
     *
     * @param string $path
     *
     * @return string
     * @throws ImagickException
     * @throws PresenterException
     */
    public function saveResultPhoto(string $path): string
    {
        $classPhoto = $this->generateResultPhoto();

        $classPhoto->writeImage($path);

        // Clear resources for correct work in queues
        $classPhoto->clear();

        return $path;
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

        return $this->generateClassPhoto($schoolDraw, $classroomDraw, $yearDraw, $this->settings->present()->classBackgroundPath());
    }

    /**
     * Prepare images for photos
     *
     * @return array
     * @throws ImagickException
     * @throws PresenterException
     */
    protected function preparePhotos(): array
    {
        $preparedPhotos = [];

        if(count($this->srcPhotos) > 0){
            $this->sortByNameStaffAndPosition();

            $dataWithPreparedNames = $this->prepareNames();
            foreach ($dataWithPreparedNames as $photo){
                $preparedPhotos[] = $this->prepareSmallImageWithText(
                    $photo['photo_path'],
                    $photo['prepared_name'],
                    $photo['staff'],
                    $this->settings->present()->fontPath()
                );
            }
        }

        return $preparedPhotos;
    }

    /**
     * Sorting person photos by name and position,
     * and set staff first
     */
    protected function sortByNameStaffAndPosition()
    {
        $staff  = array_column($this->srcPhotos, 'staff');
        $name_for_sort = array_column($this->srcPhotos, 'name_for_sort');
        $position = array_column($this->srcPhotos, 'position');

        $lastPosition = min($position);
        foreach ($position as $key => $positionValue){
            if(is_null($positionValue)){
                // We set minimal position to not change sorting order if position is not set
                $position[$key] = $lastPosition;
            }
        }

        array_multisort( $staff, SORT_DESC, $position, SORT_DESC, $name_for_sort, SORT_ASC, $this->srcPhotos);
    }

    /**
     * Extend repeat names
     *
     * @return array
     */
    protected function prepareNames(): array
    {
        $result = [];

        $personsDetails = collect($this->srcPhotos)->groupBy('prepared_name');

        foreach ($personsDetails as $group){
            if ($group->count() > 1){
                foreach ($group as $person) {
                    if (! $person['staff']) {
                        $person['prepared_name'] = $person['client']->present()->prepareFullName();
                    }
                    $result[] = $person;
                }
            } else {
                $result[] = $group[0];
            }
        }

        return $result;
    }


    /**
     * Set all images and text on class background and generate image composition
     *
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
    protected function generateClassPhoto(
        ImagickDraw $schoolDraw,
        ImagickDraw $classroomDraw,
        ImagickDraw $yearDraw,
        string $backgroundPath
    ): Imagick {
        $imagick = $this->prepareBackgroundImage($backgroundPath);

        $imagick->drawImage($schoolDraw);
        $imagick->drawImage($classroomDraw);
        $imagick->drawImage($yearDraw);

        $this->updateSmallPhotoSizesIfNeeded($this->indentTop);

        $preparedImages = $this->preparePhotos();

        $imagick = $this->composeSmallPhotos(
            $imagick, //classroom photo's background
            $preparedImages, // person's photo
            $this->indentLeft,
            $this->indentTop
        );

        return $imagick->getImage();
    }

    /**
     *
     *
     * @return bool
     */
    protected function updateSizesByMapIfPossible()
    {
        $photosCount = count($this->srcPhotos);
        if(!$this->isMapExistsForCurrentPhotos()) {
            // We have no map for such count and do nothing for this case
            return false;
        }

        // Get available space
        $availableHeight = $this->allSmallPhotosBlockHeight($this->indentTop);
        $additionalWidth = $this->allSmallPhotosBlockWidth();

        // Get map
        $map = data_get($this->photosMaps,$photosCount, array_first($this->photosMaps));
        // Set map
        $this->currentPhotosMap = $map;

        $rowsCount = count($map);
        $maxRowCount = max($map);

        // Calculate available sizes
        $smallPhotoBlockMaxWidth = round($additionalWidth / $maxRowCount, 0);
        $smallPhotoBlockMaxHeight = round($availableHeight / $rowsCount, 0);

        // Update sizes
        // Get scaling coefficient
        $currentSmallPhotoBlockWidth = $this->prepareSmallPhotoBlockWidth();
        $widthBasedScalingCoefficient = $smallPhotoBlockMaxWidth / $currentSmallPhotoBlockWidth;

        $currentSmallPhotoBlockHeight = $this->prepareSmallPhotoBlockHeight();
        $heightBasedScalingCoefficient = $smallPhotoBlockMaxHeight / $currentSmallPhotoBlockHeight;

        $optimalScalingCoefficient = min($widthBasedScalingCoefficient, $heightBasedScalingCoefficient);

        // Update row max length
        $this->smallPhotosRowMaxLength = $maxRowCount;

        // Scale based on map and current size
        $this->smallImageHeight = $this->smallImageHeight * $optimalScalingCoefficient;
        $this->smallImageWidth = $this->smallImageWidth * $optimalScalingCoefficient;

        $this->smallPhotoTextLineHeight = $this->smallPhotoTextLineHeight * $optimalScalingCoefficient;
        $this->photoNameFontSize = $this->photoNameFontSize * $optimalScalingCoefficient;

        // Update indentation for centering
        $this->updateSmallPhotosBlockLeftIndent();

        return true;
    }

    /**
     * Update left indent based on current params
     */
    protected function updateSmallPhotosBlockLeftIndent()
    {
        $updatedAllSmallPhotosBlockWidth = $this->allSmallPhotosBlockWidth();
        $additionalWidth = $this->canvasWidth - $this->indentLeft - $updatedAllSmallPhotosBlockWidth;
        $this->indentLeft = $additionalWidth / 2;
    }

    /**
     * Check if we have map for such photos count
     *
     * @return bool
     */
    protected function isMapExistsForCurrentPhotos()
    {
        $photosCount = count($this->srcPhotos);

        return isset($this->photosMaps[$photosCount]);
    }

    /**
     * @param string $backgroundPath
     *
     * @return Imagick
     * @throws ImagickException
     */
    protected function prepareBackgroundImage(string $backgroundPath)
    {
        $imagick = new Imagick($backgroundPath);

        // Resize background to needed size
        $imagick->resizeImage($this->canvasWidth, $this->canvasHeight, imagick::FILTER_LANCZOS, 0.9, true);

        return $imagick;
    }

    /**
     * Update images sizes to make them best fitted
     *
     * @param int $initialTopPosition
     *
     * @throws \Exception
     */
    protected function updateSmallPhotoSizesIfNeeded(int $initialTopPosition)
    {
        // Try to updated by map first
        $wasUpdatedByPhotosMap = $this->updateSizesByMapIfPossible();
        if($wasUpdatedByPhotosMap) {
            return;
        }

        // Do automatic sizes corrections if updating by map is not available
        $maxOptimizationIterations = 10;

        $counter = 0;

        if(count($this->srcPhotos) <= $this->smallPhotosRowMaxLength){
            return;
        }

        $lastRowCount = count($this->srcPhotos) % $this->smallPhotosRowMaxLength;
        while ((!$this->isSmallPhotosFitNormally($initialTopPosition) || $lastRowCount == 1) && (ceil(count($this->srcPhotos) / $this->smallPhotosRowMaxLength) > 1)) {
            $this->makeOneRowLess();

            if($counter > $maxOptimizationIterations) {
                throw new \Exception("Small photo sizes calculation processed more than $maxOptimizationIterations times");
            }
        }

    }

    /**
     * Update small photo sizes to make them best fit for photos
     */
    protected function makeOneRowLess()
    {
        // Calc available width
        $imageWithIndentWidth = $this->prepareSmallPhotoBlockWidth();
        $availableWidth = $imageWithIndentWidth * $this->smallPhotosRowMaxLength;

        // Calculate needed row width
        $photosCount = count($this->srcPhotos);
        $rowsCount = ceil($photosCount / $this->smallPhotosRowMaxLength);
        $lastRowCount = $photosCount % $rowsCount ? $this->smallPhotosRowMaxLength * ($photosCount % $rowsCount) : $this->smallPhotosRowMaxLength;
        $shouldBeAddedToRows = ceil($lastRowCount/($rowsCount - 1));

        $neededWidth = $availableWidth + $imageWithIndentWidth * $shouldBeAddedToRows;

        // Calculate coefficient
        $coefficient =  $availableWidth / $neededWidth;

        $this->updateSmallPhotoDataByCoefficient($coefficient);

        $this->smallPhotosRowMaxLength = (int)($this->smallPhotosRowMaxLength + $shouldBeAddedToRows);
    }

    /**
     * Update small photo sizes to make them best fit for photos
     */
    protected function makeOneRowLessByAddedToRows($shouldBeAddedToRows = 1)
    {
        // Calc available width
        $availableWidth = $this->allSmallPhotosBlockWidth();

        $imageWithIndentWidth = $this->prepareSmallPhotoBlockWidth();
        $neededWidth = $availableWidth + $imageWithIndentWidth * $shouldBeAddedToRows;

        // Calculate coefficient
        $coefficient =  $availableWidth / $neededWidth;

        $this->updateSmallPhotoDataByCoefficient($coefficient);

        $this->smallPhotosRowMaxLength = (int)($this->smallPhotosRowMaxLength + $shouldBeAddedToRows);
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
        $availableHeight = $this->allSmallPhotosBlockHeight($initialTopPosition);

        // Calc needed height for current settings
        $photosCount = count($this->srcPhotos);
        $rowsCount = ceil($photosCount / $this->smallPhotosRowMaxLength);
        $neededHeight = $this->prepareSmallPhotoBlockHeight() * $rowsCount;

        return $availableHeight >= $neededHeight;
    }

    /**
     * Calculate available width for all small photos
     *
     * @return float|int
     */
    protected function allSmallPhotosBlockWidth()
    {
        $imageWithIndentWidth = $this->prepareSmallPhotoBlockWidth();

        // Remove one indent for correct geometry
        $imageWithIndentWidth -= $this->photosIndentSize;

        return $imageWithIndentWidth * $this->smallPhotosRowMaxLength;
    }

    /**
     * Calculate available height for all small photos
     *
     * @param $initialTopPosition
     *
     * @return int
     */
    protected function allSmallPhotosBlockHeight($initialTopPosition)
    {
        $bottomIndent = $this->bottomIndent();
        return $this->canvasHeight - $initialTopPosition - $bottomIndent;
    }

    /**
     * Return bottom indent for small photos block
     *
     * @return float|int
     */
    protected function bottomIndent()
    {
        return $this->smallImageHeight / 4;
    }

    /**
     * Update small images parameters by coefficient for fir normally
     *
     * @param $coefficient
     */
    protected function updateSmallPhotoDataByCoefficient($coefficient)
    {
        // Update settings with new data
        $this->smallImageWidth = (int)($this->smallImageWidth * $coefficient);
        $this->smallImageHeight = (int)($this->smallImageHeight * $coefficient);
        $this->photosIndentSize = (int)($this->photosIndentSize * $coefficient);
        $this->smallImagesCornersRoundRadius = (int)($this->smallImagesCornersRoundRadius * $coefficient);
        $this->photoNameFontSize = (int)($this->photoNameFontSize * $coefficient);
        $this->smallPhotoTextLineHeight = (int)($this->smallPhotoTextLineHeight * $coefficient);
    }

    /**
     * @param Imagick $canvas
     * @param array   $photos
     * @param int     $initialLeftIndent - px
     * @param int     $initialTopIndent  - px
     *
     * @return Imagick
     * @throws ImagickException
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

        // Split photos according to map if needed
        if(isset($this->currentPhotosMap)) {
            $splitPhotos = $this->splitPhotosByMap($photos, $this->currentPhotosMap);
        } else {
            $splitPhotos = array_chunk($photos, $this->smallPhotosRowMaxLength);
        }

        foreach ($splitPhotos as $row) {
            // Prepare position for not full row
            $rowLength = count($row);
            if ($rowLength < $this->smallPhotosRowMaxLength) {
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

        return $canvas;
    }

    /**
     * Place photos according to the map
     *
     * @param array $photos
     * @param array $map
     *
     * @return array
     */
    protected function splitPhotosByMap(array $photos, array $map)
    {
        $splitPhotos = [];
        foreach ($map as $row => $count) {
            for ($i = $count; $i > 0; $i--) {
                $splitPhotos[$row][] = array_shift($photos);
            }
        }

        return  $splitPhotos;
    }

    /**
     * @return float|int
     */
    protected function prepareSmallPhotoBlockWidth()
    {
        return $this->smallImageWidth + $this->photosIndentSize * 2;
    }


    /**
     * @return float|int
     */
    protected function prepareSmallPhotoBlockHeight()
    {
        return $this->smallImageHeight + $this->photosIndentSize + $this->smallPhotoTextBlockHeight();
    }

    /**
     * @return float|int
     */
    protected function smallPhotoTextBlockHeight()
    {
        return $this->smallPhotoTextLineHeight * 2;
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
            $this->settings->year_font_size,
            $this->settings->present()->fontPath(),
            1500,
            500
        );
    }

    /**
     * Generate classroom draw
     *
     * @return ImagickDraw
     * @throws PresenterException
     */
    protected function generateClassroomDraw(): ImagickDraw
    {
        return $this->generateTextDraw(
            $this->classroomName != 'without classroom' ? $this->classroomName : '',
            $this->settings->class_name_font_size,
            $this->settings->present()->fontPath(),
            1500,
            400
        );
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
            $this->galleryName, //be created when create class object
            $this->settings->school_name_font_size,
            $this->settings->present()->fontPath(),
            1500,
            300
        );
    }

    /**
     * Generate Text by config
     *
     * @param string   $text
     * @param int      $fontSize
     * @param          $font
     * @param int      $x
     * @param int      $y
     *
     * @return ImagickDraw
     */
    protected function generateTextDraw(
        string $text,
        int $fontSize,
        $font,
        int $x,
        int $y)
    {
        $draw = new ImagickDraw();
        $draw->setStrokeWidth(1.2);
        $draw->setFillColor('#000000');
        $draw->setTextAlignment(Imagick::ALIGN_CENTER);
        $draw->setFontSize($fontSize);
        $draw->setFont($font);

        $draw->annotation($x, $y, $text);

        return $draw;
    }

    /**
     * Generate small image with name by config
     *
     * @param string $imagePath
     * @param string $personName
     * @param bool $isStaff
     * @param        $nameFont
     *
     * @return array
     * @throws \ImagickException
     */
    protected function prepareSmallImageWithText(string $imagePath, string $personName, bool $isStaff, $nameFont): array
    {
        // Prepare photo
        $imagick = new Imagick($imagePath);
        $imagick->resizeimage($this->smallImageWidth, $this->smallImageHeight, Imagick::FILTER_LANCZOS, 1.0, true);

        // Function deprecated
        @$imagick->roundCorners($this->smallImagesCornersRoundRadius, $this->smallImagesCornersRoundRadius);

        $imagick->newImage($this->smallImageWidth, $this->smallPhotoTextBlockHeight(), new ImagickPixel('transparent'));
        $imagick->setImageFormat('png');

        // Prepare text block part
        $imagick = $this->addName($imagick, $nameFont, $personName, $this->smallImageWidth, true, null, null, null, $isStaff);

        $imagick->resetIterator();
        $combined['image'] = $imagick->appendImages(true);
        $combined['staff'] = $isStaff;

        return $combined;
    }

    /**
     * @param Imagick  $imagick
     * @param          $nameFont
     * @param string   $personName
     * @param int|null $maxWidth
     * @param bool     $secondLineWrapAvailable
     *
     * @param int|null $nameFontSize
     * @param int|null $textLineHeight
     *
     * @param int|null $textBlockTopPosition
     * @param int|null $imageWidth
     *
     * @return Imagick
     * @throws ImagickException
     */
    protected function addName(
        Imagick $imagick,
        $nameFont,
        string $personName,
        int $maxWidth = null,
        bool $secondLineWrapAvailable = false,
        int $nameFontSize = null,
        int $textLineHeight = null,
        int $imageWidth = null,
        bool $isStaff = false
    )
    {
        $photoNameFontSize = $nameFontSize ?? $this->photoNameFontSize;
        $imageWidth = $imageWidth ?? $this->smallImageWidth;
        // Use given line height or prepare based on font size and indentation
        $textLineHeight = $textLineHeight ?? $photoNameFontSize * 1.2;

        // On image center
        $textCenterPoint = $imageWidth / 2;
        // First line center point
        $firstLineHeightCenterPoint = $textLineHeight;

        $textDraw = $this->generateTextDraw('', $photoNameFontSize, $nameFont,0,0);

        // Try to split name
        if ($isStaff || ($secondLineWrapAvailable && !$this->isTextWidthFit($textDraw, $personName, $maxWidth))) {
            $parts = explode(' ', trim($personName));

            if(count($parts) > 0){

                // First line center point
                $lineHeightCenterPoint = $firstLineHeightCenterPoint;

                foreach ($parts as $key => $part){
                    if(empty(trim($part))){
                        continue;
                    }
                    if($key != 0){
                        $lineHeightCenterPoint += $textLineHeight;
                    }

                    $imagick = $this->placeTextWithAlignBasedOnMaxSize(
                        $imagick,
                        $textDraw,
                        $textCenterPoint,
                        $lineHeightCenterPoint,
                        trim($part),
                        $maxWidth
                    );
                }
                return  $imagick;
            }

        }

        $imagick = $this->placeTextWithAlignBasedOnMaxSize($imagick, $textDraw, $textCenterPoint, $firstLineHeightCenterPoint, $personName, $maxWidth);

        return $imagick;
    }

    /**
     * @param Imagick     $imagick
     * @param ImagickDraw $textDraw
     * @param int         $textCenterPoint
     * @param int         $textHeightCenterPoint
     * @param string      $text
     * @param int         $maxWidth
     *
     * @return Imagick
     * @throws ImagickException
     */
    protected function placeTextWithAlignBasedOnMaxSize(Imagick $imagick, ImagickDraw $textDraw, int $textCenterPoint, int $textHeightCenterPoint, string $text, int $maxWidth = null)
    {
        if ($maxWidth && !$this->isTextWidthFit($textDraw, $text, $maxWidth)) {
            $textDraw->setTextAlignment(Imagick::ALIGN_LEFT);
            $textCenterPoint = 0;
        } else {
            $textDraw->setTextAlignment(Imagick::ALIGN_CENTER);
        }

        $imagick->annotateImage($textDraw, $textCenterPoint, $textHeightCenterPoint, 0, $text);

        return $imagick;
    }

    /**
     * @param ImagickDraw $textDraw
     * @param string      $text
     * @param int         $maxWidth
     *
     * @return bool
     * @throws ImagickException
     */
    protected function isTextWidthFit(ImagickDraw $textDraw, string $text, int $maxWidth = null)
    {
        if(is_null($maxWidth)) {
            return  true;
        }

        $textPlacingMetric = (new Imagick())->queryFontMetrics($textDraw, $text);

        return $textPlacingMetric['textWidth'] <= $maxWidth;
    }
}
