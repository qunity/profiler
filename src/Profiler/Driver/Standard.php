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
     * Timers key names
     */
    protected const MEASUREMENT_START = 'start';
    protected const MEASUREMENT_REALMEM_START = 'realmem_start';
    protected const MEASUREMENT_EMALLOC_START = 'emalloc_start';
    /**#@-*/

    /**
     * Timers nesting stack
     * @var array
     */
    protected array $codes = [];

    /**
     * Timers data
     * @var array
     */
    protected array $timers = [];

    /**
     * @inheritDoc
     */
    public function start(string $code): void
    {
        if (empty($this->timers[$code])) {
            $this->timers[$code] = [
                self::MEASUREMENT_TIME => 0,
                self::MEASUREMENT_COUNT => 0,
                self::MEASUREMENT_REALMEM => 0,
                self::MEASUREMENT_EMALLOC => 0
            ];
        }
        $this->timers[$code][self::MEASUREMENT_REALMEM_START] = memory_get_usage(true);
        $this->timers[$code][self::MEASUREMENT_EMALLOC_START] = memory_get_usage();
        $this->timers[$code][self::MEASUREMENT_START] = microtime(true);
        $this->timers[$code][self::MEASUREMENT_COUNT]++;
        $this->codes[] = $code;
    }

    /**
     * @inheritDoc
     */
    public function stop(string $code): void
    {
        if (empty($this->timers[$code])) {
            throw new InvalidArgumentException(sprintf("Profiler timer \"%s\" doesn't exist", $code));
        }
        $this->timers[$code][self::MEASUREMENT_TIME] +=
            microtime(true) - $this->timers[$code][self::MEASUREMENT_START];
        $this->timers[$code][self::MEASUREMENT_START] = 0;
        $this->timers[$code][self::MEASUREMENT_REALMEM] += memory_get_usage(true);
        $this->timers[$code][self::MEASUREMENT_REALMEM] -= $this->timers[$code][self::MEASUREMENT_REALMEM_START];
        $this->timers[$code][self::MEASUREMENT_EMALLOC] += memory_get_usage();
        $this->timers[$code][self::MEASUREMENT_EMALLOC] -= $this->timers[$code][self::MEASUREMENT_EMALLOC_START];
        $this->timers[$code][self::MEASUREMENT_PATH] = $this->codes;
        unset($this->codes[array_key_last($this->codes)]);
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        $data = [];
        $index = 0;
        foreach ($this->timers as $timer) {
            $data[] = [
                self::MEASUREMENT_INDEX => ++$index,
                self::MEASUREMENT_PATH => $timer[self::MEASUREMENT_PATH],
                self::MEASUREMENT_TIME => number_format(
                    round($timer[self::MEASUREMENT_TIME], 6, PHP_ROUND_HALF_UP),
                    6
                ),
                self::MEASUREMENT_AVG => number_format(
                    round($timer[self::MEASUREMENT_TIME] / $timer[self::MEASUREMENT_COUNT], 6, PHP_ROUND_HALF_UP),
                    6
                ),
                self::MEASUREMENT_COUNT => $timer[self::MEASUREMENT_COUNT],
                self::MEASUREMENT_REALMEM => $timer[self::MEASUREMENT_REALMEM],
                self::MEASUREMENT_EMALLOC => $timer[self::MEASUREMENT_EMALLOC]
            ];
        }
        return $data;
    }
}
