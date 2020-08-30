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
 * Class OutputFactory
 * @package Qunity\Component\Profiler
 */
class OutputFactory
{
    /**
     * Create instance output-profiler
     *
     * @param string $class
     * @param array $args
     *
     * @return OutputInterface
     */
    public static function create(string $class, array $args = []): OutputInterface
    {
        $instance = new $class(...$args);
        if (!($instance instanceof OutputInterface)) {
            throw new InvalidArgumentException(
                sprintf('Class %s does`t implement the interface %s', get_class($instance), OutputInterface::class)
            );
        }
        return $instance;
    }
}
