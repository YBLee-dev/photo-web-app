<?php

namespace App;


class Utils
{
    /*
     * Prepare angular page
     */
    public static function getAngularPage()
    {
        $path = public_path('dist/index.html');
        return file_get_contents($path);
    }

    /**
     * Prepare directory with nested directories if not exists
     *
     * @param string $path
     */
    public static function prepareDir(string $path)
    {
        $directoryPath = dirname($path);

        if (!file_exists($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }
    }

    /**
     * Generate simple number password
     *
     * @return string
     */
    public static function generateSimpleNumberPassword()
    {
        return str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
    }

    /**
     * @param string ...$params
     *
     * @return string
     */
    public static function prepareRequestParamsString(string ... $params)
    {
        $str = '';

        foreach ($params as $param){
            $value = request()->get($param,  null);

            if(is_null($value)){
                continue;
            }

            if(is_string($value)){
                $str .= "$param=$value&";
                continue;
            }

            if(is_array($value)){
                $str .= "{$param}[]=".implode("&{$param}[]=", $value);
                continue;
            }
        }

        return $str ? str_start($str, '?&') : '';
    }
}
