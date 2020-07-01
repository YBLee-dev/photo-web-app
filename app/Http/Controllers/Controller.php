<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Webmagic\Core\Controllers\AjaxRedirectTrait;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, AjaxRedirectTrait;

    /**
     * Save images from validated data to directory
     *
     * @param array  $data
     * @param array  $imageFieldsKeys
     * @param string $dirPath
     *
     * @return array
     */
    public function saveFiles(array $data, array $imageFieldsKeys, string $dirPath)
    {
        foreach ($imageFieldsKeys as $fieldName) {
            $fieldUpdateKey = $fieldName.'0';

            if (array_has($data, $fieldName)) {
                $file = request()->file($fieldUpdateKey);
                $real_file_name = $file->getClientOriginalName();
                $real_file_name = uniqid()."_$real_file_name";

                $full_file_name = $file->move($dirPath, $real_file_name)->getFilename();

                $data[$fieldName] = $full_file_name;
            }
        }

        return $data;
    }
}
