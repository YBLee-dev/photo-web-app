<?php

namespace App\Photos\People;

use App\Photos\Galleries\Gallery;
use Laracasts\Presenter\Exceptions\PresenterException;
use App\Photos\AdditionalClassrooms\AdditionalClassroomsRepo;

class PersonService
{
    /**
     * @param Gallery $gallery
     *
     * @throws PresenterException
     */
    public function createPeopleFromGalleryProcessingDirectory(Gallery $gallery)
    {
        $subGalleries = $gallery->subgalleries;
        $factory = (new PersonFactory());

        foreach ($subGalleries as $subGallery) {
            $factory->createFromFile($subGallery->present()->mainPhotoUploadedLocalPath(), $subGallery);
        }
    }

    /**
     * Get all available teachers prefix
     *
     * @return array
     */
    public function getTeacherPrefixes()
    {
        $prefixes = [
            "mr.",
            "mister",
            "mrs.",
            "misses",
            "ms.",
            "miss",
            "dr.",
            "director",
            "principal",
            "asst.",
            "assistant",
            "pastor",
        ];

        return $prefixes;
    }

    /**
     * Check is name has teacher prefix
     *
     * @param $name
     * @return bool|string
     */
    public function isTeacher($name)
    {
        $prefixes = $this->getTeacherPrefixes();
        $found = false;
        foreach ($prefixes as $p) {
            if (strpos(strtolower($name), strtolower($p)) !== false) {
                $found = ucfirst($p);
                break;
            }
        }

        return $found;
    }

    /**
     * Update additional classrooms info for Person
     *
     * @param array $classrooms
     * @param \App\Photos\People\Person $person
     * @throws \Exception
     */
    public function updateAdditionalClassrooms(array $classrooms, Person $person)
    {
        $person->additionalClassrooms()->delete();
        $classroomsRepo = new AdditionalClassroomsRepo();

        foreach ($classrooms as $name) {
            $classrooms[] = $classroomsRepo->create([
                'person_id' => $person->id,
                'sub_gallery_id' => $person->sub_gallery_id,
                'name' => $name,
            ]);
        }
    }
}
