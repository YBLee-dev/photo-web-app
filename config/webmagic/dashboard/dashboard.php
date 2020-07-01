<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Dashboard title
     |--------------------------------------------------------------------------
     */
    'title' => 'Playful Portraits',

    /*
     |--------------------------------------------------------------------------
     | Dashboard logo configuration
     |--------------------------------------------------------------------------
     */
    'logo_settings' => [
        'link' => '/dashboard',
        'part_one' => 'Playful ',
        'part_two' => 'Portraits',
        'part_one_mini' => 'PP',
        'part_two_mini' => '',
    ],

    /*
     |--------------------------------------------------------------------------
     | Menu configuration
     |--------------------------------------------------------------------------
     */
    'menu' => [
        [
            'text' => 'Unprocessed photos',
            'icon' => 'fa-files-o',
            'link' => 'dashboard/unprocessed-photos',
            'rank' => 900,
            'gates' => ['photographer', 'admin'],
            'active_rules' => [
                'routes_parts' => [
                    'dashboard::unprocessed-photos.index',
                ],
            ],
        ],
        [
            'text' => 'Schools',
            'icon' => ' fa-institution ',
            'link' => 'dashboard/schools',
            'rank' => 900,
            'gates' => ['photographer','admin'],
            'active_rules' => [
                'routes_parts' => [
                    'dashboard::schools.index',
                    'dashboard::schools.create',
                    'dashboard::schools.edit',
                    'dashboard::schools.show',
                ],
            ],
        ],
        [
            'text' => 'Goods',
            'icon' => 'fa-shopping-cart ',
            'gates' => ['admin'],
            'link' => 'dashboard/products',
            'rank' => 900,
            'subitems' => [
                [
                    'text' => 'Products',
                    'icon' => 'fa-picture-o',
                    'link' => 'dashboard/products',
                    'rank' => 900,
                    'gates' => ['admin'],
                    'active_rules' => [
                        'routes_parts' => [
                            'dashboard::products.index',
                            'dashboard::products.show',
                            'dashboard::products.edit',
                            'dashboard::products.copy',
                            'dashboard::products.create.printable',
                            'dashboard::products.create.other',
                            'dashboard::products.create.downloadable',
                        ],
                    ],
                ],
                [
                    'text' => 'Packages',
                    'icon' => 'fa-th-large',
                    'link' => 'dashboard/packages',
                    'rank' => 900,
                    'gates' => ['admin'],
                    'active_rules' => [
                        'routes_parts' => [
                            'dashboard::packages.index',
                            'dashboard::packages.edit',
                            'dashboard::packages.create',
                            'dashboard::packages.show',
                        ],
                    ],
                ],
                [
                    'text' => 'Price lists',
                    'icon' => 'fa-list-alt',
                    'link' => 'dashboard/price-lists',
                    'rank' => 900,
                    'gates' => ['admin'],
                    'active_rules' => [
                        'routes_parts' => [
                            'dashboard::price-lists.index',
                            'dashboard::price-lists.edit',
                            'dashboard::price-lists.create',
                            'dashboard::price-lists.show',
                        ],
                    ],
                ],
//                [
//                    'text' => 'Sizes',
//                    'icon' => 'fa-arrows-alt',
//                    'link' => 'dashboard/sizes',
//                    'active_rules' => [
//                        'routes_parts' => [
//                            'dashboard::sizes.index',
//                            'dashboard::sizes.edit',
//                            'dashboard::sizes.create',
//                        ],
//                    ],
//                ],
//                [
//                    'text' => 'Sizes Combinations',
//                    'icon' => 'fa-th',
//                    'link' => 'dashboard/combinations',
//                    'active_rules' => [
//                        'routes_parts' => [
//                            'dashboard::combinations.index',
//                            'dashboard::combinations.edit',
//                            'dashboard::combinations.create',
//                            'dashboard::combinations.show',
//                        ],
//                    ],
//                ],
            ],
        ],
        [
            'text' => 'Finance',
            'icon' => ' fa-money',
            'link' => 'dashboard/carts',
            'gates' => ['admin'],
            'rank' => 900,
            'subitems' => [
                [
                    'text' => 'Carts',
                    'icon' => 'fa-shopping-cart',
                    'link' => 'dashboard/carts',
                    'rank' => 900,
                    'gates' => ['admin'],
                    'active_rules' => [
                        'routes_parts' => [
                            'dashboard::carts.index',
                            'dashboard::carts.edit',
                            'dashboard::carts.show',
                        ],
                    ],
                ],
                [
                    'text' => 'Orders',
                    'icon' => 'fa-file-text-o',
                    'link' => 'dashboard/orders',
                    'rank' => 900,
                    'gates' => ['admin'],
                    'active_rules' => [
                        'routes_parts' => [
                            'dashboard::orders.index',
                            'dashboard::orders.edit',
                            'dashboard::orders.show',
                        ],
                    ],
                ],
                [
                    'text' => 'Promo codes',
                    'icon' => 'fa-tags',
                    'link' => 'dashboard/promo-codes',
                    'rank' => 900,
                    'gates' => ['admin'],
                    'active_rules' => [
                        'routes_parts' => [
                            'dashboard::promo-codes.index',
                            'dashboard::promo-codes.edit',
                            'dashboard::promo-codes.create',
                            'dashboard::promo-codes.show',
                        ],
                    ],
                ],
            ]
        ],
        [
            'text' => 'Galleries list',
            'icon' => 'fa-sort-alpha-asc',
            'link' => 'dashboard/gallery',
            'rank' => 900,
            'gates' => ['photographer', 'admin'],
            'active_rules' => [
                'routes_parts' => [
                    'dashboard::gallery.index',
                    'dashboard::gallery.show',
                    'dashboard::gallery.edit',
                    'dashboard::gallery.subgallery.show',
                ],
            ],
        ],
        [
            'text' => 'Users',
            'icon' => 'fa-users',
            'link' => 'dashboard/users',
            'rank' => 900,
            'gates' => ['admin'],
            'active_rules' => [
                'routes_parts' => [
                    'dashboard::users',
                ],
            ],
        ],
        [
            'text' => 'Profile',
            'icon' => 'fa-book',
            'link' => 'dashboard/users/profile/show',
            'rank' => 900,
            'gates' => ['photographer'],
            'active_rules' => [
                'routes_parts' => [
                    'dashboard::users',
                ],
            ],
        ],
        [
            'text' => 'Settings',
            'icon' => 'fa-wrench',
            'gates' => ['admin'],
            'link' => 'dashboard/settings/main',
            'rank' => 900,
            'subitems' => [
                [
                    'text' => 'Main',
                    'icon' => 'fa-cog',
                    'link' => 'dashboard/settings/main',
                    'active_rules' => [
                        'routes_parts' => [
                            'dashboard::settings.main',
                        ],
                    ],
                ],
                [
                    'text' => 'Watermark',
                    'icon' => 'fa-cog',
                    'link' => 'dashboard/settings/watermark',
                    'active_rules' => [
                        'routes_parts' => [
                            'dashboard::settings.watermark',
                        ],
                    ],
                ],
                [
                    'text' => 'Specification',
                    'icon' => 'fa-cog',
                    'link' => 'dashboard/settings/specification',
                    'active_rules' => [
                        'routes_parts' => [
                            'dashboard::settings.specification',
                        ],
                    ],
                ],
                [
                    'text' => 'Order text',
                    'icon' => 'fa-cog',
                    'link' => 'dashboard/settings/text',
                    'active_rules' => [
                        'routes_parts' => [
                            'dashboard::settings.text',
                        ],
                    ],
                ],
                [
                    'text' => 'Tax',
                    'icon' => 'fa-cog',
                    'link' => 'dashboard/settings/tax',
                    'active_rules' => [
                        'routes_parts' => [
                            'dashboard::settings.tax',
                        ],
                    ],
                ],
                [
                    'text' => 'Notifications',
                    'icon' => 'fa-cog',
                    'link' => 'dashboard/notifier/notification',
                    'rank' => 100,
                    'active_rules' => [
                        'routes_parts'=>[
                            'notifier::'
                        ],
                    ]
                ]
            ],
        ],
    ],

    /*
     |--------------------------------------------------------------------------
     | NavBar menu
     |--------------------------------------------------------------------------
     */
    'header_navigation' => [
        [
            'text' => 'Logout',
            'icon' => '',
            'link' => 'logout',
        ],
        [
            'text' => 'Preview site',
            'icon' => '',
            'link' => '/',
        ],
    ],

    /*
     |--------------------------------------------------------------------------
     | Activate dashboard presentation mode
     |--------------------------------------------------------------------------
     |
     | You can see the available dashboard components if enabled
     |
     */
    'presentation_mode' => false,
];
