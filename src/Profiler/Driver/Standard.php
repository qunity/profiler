<?php

/**
 * This file is part of the Qunity package.
 *
 * Copyright (c) Rodion Kachkin <kyleRQWS@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Qunity\Component\Profiler\Driver;

use LogicException;
use Qunity\Component\Profiler\AbstractDriver;

/**
 * Class Standard
 * @package Qunity\Component\Profiler\Driver
 */
class Standard extends AbstractDriver
{
    /**#@+
     * Additional measurement key names
     */
    protected const MEASUREMENT_START = 'start';
    protected const MEASUREMENT_REALMEM_START = 'realmem_start';
    protected const MEASUREMENT_EMALLOC_START = 'emalloc_start';
    /**#@-*/

    /**
     * Measurement stack
     * @var array
     */
    protected array $codes = [];

    /**
     * Measurement data
     * @var array
     */
    protected array $data = [];

    /**
     * @inheritDoc
     */
    public function start(string $code): void
    {
        if (empty($this->data[$code])) {
            $this->data[$code] = [
                self::MEASUREMENT_TIME => 0,
                self::MEASUREMENT_COUNT => 0,
                self::MEASUREMENT_REALMEM => 0,
                self::MEASUREMENT_EMALLOC => 0
            ];
        }
        $this->data[$code][self::MEASUREMENT_REALMEM_START] = memory_get_usage(true);
        $this->data[$code][self::MEASUREMENT_EMALLOC_START] = memory_get_usage();
        $this->data[$code][self::MEASUREMENT_START] = microtime(true);
        $this->data[$code][self::MEASUREMENT_COUNT]++;
        $this->codes[] = $code;
    }

    /**
     * @inheritDoc
     */
    public function stop(string $code): void
    {
        if (empty($this->data[$code])) {
            throw new LogicException("Profiler measurement ${code} does not exist");
        }
        $this->data[$code][self::MEASUREMENT_TIME] +=
            microtime(true) - $this->data[$code][self::MEASUREMENT_START];
        $this->data[$code][self::MEASUREMENT_START] = 0;
        $this->data[$code][self::MEASUREMENT_REALMEM] += memory_get_usage(true);
        $this->data[$code][self::MEASUREMENT_REALMEM] -= $this->data[$code][self::MEASUREMENT_REALMEM_START];
        $this->data[$code][self::MEASUREMENT_EMALLOC] += memory_get_usage();
        $this->data[$code][self::MEASUREMENT_EMALLOC] -= $this->data[$code][self::MEASUREMENT_EMALLOC_START];
        $this->data[$code][self::MEASUREMENT_PATH] = $this->codes;
        unset($this->codes[array_key_last($this->codes)]);
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        $index = (int)$data = [];
        $timeMinimum = $this->getConfig('time_minimum', 0.001);
        foreach ($this->data as $timer) {
            if ($timer[self::MEASUREMENT_TIME] < $timeMinimum) {
                continue;
            }
            $data[] = [
                self::MEASUREMENT_INDEX => ++$index,
                self::MEASUREMENT_PATH => $timer[self::MEASUREMENT_PATH],
                self::MEASUREMENT_TIME => $this->formatTime($timer[self::MEASUREMENT_TIME]),
                self::MEASUREMENT_AVG =>
                    $this->formatTime($timer[self::MEASUREMENT_TIME] / $timer[self::MEASUREMENT_COUNT]),
                self::MEASUREMENT_COUNT => $timer[self::MEASUREMENT_COUNT],
                self::MEASUREMENT_REALMEM => $timer[self::MEASUREMENT_REALMEM],
                self::MEASUREMENT_EMALLOC => $timer[self::MEASUREMENT_EMALLOC]
            ];
        }
        return $data;
    }

    /**
     * Date formatting for output
     *
     * @param float $time
     * @return string
     */
    protected function formatTime(float $time): string
    {
        $precision = $this->getConfig('time_precision', 5);
        return number_format(round($time, $precision, PHP_ROUND_HALF_UP), $precision);
    }
}
