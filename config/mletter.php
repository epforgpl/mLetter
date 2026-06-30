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
        'page_numbers' => [
            'enabled' => true,
            'text' => '{PAGE_NUM} / {PAGE_COUNT}',
            'font' => 'Source Sans Pro',
            'font_size' => 8,
            'right' => 18,
            'bottom' => 10,
        ],
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
