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
     * Timer array keys
     */
    public const KEY_TIME = 'time';
    public const KEY_AVG = 'avg';
    protected const KEY_START = 'start';
    public const KEY_COUNT = 'count';
    public const KEY_REALMEM = 'realmem';
    protected const KEY_REALMEM_START = 'realmem_start';
    public const KEY_EMALLOC = 'emalloc';
    protected const KEY_EMALLOC_START = 'emalloc_start';
    /**#@-*/

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
                self::KEY_TIME => 0,
                self::KEY_AVG => 0,
                self::KEY_START => 0,
                self::KEY_COUNT => 0,
                self::KEY_REALMEM => 0,
                self::KEY_REALMEM_START => 0,
                self::KEY_EMALLOC => 0,
                self::KEY_EMALLOC_START => 0
            ];
        }
        $this->timers[$code][self::KEY_REALMEM_START] = memory_get_usage(true);
        $this->timers[$code][self::KEY_EMALLOC_START] = memory_get_usage();
        $this->timers[$code][self::KEY_START] = microtime(true);
        $this->timers[$code][self::KEY_COUNT]++;
    }

    /**
     * @inheritDoc
     */
    public function stop(string $code): void
    {
        if (empty($this->timers[$code])) {
            throw new InvalidArgumentException(sprintf("Timer \"%s\" doesn't exist", $code));
        }
        $this->timers[$code][self::KEY_TIME] += microtime(true) - $this->timers[$code][self::KEY_START];
        $this->timers[$code][self::KEY_AVG] =
            $this->timers[$code][self::KEY_TIME] / $this->timers[$code][self::KEY_COUNT];
        $this->timers[$code][self::KEY_START] = 0;
        $this->timers[$code][self::KEY_REALMEM] += memory_get_usage(true);
        $this->timers[$code][self::KEY_REALMEM] -= $this->timers[$code][self::KEY_REALMEM_START];
        $this->timers[$code][self::KEY_EMALLOC] += memory_get_usage();
        $this->timers[$code][self::KEY_EMALLOC] -= $this->timers[$code][self::KEY_EMALLOC_START];
    }

    /**
     * @inheritDoc
     */
    public function data(): array
    {
        $data = [];
        foreach ($this->timers as $code => $timer) {
            $data[$code] = [
                self::KEY_TIME => $timer[self::KEY_TIME],
                self::KEY_AVG => $timer[self::KEY_AVG],
                self::KEY_COUNT => $timer[self::KEY_COUNT],
                self::KEY_REALMEM => $timer[self::KEY_REALMEM],
                self::KEY_EMALLOC => $timer[self::KEY_EMALLOC]
            ];
        }
        return $data;
    }
}
