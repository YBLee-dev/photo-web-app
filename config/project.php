<?php

return [

    /**
     * Photos preview sizes
     */
    'preview_size' => [
        'width' => 800,
        'height' => 800
    ],

    /**
     * Size for face photo cropping
     */
    'cropped_face_size' => [
        'width' => 400,
        'height' => 500,
        'top_indent' => 50,
        'bottom_indent' => 120
    ],

    /*
      | Default settings for group photos
      |
      */
    'default_settings_group_photo' => [
        'school_logo' => 'default/school-logo-blank.png',
        'naming_structure' => 'First Name Last Initial',
        'use_teacher_prefix' => true,
        'use_school_logo' => true,

        'font_file' => 'Poppins-Regular.ttf',

        'school_name_font_size' => 60,
        'class_name_font_size' => 50,
        'year_font_size' => 50,
        'name_font_size' => 30,

        'school_name_font_size_school_photo' => 90,
        'year_font_size_school_photo' => 75,
        'name_font_size_school_photo' => 45,

        'school_background' => 'default/school-photo-background.jpg',
        'class_background' => 'default/class_photo_background.jpg',
        'id_cards_background_portrait' => 'default/id-card-portrait.jpg',
        'id_cards_background_landscape' => 'default/id-card-landscape.jpg',

        'id_cards_portrait_name_size' => 50,
        'id_cards_portrait_title_size' => 40,
        'id_cards_portrait_year_size' => 40,
        'id_cards_landscape_name_size' => 47,
        'id_cards_landscape_title_size' => 38,
        'id_cards_landscape_year_size' => 38,
    ],

    // Available naming structures
    'available_naming_structures' => [
        'Full Name' => 'Full Name',
        'First Name' => 'First Name',
        'First Name Last Initial' => 'First Name Last Initial',
        'First Name Last Name' => 'First Name Last Name',
    ],

    // Path for templates
    'templates_img_path' => 'templates',

    // Path for templates
    'settings_img_path' => 'webmagic/dashboard/settings',

    // Font path
    'fonts_path' => 'fonts',

    //Pagination size
    'pagination_size' => 10,
    'tiles_pagination_size' => 12,


    //Directory structure errors
    'structure_errors' => [
        'empty_directories' => [
            'name' => 'There are the empty directories in the main folder:',
            'solution' => 'Please, fill it with photos or delete it',
        ],
        'unsatisfactory_files' => [
            'name' => 'There are files in the main folder:',
            'solution' => 'There can be folders in the main folder only. Please, remove all files from the main folder'
        ],
        'unsatisfactory_directories' => [
            'name' => 'There are folders in the second level folders:',
            'solution' => 'There can be only files (.jpg, .png) in the second level folders only. Please, remove all folders from the second level folders'
        ]
    ]
];
