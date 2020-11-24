<?php

/**
 * This file is part of the Qunity package.
 *
 * Copyright (c) Rodion Kachkin <kyleRQWS@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Qunity\Component\Profiler\Output;

use LogicException;
use Qunity\Component\Profiler\AbstractOutput;
use Qunity\Component\Profiler\CommonInterface;
use Qunity\Component\Profiler\DriverInterface;

/**
 * Class Csv
 * @package Qunity\Component\Profiler\Output
 */
class Csv extends AbstractOutput
{
    /**
     * @inheritDoc
     */
    public function output(DriverInterface $driver): void
    {
        $file = $this->getConfig('file');
        if (!is_dir(($dir = dirname($file)))) {
            mkdir($dir, 0755, true);
        }
        $handle = fopen($file, 'w');
        fputcsv($handle, [
            ucfirst(strtolower(DriverInterface::MEASUREMENT_INDEX)),
            ucfirst(strtolower(DriverInterface::MEASUREMENT_PATH)),
            ucfirst(strtolower(DriverInterface::MEASUREMENT_TIME)),
            ucfirst(strtolower(DriverInterface::MEASUREMENT_AVG)),
            ucfirst(strtolower(DriverInterface::MEASUREMENT_COUNT)),
            ucfirst(strtolower(DriverInterface::MEASUREMENT_REALMEM)),
            ucfirst(strtolower(DriverInterface::MEASUREMENT_EMALLOC))
        ]);
        foreach ($driver->data() as $row) {
            $row[DriverInterface::MEASUREMENT_PATH] = implode(' > ', $row[DriverInterface::MEASUREMENT_PATH]);
            fputcsv($handle, $row);
        }
        fclose($handle);
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $config): CommonInterface
    {
        if (empty($config['file'])) {
            throw new LogicException('Output CSV file path not specified');
        }
        return parent::setConfig($config);
    }
}
