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

/**
 * Interface ProfilerInterface
 * @package Qunity\Component
 */
interface ProfilerInterface
{
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
     * @param string $class
     * @param array $args
     */
    public static function addDriver(string $class, array $args = []): void;

    /**
     * Add profiler output
     *
     * @param string $class
     * @param array $args
     */
    public static function addOutput(string $class, array $args = []): void;

    /**
     * Start profiler metering
     * @param string $code
     */
    public static function start(string $code): void;

    /**
     * Stop profiler metering
     * @param string $code
     */
    public static function stop(string $code): void;

    /**
     * Profiler drivers data output
     */
    public static function output(): void;
}
