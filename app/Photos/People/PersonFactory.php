<?php


namespace App\Photos\People;


use App\Photos\SubGalleries\SubGallery;

class PersonFactory
{
    /**
     * Create person from file and sub directory
     *
     * @param string     $photoPath
     * @param SubGallery $subGallery
     *
     * @return Person
     */
    public function createFromFile(string $photoPath, SubGallery $subGallery)
    {
        // Create person
        $dataFromPhoto = $this->readIPTCData($photoPath);

        // First or new for correct work when you uploaded new files to existing gallery
        $person = Person::firstOrNew([
            'first_name' => $dataFromPhoto['first_name'] ? $dataFromPhoto['first_name'] : $subGallery->name,
            'last_name' => $dataFromPhoto['last_name'],
            'classroom' => $dataFromPhoto['classroom'] ?: 'without classroom',
            'school_name' => $subGallery->gallery->school->name,
            'graduate' => $dataFromPhoto['graduate'],
            'sub_gallery_id' => $subGallery->id
        ]);

        $person['teacher'] = $person->isStaff();

        $person->save();

        return $person;
    }

    /**
     * Read meta data from files
     *
     * @param string $filePath
     *
     * @return array
     */
    public function readIPTCData(string $filePath)
    {
        // Default values
        $data = [
            'first_name' => '',
            'last_name' => '',
            'classroom' => '',
            'school_name' => '',
            'graduate' => false,
        ];

        // Read data
        try {
            getimagesize($filePath, $picinfo);

            if (isset($picinfo['APP13'])) {
                $iptc = iptcparse($picinfo["APP13"]);
                if (is_array($iptc)) {
                    $data = [
                        'first_name' => $iptc["2#090"][0] ?? $iptc["2#116"][0] ?? '',
                        'last_name' => $iptc["2#105"][0] ?? $iptc["2#116"][0] ?? '',
                        'classroom' => $iptc["2#025"][0] ?? $iptc["2#101"][0] ?? '',
                        'school_name' => $iptc["2#092"][0] ?? '',
                        'graduate' => isset($iptc["1#090"][0]) ? true : false,
                    ];
                }
            }
        } catch (\Exception $e) {
            logger($e);
        }

        return $data;
    }
}
