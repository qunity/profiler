<?php

/**
 * This file is part of the Qunity package.
 *
 * Copyright (c) Rodion Kachkin <kyleRQWS@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Qunity\Component\Profiler;

/**
 * Interface DriverInterface
 * @package Qunity\Component\Profiler
 */
interface DriverInterface
{
    /**#@+
     * Measurement key names
     */
    public const MEASUREMENT_INDEX = '#';
    public const MEASUREMENT_PATH = 'path';
    public const MEASUREMENT_TIME = 'time';
    public const MEASUREMENT_AVG = 'avg';
    public const MEASUREMENT_COUNT = 'count';
    public const MEASUREMENT_REALMEM = 'realmem';
    public const MEASUREMENT_EMALLOC = 'emalloc';
    /**#@-*/

    /**
     * Start measurement
     * @param string $code
     */
    public function start(string $code): void;

    /**
     * Stop measurement
     * @param string $code
     */
    public function stop(string $code): void;

    /**
     * Get measurements data
     * @return array
     */
    public function data(): array;
}
