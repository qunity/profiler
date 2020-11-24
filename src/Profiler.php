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

use LogicException;
use Symfony\Component\HttpFoundation\Request;

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
     * @var Profiler\DriverInterface[]
     */
    protected static array $drivers = [];

    /**
     * Profiler output list
     * @var Profiler\OutputInterface[]
     */
    protected static array $outputs = [];

    /**
     * Allowed IPs list for profiler
     * @var array
     */
    protected static array $allowIps = [];

    /**
     * Profiler constructor
     */
    protected function __construct()
    {
        // creating a current instance via "new" is forbidden
    }

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
        if (isset($config['allow_ips'])) {
            self::$allowIps = array_unique(array_map(function (string $address) {
                if ($address == 'localhost') {
                    $address = '127.0.0.1';
                }
                return inet_pton($address);
            }, $config['allow_ips']));
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
    public static function addDriver(string $name, string $class, array $config = []): void
    {
        if (isset(self::$drivers[$name])) {
            throw new LogicException("Profiler driver ${name} already exist");
        }
        self::$drivers[$name] = Profiler\DriverFactory::create($class, $config);
    }

    /**
     * @inheritDoc
     */
    public static function getDriver(string $name): Profiler\DriverInterface
    {
        if (isset(self::$drivers[$name])) {
            return self::$drivers[$name];
        }
        throw new LogicException("Profiler driver ${name} does not exist");
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
    public static function addOutput(string $name, string $class, array $config = []): void
    {
        if (isset(self::$drivers[$name])) {
            throw new LogicException("Profiler output ${name} already exist");
        }
        self::$outputs[$name] = Profiler\OutputFactory::create($class, $config);
    }

    /**
     * @inheritDoc
     */
    public static function getOutput(string $name): Profiler\OutputInterface
    {
        if (isset(self::$outputs[$name])) {
            return self::$outputs[$name];
        }
        throw new LogicException("Profiler output ${name} does not exist");
    }

    /**
     * @inheritDoc
     */
    public static function start(string $code): void
    {
        if (self::isAllowed()) {
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
    public static function isAllowed(): bool
    {
        return self::$enabled &&
            (!self::$allowIps || in_array(inet_pton(Request::createFromGlobals()->getClientIp()), self::$allowIps));
    }

    /**
     * @inheritDoc
     */
    public static function stop(string $code): void
    {
        if (self::isAllowed()) {
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
        if (self::isAllowed()) {
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
