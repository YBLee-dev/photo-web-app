<?php


namespace App\Photos\TemplatedPhotosGeneration;


use App\Photos\People\Person;
use App\Photos\SettingsGroupPhotos\SettingsGroupPhotos;
use App\Photos\SubGalleries\SubGallery;
use App\Utils;
use Imagick;
use ImagickDraw;
use ImagickException;
use ImagickPixel;
use Laracasts\Presenter\Exceptions\PresenterException;

class IDCardsGenerator
{
    protected $school_logo;
    protected $name;
    protected $date;
    protected $image_path;
    protected $landscape_template;
    protected $portrait_template;
    protected $font;
    protected $portraitNameSize;
    protected $portraitTitleSize;
    protected $portraitYearSize;
    protected $landscapeNameSize;
    protected $landscapeTitleSize;
    protected $landscapeYearSize;

    /** @var Person */
    protected $person;

    /**
     * IDCardsGenerator constructor.
     *
     * @param Person $person
     *
     * @throws PresenterException
     */
    public function __construct(Person $person)
    {
        /** @var SettingsGroupPhotos $groupSettings */
        $groupSettings = $person->subgallery->gallery->season->groupSettings;

        $this->person = $person;

        $this->school_logo          = $groupSettings->present()->schoolLogoPath();
        $this->name                 = $person->present()->name();
        $this->date                 = $groupSettings->year;
        $this->image_path           = $person->croppedPhoto()->present()->originalUrl();
        $this->landscape_template   = $groupSettings->present()->idCardLandscapeBackgroundPath();
        $this->portrait_template    = $groupSettings->present()->idCardPortraitBackgroundPath();
        $this->font                 = $groupSettings->present()->fontPath();
        $this->portraitNameSize     = $groupSettings->id_cards_portrait_name_size;
        $this->portraitTitleSize    = $groupSettings->id_cards_portrait_title_size;
        $this->portraitYearSize     = $groupSettings->id_cards_portrait_year_size;
        $this->landscapeNameSize     = $groupSettings->id_cards_landscape_name_size;
        $this->landscapeTitleSize    = $groupSettings->id_cards_landscape_title_size;
        $this->landscapeYearSize     = $groupSettings->id_cards_landscape_year_size;
    }

    /**
     * Generate landscape image & return path
     *
     * @return Imagick
     * @throws ImagickException
     */
    public function getLandscapeImage()
    {
        $logoImage = $this->prepareLogoImage($this->school_logo);
        $nameDraw = $this->generateNameDraw($this->name, 625, 300, $this->landscapeNameSize);
        $textDraw = $this->generateStaffDraw($this->getTitle(), 387, 810, $this->landscapeTitleSize);
        $yearDraw = $this->generateYearDraw($this->date, 1025, 810, $this->landscapeYearSize);
        $smallImage = $this->prepareSmallImage($this->image_path);

        $idCard = $this->generateIdCardLandscape(
            $this->landscape_template,
            $logoImage,
            $nameDraw,
            $textDraw,
            $yearDraw,
            $smallImage
        );

        return $idCard;
    }

    /**
     * @param string $fullLocalPath
     *
     * @return bool
     * @throws ImagickException
     */
    public function generateAndSaveLandscapeImage(string $fullLocalPath)
    {
        $imagick = $this->getLandscapeImage();
        $status = $imagick->writeImage($fullLocalPath);

        // Clear resources
        $imagick->clear();

        return $status;
    }

    /**
     * @param string $fullLocalPath
     *
     * @return bool
     * @throws ImagickException
     */
    public function generateAndSavePortraitImage(string $fullLocalPath)
    {
        return $this->getPortraitImage()->writeImage($fullLocalPath);
    }

    /**
     * Generate landscape image
     *
     * @param $baseTemplate
     * @param $logoImage
     * @param $nameDraw
     * @param $textDraw
     * @param $yearDraw
     * @param $imagePhoto
     * @return Imagick
     * @throws ImagickException
     */
    protected function generateIdCardLandscape($baseTemplate, $logoImage, $nameDraw, $textDraw, $yearDraw, $imagePhoto)
    {
        $imagick = new Imagick($baseTemplate);
        $imagick->setImageFormat("jpg");

        $imagick->compositeImage($logoImage, Imagick::COMPOSITE_ATOP, (775 - $logoImage->getImageWidth())/2, 150);
        $imagick->compositeImage($nameDraw, Imagick::COMPOSITE_ATOP, (775 - $nameDraw->getImageWidth())/2, 430);

        $imagick->drawImage($textDraw);
        $imagick->drawImage($yearDraw);

        $imagick->compositeImage($imagePhoto, Imagick::COMPOSITE_ATOP, 775, 50);

        return $imagick->getImage();
    }

    /**
     * Generate portrait image & return path
     *
     * @return mixed|string
     * @throws ImagickException
     */
    public function getPortraitImage()
    {
        $logoImage = $this->prepareLogoImage($this->school_logo);
        $nameDraw = $this->generateNameDraw($this->name, 500, 280, $this->portraitNameSize);

        $textDraw = $this->generateStaffDraw($this->getTitle(), 450, 930, $this->portraitTitleSize);
        $yearDraw = $this->generateYearDraw($this->date, 450, 1010, $this->portraitYearSize);
        $smallImage = $this->prepareSmallImage($this->image_path);

        $idCard = $this->generateIdCardPortrait(
            $this->portrait_template,
            $logoImage,
            $nameDraw,
            $textDraw,
            $yearDraw,
            $smallImage
        );

        return $idCard;
    }

    /**
     * Prepare staff title
     *
     * @return string|null
     */
    protected function getTitle()
    {
        return $this->person->title ?? 'Staff';
    }

    /**
     * Generate landscape image
     *
     * @param $baseTemplate
     * @param $logoImage
     * @param $nameDraw
     * @param $textDraw
     * @param $yearDraw
     * @param $imagePhoto
     * @return Imagick
     * @throws ImagickException
     */
    protected function generateIdCardPortrait($baseTemplate, $logoImage, $nameDraw, $textDraw, $yearDraw, $imagePhoto)
    {
        $imagick = new Imagick($baseTemplate);
        $imagick->setImageFormat("jpg");

        $imagick->compositeImage($imagePhoto, Imagick::COMPOSITE_ATOP, 200, 50);
        $imagick->compositeImage($nameDraw, Imagick::COMPOSITE_ATOP, 200, 610);
        $imagick->drawImage($textDraw);
        $imagick->drawImage($yearDraw);
        $imagick->compositeImage($logoImage, Imagick::COMPOSITE_ATOP, ($imagick->getImageWidth() - $logoImage->getImageWidth())/2, 1050);

        return $imagick->getImage();
    }

    /**
     * Logo generate
     *
     * @param $imagePath
     * @return Imagick
     * @throws ImagickException
     */
    protected function prepareLogoImage($imagePath)
    {
        $imagePhoto = new Imagick($imagePath);
        $imagePhoto->resizeimage(400, 300, Imagick::FILTER_LANCZOS, 1.0, true);

        return $imagePhoto;
    }

    /**
     * Draw person name
     *
     * @param string $name
     * @param $height
     * @param $weight
     * @param $fontSize
     * @return Imagick
     * @throws ImagickException
     */
    protected function generateNameDraw(string $name, $weight, $height, $fontSize)
    {
        $imagePhoto = new Imagick();
        $imagePhoto->newImage($weight, $height, new ImagickPixel('transparent'));
        $imagePhoto->setImageFormat('png');

        $draw = $this->generateTextDraw('', $fontSize, 0, 0);

        $strArray = explode(" ",$name);

        $text_y = 130;
        foreach ($strArray as $line){
            if(empty(trim($line))){
                continue;
            }
            $imagePhoto->annotateImage($draw, $weight/2, $text_y, 0, $line);
            $text_y += $fontSize;
        }

        $imagePhoto->resetIterator();
        $combined = $imagePhoto->appendImages(true);

        return $combined;
    }


    /**
     * Draw year
     *
     * @param string $date
     * @param $x
     * @param $y
     * @param $fontSize
     * @return ImagickDraw
     */
    protected function generateYearDraw(string $date, $x, $y, $fontSize)
    {
        return $this->generateTextDraw($date, $fontSize, $x, $y);
    }

    /**
     * Draw staff text
     *
     * @param string $text
     * @param $x
     * @param $y
     * @param $fontSize
     * @return ImagickDraw
     */
    protected function generateStaffDraw(string $text, $x, $y, $fontSize)
    {
        return $this->generateTextDraw($text, $fontSize, $x, $y);
    }

    /**
     * Generate Text by config
     *
     * @param string $text
     * @param $fontSize
     * @param $x
     * @param $y
     * @return ImagickDraw
     */
    protected function generateTextDraw(string $text, $fontSize, $x, $y)
    {
        $draw = new ImagickDraw();
        $draw->setStrokeWidth(1.2);
        $draw->setFillColor('#000000');
        $draw->setTextAlignment(Imagick::ALIGN_CENTER);

        $draw->setFontSize($fontSize);
        $draw->setFont($this->font);

        $draw->annotation($x, $y, $text);

        return $draw;
    }

    /**
     * Prepare person image
     *
     * @param string $imagePath
     * @return Imagick
     * @throws ImagickException
     */
    protected function prepareSmallImage(string $imagePath)
    {
        $imagePhoto = new Imagick($imagePath);
        $imagePhoto->resizeimage(500, 720, Imagick::FILTER_LANCZOS, 1.0, true);

        return $imagePhoto;
    }
}
