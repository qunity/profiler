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

/**
 * Interface OutputInterface
 * @package Qunity\Component\Profiler
 */
interface OutputInterface
{
    /**
     * Driver measurement output
     * @param DriverInterface $driver
     */
    public function output(DriverInterface $driver): void;
}
