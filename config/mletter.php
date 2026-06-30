<?php

return [
    'brand' => [
        'name' => 'Fundacja Moje Państwo',
        'primary_color' => '#364F87',
        'rule_color' => '#AEB8D6',
    ],

    'pdf' => [
        'driver' => 'dompdf',
        'format' => 'a4',
        'disk' => 'local',
        'margins' => [18, 18, 18, 18],
        'dompdf' => [
            'font_dir' => null,
            'font_cache' => null,
            'temp_dir' => null,
            'chroot' => null,
            'default_font' => 'Source Sans Pro',
            'is_remote_enabled' => false,
        ],
    ],

    'assets' => [
        'publish_path' => 'vendor/mletter',
    ],
];
