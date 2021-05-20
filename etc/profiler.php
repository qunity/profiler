<?php

/**
 * This file is part of the Qunity package.
 *
 * Copyright (c) Rodion Kachkin <kyleRQWS@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Qunity\Component\Profiler\Driver\Standard as ProfilerDriverStandard;
use Qunity\Component\Profiler\Output\Csv as ProfilerOutputCsv;
use Qunity\Component\Profiler\Output\Html as ProfilerOutputHtml;

return [
    'enabled' => true,
    'drivers' => [
        'standard' => [
            'class' => ProfilerDriverStandard::class,
            'config' => [
                'enabled' => true,
                'time_minimum' => 0.001,
                'time_precision' => 5
            ]
        ]
    ],
    'outputs' => [
        'csv' => [
            'class' => ProfilerOutputCsv::class,
            'config' => [
                'enabled' => true,
                'file' => sprintf('%s/var/profiler/%d.csv', BASE_DIR, time())
            ]
        ],
        'html' => [
            'class' => ProfilerOutputHtml::class,
            'config' => [
                'enabled' => true
            ]
        ]
    ],
    'allow_ips' => [
        'localhost'
    ]
];
