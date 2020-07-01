<?php

namespace App\Photos\SettingsGroupPhotos;


class SettingsGroupPhotosService
{
    /**
     * Return default settings
     *
     * @return array
     */
    public function getDefaultSettings() : SettingsGroupPhotos
    {
        $settings = config('project.default_settings_group_photo');

        return new SettingsGroupPhotos($settings);
    }

    /**
     * Return array of fonts
     *
     * @return array
     */
    public function getAvailableFonts() : array
    {
        $fonts = [];
        $customFontsList = scandir('fonts');
        foreach ($customFontsList as $font) {
            $pathInfo = pathinfo($font);
            if (array_key_exists('extension', $pathInfo) AND $pathInfo['extension'] == 'ttf') {
                $fonts[$font] = $font;
            }
        }

        return $fonts;
    }

    /**
     * Return array af naming structure
     *
     * @return array
     */
    public function getAvailableNamingStructures() : array
    {
        return config('project.available_naming_structures');
    }
}
