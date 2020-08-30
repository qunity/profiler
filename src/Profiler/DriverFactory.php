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

use InvalidArgumentException;

/**
 * Class DriverFactory
 * @package Qunity\Component\Profiler
 */
class DriverFactory
{
    /**
     * Create instance driver-profiler
     *
     * @param string $class
     * @param array $args
     *
     * @return DriverInterface
     */
    public static function create(string $class, array $args = []): DriverInterface
    {
        $instance = new $class(...$args);
        if (!($instance instanceof DriverInterface)) {
            throw new InvalidArgumentException(
                sprintf('Class %s does`t implement the interface %s', get_class($instance), DriverInterface::class)
            );
        }
        return $instance;
    }
}
