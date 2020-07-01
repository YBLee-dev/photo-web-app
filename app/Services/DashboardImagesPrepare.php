<?php

namespace App\Services;

use Illuminate\Http\Request;

class DashboardImagesPrepare
{
    /**
     * Save images from validated data to directory
     *
     * @param array  $data
     * @param array  $imageFieldsNames
     * @param string $path
     *
     * @return array
     */
    public function saveImagesInDirectory(array $data, array $imageFieldsNames, $path = '')
    {
        foreach ($imageFieldsNames as $fieldName) {
            $fieldUpdateKey = $fieldName.'0';

            if (array_has($data, $fieldName)) {
                $file = request()->file($fieldUpdateKey);
                $real_file_name = $file->getClientOriginalName();
                $file_name = str_replace(' ', '-', $real_file_name);
                $file_name = uniqid()."_$file_name";

                $full_file_name = $file->move(public_path($path), $file_name)->getFilename();

                $data[$fieldName] = $full_file_name;
            }
        }

        return $data;
    }





}
