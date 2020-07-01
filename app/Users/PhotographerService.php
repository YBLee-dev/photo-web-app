<?php

namespace App\Users;

use App\Core\StorageManager;
use App\Jobs\UpdatePermissionsOnStorage;
use Illuminate\Support\Facades\Storage;

class PhotographerService
{
    /**
     * @param User $user
     *
     * @return array
     * @throws \Exception
     */
    public function getUnprocessedGallery(User $user): array
    {
        if (empty($user->ftp_login)) {
            return [];
        }

        $directories = Storage::disk('uploads')->directories($user->ftp_login);

        foreach ($directories as $key => $directory) {
            // Don't add gallery which is in moving process
            if($this->isDirectoryMovingInProgress($directory)){
                continue;
            }

            $galleries[] = [
                'id' => md5($directory),
                'gallery_name' => $this->getDirNameFromPath($directory),
                'user_id' => $user->id,
                'user_email' => $user->email,
            ];
        }

        return $galleries ?? [];
    }

    /**
     * @param string $directory
     *
     * @return bool
     * @throws \Exception
     */
    protected function isDirectoryMovingInProgress(string $directory): bool
    {
        return (new StorageManager())->isUploadedDirectoryMoving($directory);
    }

    /**
     * Check directory structure
     *
     * @param $directory
     * @return array
     */
    public function checkDirectoryStructure($directory, $ftp_user)
    {
        $storage = (new StorageManager())->getLocalUploadsStorage();

        //Check permissions on dir
        try {
            $storage->allFiles($directory);
        }
        catch (\Exception $e) {
            UpdatePermissionsOnStorage::dispatch($ftp_user);
            $errors['No permissions for user'] = $ftp_user;
        }

        $errorsInfo = config('project.structure_errors');
        $errorsExist = false;

        //Check if in school dir exists other files except folders
        $files = $storage->files($directory);
        if (! empty($files)) {
            $unwanted_files = [];
            foreach ($files as $file_name){
                $unwanted_files[] = $file_name;
            }
            $errorsInfo['unsatisfactory_files']['files'] = $unwanted_files;
            $errorsExist = true;
        }

        $directories = $storage->directories($directory);

        //Check if school not empty
        if (empty($directories)) {
            $errorsInfo['empty_directories']['files'] = $directory;
            $errorsExist = true;
        }

        foreach ($directories as $subdirectory) {

            //Check if subgallery has folders
            $dirsInSub = $storage->directories($subdirectory);
            if (! empty($dirsInSub)) {
                foreach ($dirsInSub as $item){
                    $errorsInfo['unsatisfactory_directories']['files'][] = $item;
                }
                $errorsExist = true;
            }

            //Check if subgallery not empty
            $files = $storage->files($subdirectory);
            if (empty($files)) {
                $errorsInfo['empty_directories']['files'][] = $subdirectory;
                $errorsExist = true;
            }
        }

        return $errorsExist ? $errorsInfo : [];
    }

    /**
     * @return array
     */
    public function getAllUnprocessedGalleries(): array
    {
        $userRepo = new UserRepo();
        $users = $userRepo->getAll();

        foreach ($users as $user) {

            $gallery = $this->getUnprocessedGallery($user);

            if (count($gallery)) {
                if (! isset($galleries)) {
                    $galleries = $gallery;
                } else {
                    $galleries = array_merge($galleries, $gallery);
                }
            }


        }

        return $galleries ?? [];
    }

    /**
     * @param $dir
     *
     * @return bool|string
     */
    public function getDirNameFromPath(string $path): string
    {
        return substr($path, strrpos($path, '/') + 1);
    }
}
