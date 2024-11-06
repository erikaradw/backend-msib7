<?php

return [
    'default' => 'local',
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],
        // Tambahkan konfigurasi disk lain jika diperlukan
    ],
];