<?php

return [
    [
        'text' => 'Dashboard details',
        'icon' => 'fa-info',
        'subitems' => [
            [
                'text' => 'Pages',
                'icon' => 'fa-circle-o',
                'subitems' => [
                    [
                        'link' => 'dashboard/tech/table-page-description',
                        'text' => 'Table Page',
                        'icon' => 'fa-circle-o',
                        'active_rules' => [
                            'urls'=> [
                                'dashboard/tech/table-page',
                                'dashboard/tech/table-page-description'
                            ]
                        ]
                    ],
                    [
                        'link' => 'dashboard/tech/tiles-list-page-description',
                        'text' => 'Tiles list page',
                        'icon' => 'fa-circle-o',
                        'active_rules' => [
                            'urls'=> [
                                'dashboard/tech/tiles-list-page',
                                'dashboard/tech/tiles-list-page-description'
                            ]
                        ]
                    ],
                    [
                        'link' => 'dashboard/tech/form-page',
                        'text' => 'Form Page',
                        'icon' => 'fa-circle-o',
                        'active_rules' => [
                            'urls'=> 'dashboard/tech/form-page'
                        ]
                    ],
                ],
            ],
            [
                'text' => 'Elements',
                'icon' => 'fa-circle-o',
                'subitems' => [
                    [
                        'link' => 'dashboard/tech/date-dropdown',
                        'text' => 'Date dropdown',
                        'icon' => 'fa-circle-o',
                        'active_rules' => [
                            'urls'=> 'dashboard/tech/date-dropdown'
                        ]
                    ],
                    [
                        'link' => 'dashboard/tech/images',
                        'text' => 'Images',
                        'icon' => 'fa-circle-o',
                        'active_rules' => [
                            'urls'=> 'dashboard/tech/images'
                        ]
                    ],
                ]
            ],
            [
                'text' => 'Fast JS Actions',
                'icon' => 'fa-circle-o',
                'link' => 'dashboard/tech/js-actions',
                'active_rules' => [
                    'urls'=> 'dashboard/tech/js-actions'
                ]
            ],
        ],
    ],
];
