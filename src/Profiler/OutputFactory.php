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

use LogicException;

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
     * @param array $config
     *
     * @return OutputInterface
     */
    public static function create(string $class, array $config = []): OutputInterface
    {
        $instance = new $class();
        if (!($instance instanceof OutputInterface)) {
            throw new LogicException(
                sprintf('Class %s does`t implement the interface %s', get_class($instance), OutputInterface::class)
            );
        }
        return $instance->setConfig($config);
    }
}
