<?php
/**
 * Created by PhpStorm.
 * User: Valentina
 * Date: 12.12.2017
 * Time: 15:38
 */

namespace App\Http\ViewComposers;


use Illuminate\View\View;

class BaseStylesViewComposer
{
    /**
     * @param View $view
     */
    public function compose(View $view)
    {
        $baseStyles = file_get_contents(public_path('css/base-style.css'));

        $view->with(compact('baseStyles'));
    }
}