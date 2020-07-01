<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Module routes prefix
    |--------------------------------------------------------------------------
    |
    | This prefix use for generation all routes for module
    |
    */
    'prefix' => 'dashboard/notifier',

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Can use middleware for access to module page
    |
    */

    'middleware' => ['notifier', 'auth'],

    /*
    |--------------------------------------------------------------------------
    | Parent category in dashboard
    |--------------------------------------------------------------------------
    |
    | Use for generation module page in dashboard.
    | Use '' if not need parent category
    |
    */

    'menu_parent_category' => '',

    /*
    |--------------------------------------------------------------------------
    | Dashboard menu item config
    |--------------------------------------------------------------------------
    |
    | Config new item in dashboard menu
    |
    */

    'menu_item_name' => 'notifier',

    'dashboard_menu_item' => [
        //'text' => 'notifier::common.notifications',
        //'icon' => 'fa-envelope-o',
        //'link' => 'dashboard/notifier/notification',
        //'rank' => 100,
        //'active_rules' => [
        //    'routes_parts'=>[
        //        'notifier::'
        //    ],
        //]

    ]
];
