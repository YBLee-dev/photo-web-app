<?php

namespace App\Photos\TemplatedPhotosGeneration;

use Illuminate\Support\Facades\Storage;
use Imagick;
use ImagickDraw;

//todo check and add comments
class ProofPhotoGenerator
{
    protected $school_name;
    protected $name;
    protected $classroom;
    protected $password;
    protected $deadline;
    protected $image_path;
    protected $title;
    protected $proof_base_template;
    protected $lineIndent;

    protected $currentCoordinateY;

    /**
     * ProofingPhotosGenerator constructor.
     *
     * @param      $school_name
     * @param      $person_name
     * @param      $classroom
     * @param      $password
     * @param      $deadline
     * @param      $image_path
     * @param null $title
     */
    public function __construct($school_name, $person_name, $classroom, $password, $deadline, $image_path, $title = null)
    {
        $this->school_name = $school_name;
        $this->name = $person_name;
        $this->classroom = $classroom;
        $this->password = $password;
        $this->deadline = $deadline;
        $this->image_path = $image_path;
        $this->title = $title;

        $this->lineIndent = $title ? 250 : 300;
        // todo move to dashboard later
        $this->proof_base_template = storage_path('app/public/group-settings/templates/default/proof_base_template.jpg');
    }

    /**
     * @param string $path
     *
     * @return bool
     * @throws \ImagickException
     */
    public function saveResultImage(string $path)
    {
        $imagick = $this->getImage();
        $status = $imagick->writeImage($path);

        // Clear resources
        $imagick->clear();

        return $status;
    }

    /**
     * @return Imagick
     * @throws \ImagickException
     */
    public function getImage()
    {
        $school_draw = $this->generateSchoolNameDraw($this->school_name);
        if($this->title){
            $title_draw = $this->generateNameDraw($this->title);
        }
        $name_draw = $this->generateNameDraw($this->name);
        $part_draw = $this->generateClassroomDraw($this->classroom);
        $password_draw = $this->generatePasswordDraw($this->password);
        $date_draw = $this->generateDateDraw($this->deadline);
        $small_image = $this->prepareSmallImage($this->image_path);
        $proof = $this->generateProofs(
            $this->proof_base_template,
            $school_draw,
            $name_draw,
            $part_draw,
            $password_draw,
            $date_draw,
            $small_image,
            $title_draw ?? ''
        );

        return $proof;
    }

    /**
     * @param string $school_name
     *
     * @return ImagickDraw
     */
    protected function generateSchoolNameDraw(string $school_name)
    {
        $draw = new ImagickDraw();
        $draw->setStrokeWidth(1.2);
        $draw->setFontSize(120);
        $draw->setFillColor('#000');
        $draw->setFont(public_path("fonts/Poppins-Regular.ttf"));
        $draw->setTextAlignment(Imagick::ALIGN_CENTER);
        $str = wordwrap($school_name, 20,"\n");
        $str_array = explode("\n",$str);

        $y = 690;
        foreach($str_array as $key => $line){
            if($key > 0) {
                $y += 120;
            }

            $draw->annotation(1680, $y, $line);
        };

        $this->currentCoordinateY = $y;

        return $draw;
    }

    /**
     * @param string $name
     *
     * @return ImagickDraw
     */
    protected function generateNameDraw(string $name)
    {
        $y = $this->currentCoordinateY + $this->lineIndent;
        $this->currentCoordinateY = $y;

        $draw = new ImagickDraw();
        $draw->setStrokeWidth(1.2);
        $draw->setFontSize(170);
        $draw->setFillColor('#77907a');
        $draw->setFont(public_path("fonts/Poppins-Regular.ttf"));
        $draw->setTextAlignment(Imagick::ALIGN_CENTER);
        $draw->annotation(1680, $y, $name);

        return $draw;
    }

    /**
     * @param string $classroom
     *
     * @return ImagickDraw
     */
    protected function generateClassroomDraw(string $classroom)
    {
        $y = $this->currentCoordinateY + $this->lineIndent;
        $this->currentCoordinateY = $y;

        $fontSize = 170;
        $draw = new ImagickDraw();
        $draw->setStrokeWidth(1.2);
        $draw->setFontSize(170);
        $draw->setFillColor('#77907a');
        $draw->setFont(public_path("fonts/Poppins-Regular.ttf"));
        $draw->setTextAlignment(Imagick::ALIGN_CENTER);

        if(!$this->isTextWidthFit($draw, $classroom, 1500)){
            $coeff = 1.9;

            $parts = wordwrap($classroom, strlen($classroom)/$coeff, "\n", false);
            while( substr_count( $parts, "\n" ) > 1){
                $coeff -= 0.1;
                $parts = wordwrap($classroom, strlen($classroom)/$coeff, "\n", false);
            }

            while (!$this->isTextWidthFit($draw, $parts, 1500)){
                $fontSize *= 0.9;
                $draw->setFontSize($fontSize);
            }
            $classroom = $parts;
        }

        $draw->annotation(1680, $y, $classroom);

        return $draw;
    }

    /**
     * @param string $password
     *
     * @return ImagickDraw
     */
    protected function generatePasswordDraw(string $password)
    {
        $draw = new ImagickDraw();
        $draw->setStrokeWidth(1.2);
        $draw->setFontSize(170);
        $draw->setFillColor('#fe0000');
        $draw->setFont(public_path("fonts/Poppins-Regular.ttf"));
        $draw->annotation(1170, 2295, $password);

        return $draw;
    }

    /**
     * @param string $date
     *
     * @return ImagickDraw
     */
    protected function generateDateDraw(string $date)
    {
        $draw = new ImagickDraw();
        $draw->setStrokeWidth(1.2);
        $draw->setFontSize(170);
        $draw->setFillColor('#fe0000');
        $draw->setFont(public_path("fonts/Poppins-Regular.ttf"));
        $draw->setTextAlignment(Imagick::ALIGN_CENTER);
        $draw->annotation(1275, 3050, $date);

        return $draw;
    }

    /**
     * @param string $image_path
     *
     * @return Imagick
     * @throws \ImagickException
     */
    protected function prepareSmallImage(string $image_path)
    {
        $image_photo= new Imagick($image_path);
        $image_photo->resizeimage(740, 1130, Imagick::FILTER_LANCZOS, 1.0, true);

        return $image_photo;
    }

    /**
     * @param $proof_base_template
     * @param $school_draw
     * @param $name_draw
     * @param $part_draw
     * @param $password_draw
     * @param $data_draw
     * @param $image_photo
     *
     * @param null $title_draw
     * @return Imagick
     * @throws \ImagickException
     */
    public function generateProofs($proof_base_template, $school_draw, $name_draw, $part_draw, $password_draw, $data_draw, $image_photo, $title_draw = null)
    {
        $imagick = new Imagick($proof_base_template);
        $imagick->setImageFormat("jpg");
        $imagick->drawImage($school_draw);
        $imagick->drawImage($name_draw);
        $imagick->drawImage($part_draw);
        $imagick->drawImage($password_draw);
        $imagick->drawImage($data_draw);
        if($title_draw){
            $imagick->drawImage($title_draw);
        }
        $imagick->compositeImage($image_photo, Imagick::COMPOSITE_ATOP, 120, 500);

        return $imagick->getImage();
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
