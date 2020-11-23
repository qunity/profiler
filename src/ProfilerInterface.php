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

use Qunity\Component\Profiler\DriverInterface;
use Qunity\Component\Profiler\OutputInterface;

/**
 * Interface ProfilerInterface
 * @package Qunity\Component
 */
interface ProfilerInterface
{
    /**
     * Configure profiler
     * @param array $config
     */
    public static function configure(array $config): void;

    /**
     * Configure profiler driver
     *
     * @param string $name
     * @param array $config
     */
    public static function configureDriver(string $name, array $config): void;

    /**
     * Configure profiler output
     *
     * @param string $name
     * @param array $config
     */
    public static function configureOutput(string $name, array $config): void;

    /**
     * Enable profiler
     */
    public static function enable(): void;

    /**
     * Disable profiler
     */
    public static function disable(): void;

    /**
     * Add profiler driver
     *
     * @param string $name
     * @param string $class
     * @param array $config
     */
    public static function addDriver(string $name, string $class, array $config = []): void;

    /**
     * Get profiler driver
     *
     * @param string $name
     * @return DriverInterface
     */
    public static function getDriver(string $name): DriverInterface;

    /**
     * Add profiler output
     *
     * @param string $name
     * @param string $class
     * @param array $config
     */
    public static function addOutput(string $name, string $class, array $config = []): void;

    /**
     * Get profiler output
     *
     * @param string $name
     * @return OutputInterface
     */
    public static function getOutput(string $name): OutputInterface;

    /**
     * Start profiler measurement
     * @param string $code
     */
    public static function start(string $code): void;

    /**
     * Stop profiler measurement
     * @param string $code
     */
    public static function stop(string $code): void;

    /**
     * Profiler drivers measurement output
     */
    public static function output(): void;

    /**
     * Check is allowed profiler activity
     * @return bool
     */
    public static function isAllowed(): bool;
}
