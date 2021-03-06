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
 * Class AbstractCommon
 * @package Qunity\Component\Profiler
 */
abstract class AbstractCommon implements CommonInterface
{
    /**
     * Config data
     * @var array
     */
    protected array $config = [];

    /**
     * @inheritDoc
     */
    public function getConfig(string $key = null, $default = null)
    {
        $this->validateConfig($this->config);
        if ($key) {
            if (isset($this->config[$key])) {
                return $this->config[$key];
            }
        } elseif ($this->config) {
            return $this->config;
        }
        return $default;
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $config): CommonInterface
    {
        $this->validateConfig($config);
        $this->config = $config;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function validateConfig(array $config): CommonInterface
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        if (isset($this->config['enabled'])) {
            return (bool)$this->config['enabled'];
        }
        return false;
    }
}
