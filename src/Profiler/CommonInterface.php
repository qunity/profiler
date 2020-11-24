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
 * Interface CommonInterface
 * @package Qunity\Component\Profiler
 */
interface CommonInterface
{
    /**
     * Set config data
     *
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config): self;

    /**
     * Get config data / config value
     *
     * @param string|null $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getConfig(string $key = null, $default = null);

    /**
     * Validate config data
     *
     * @param array $config
     * @return $this
     */
    public function validateConfig(array $config): self;

    /**
     * Check is enabled activity
     * @return bool
     */
    public function isEnabled(): bool;
}
