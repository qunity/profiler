<?php

/**
 * This file is part of the Qunity package.
 *
 * Copyright (c) Rodion Kachkin <kyleRQWS@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Qunity\Component;

use Qunity\Component\Profiler\DriverFactory;
use Qunity\Component\Profiler\DriverInterface;
use Qunity\Component\Profiler\OutputFactory;
use Qunity\Component\Profiler\OutputInterface;

/**
 * Class Profiler
 * @package Qunity\Component
 */
class Profiler implements ProfilerInterface
{
    /**
     * Profiler activity status
     * @var bool
     */
    protected static bool $enabled = false;

    /**
     * Profiler driver list
     * @var DriverInterface[]
     */
    protected static array $drivers = [];

    /**
     * Profiler output list
     * @var OutputInterface[]
     */
    protected static array $outputs = [];

    /**
     * @inheritDoc
     */
    public static function enable(): void
    {
        self::$enabled = true;
    }

    /**
     * @inheritDoc
     */
    public static function disable(): void
    {
        self::$enabled = false;
    }

    /**
     * @inheritDoc
     */
    public static function addDriver(string $class, array $args = []): void
    {
        if (self::$enabled) {
            self::$drivers[$class] = DriverFactory::create($class, $args);
        }
    }

    /**
     * @inheritDoc
     */
    public static function addOutput(string $class, array $args = []): void
    {
        if (self::$enabled) {
            self::$outputs[$class] = OutputFactory::create($class, $args);
        }
    }

    /**
     * @inheritDoc
     */
    public static function start(string $code): void
    {
        if (self::$enabled) {
            foreach (self::$drivers as $driver) {
                $driver->start($code);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public static function stop(string $code): void
    {
        if (self::$enabled) {
            foreach (self::$drivers as $driver) {
                $driver->stop($code);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public static function output(): void
    {
        if (self::$enabled) {
            foreach (self::$drivers as $driver) {
                foreach (self::$outputs as $output) {
                    $output->output($driver);
                }
            }
        }
    }
}
