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

use Qunity\Component\Profiler\DriverInterface;
use Qunity\Component\Profiler\OutputInterface;

/**
 * Class Csv
 * @package Qunity\Component\Profiler\Output
 */
class Csv implements OutputInterface
{
    /**
     * Output file path
     * @var string
     */
    protected string $file;

    /**
     * Csv constructor
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * @inheritDoc
     */
    public function output(DriverInterface $driver): void
    {
        if (!is_dir(($dir = dirname($this->file)))) {
            mkdir($dir, 0755, true);
        }
        $handle = fopen($this->file, 'w');
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
}
