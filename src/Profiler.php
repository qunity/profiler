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

use InvalidArgumentException;
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
    public static function configure(array $config): void
    {
        if (isset($config['enabled'])) {
            self::$enabled = (bool)$config['enabled'];
        }
        if (isset($config['drivers'])) {
            foreach ($config['drivers'] as $name => $driver) {
                self::configureDriver($name, $driver);
            }
        }
        if (isset($config['outputs'])) {
            foreach ($config['outputs'] as $name => $output) {
                self::configureOutput($name, $output);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public static function configureDriver(string $name, array $driver): void
    {
        $config = [];
        if (isset($driver['config'])) {
            $config = $driver['config'];
        }
        if (isset($driver['class'])) {
            self::addDriver($name, $driver['class'], $config);
        } elseif ($config) {
            self::getDriver($name)->setConfig($config);
        }
    }

    /**
     * @inheritDoc
     */
    public static function configureOutput(string $name, array $output): void
    {
        $config = [];
        if (isset($output['config'])) {
            $config = $output['config'];
        }
        if (isset($output['class'])) {
            self::addOutput($name, $output['class'], $config);
        } elseif ($config) {
            self::getOutput($name)->setConfig($config);
        }
    }

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
    public static function addDriver(string $name, string $class, array $config = []): void
    {
        self::$drivers[$name] = DriverFactory::create($class, $config);
    }

    /**
     * @inheritDoc
     */
    public static function getDriver(string $name): DriverInterface
    {
        if (isset(self::$drivers[$name])) {
            return self::$drivers[$name];
        }
        throw new InvalidArgumentException(sprintf('Profiler driver %s does not exist', $name));
    }

    /**
     * @inheritDoc
     */
    public static function addOutput(string $name, string $class, array $config = []): void
    {
        self::$outputs[$name] = OutputFactory::create($class, $config);
    }

    /**
     * @inheritDoc
     */
    public static function getOutput(string $name): OutputInterface
    {
        if (isset(self::$outputs[$name])) {
            return self::$outputs[$name];
        }
        throw new InvalidArgumentException(sprintf('Profiler output %s does not exist', $name));
    }

    /**
     * @inheritDoc
     */
    public static function start(string $code): void
    {
        if (self::$enabled) {
            foreach (self::$drivers as $driver) {
                if ($driver->isEnabled()) {
                    $driver->start($code);
                }
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
                if ($driver->isEnabled()) {
                    $driver->stop($code);
                }
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
                if ($driver->isEnabled()) {
                    foreach (self::$outputs as $output) {
                        if ($output->isEnabled()) {
                            $output->output($driver);
                        }
                    }
                }
            }
        }
    }
}
