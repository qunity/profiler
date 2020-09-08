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

use InvalidArgumentException;
use Qunity\Component\Profiler\DriverInterface;

/**
 * Class Standard
 * @package Qunity\Component\Profiler\Driver
 */
class Standard implements DriverInterface
{
    /**#@+
     * Additional measurement key names
     */
    protected const MEASUREMENT_START = 'start';
    protected const MEASUREMENT_REALMEM_START = 'realmem_start';
    protected const MEASUREMENT_EMALLOC_START = 'emalloc_start';
    /**#@-*/

    /**
     * Number of decimal digits in timing
     */
    protected const TIME_PRECISION = 6;

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
            throw new InvalidArgumentException(sprintf("Profiler measurement %s doesn't exist", $code));
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
        $data = [];
        $index = 0;
        foreach ($this->data as $timer) {
            $data[] = [
                self::MEASUREMENT_INDEX => ++$index,
                self::MEASUREMENT_PATH => $timer[self::MEASUREMENT_PATH],
                self::MEASUREMENT_TIME => number_format(round(
                    $timer[self::MEASUREMENT_TIME],
                    self::TIME_PRECISION,
                    PHP_ROUND_HALF_UP
                ), self::TIME_PRECISION),
                self::MEASUREMENT_AVG => number_format(round(
                    $timer[self::MEASUREMENT_TIME] / $timer[self::MEASUREMENT_COUNT],
                    self::TIME_PRECISION,
                    PHP_ROUND_HALF_UP
                ), self::TIME_PRECISION),
                self::MEASUREMENT_COUNT => $timer[self::MEASUREMENT_COUNT],
                self::MEASUREMENT_REALMEM => $timer[self::MEASUREMENT_REALMEM],
                self::MEASUREMENT_EMALLOC => $timer[self::MEASUREMENT_EMALLOC]
            ];
        }
        return $data;
    }
}
