<?php

return [
    'default' => 'png',
    
    'renderers' => [
        'png' => [
            'extension' => 'png',
            'mime_type' => 'image/png',
            'quality' => 90,
            'back_color' => [255, 255, 255],
            'fore_color' => [0, 0, 0],
        ],
    ],

    'use_path_generator' => 'default',
    'path_generator' => [
        'default' => \SimpleSoftwareIO\QrCode\Generator::class,
    ],
    
    'image_type' => 'png',
    'image_quality' => 90,
    'error_correction' => 'H',
    'encoding' => 'UTF-8',
    'margin' => 1,
    'size' => 300,
];
