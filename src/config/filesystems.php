<?php

return [
    'disks' => [
        'generated-csv' => [
            'driver' => 'local',
            'root' => storage_path('app').'/csv',
        ],
    ],
];
