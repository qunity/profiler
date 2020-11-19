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

use Qunity\Component\Profiler\AbstractOutput;
use Qunity\Component\Profiler\DriverInterface;

/**
 * Class Html
 * @package Qunity\Component\Profiler\Output
 */
class Html extends AbstractOutput
{
    /**
     * @inheritDoc
     */
    public function output(DriverInterface $driver): void
    {
        $htmlCss = <<<HTML
<style>
    .profiler { margin: 8px; }
    .profiler table { width: 100%; font: 12px sans-serif; box-shadow: 1px 2px 3px #ccc; border-collapse: collapse; }
    .profiler thead { text-transform: uppercase; background: #f7f7f7; }
    .profiler th, .profiler td { color: #505050; border: solid 1px #f3f3f3; vertical-align: middle; padding: 5px; }
    .profiler th { cursor: default; }
    .profiler .row:hover { background: #f7f7f7; } 
    .profiler .nesting { color: #707070; } 
</style>
HTML;

        $htmlThead = $this->html(
            'tr',
            $this->html('th', DriverInterface::MEASUREMENT_INDEX) .
            $this->html('th', DriverInterface::MEASUREMENT_PATH) .
            $this->html('th', DriverInterface::MEASUREMENT_TIME) .
            $this->html('th', DriverInterface::MEASUREMENT_AVG) .
            $this->html('th', DriverInterface::MEASUREMENT_COUNT) .
            $this->html('th', DriverInterface::MEASUREMENT_REALMEM) .
            $this->html('th', DriverInterface::MEASUREMENT_EMALLOC)
        );

        $htmlIndent = $this->html('span', '&#183;&#160;', ['class' => 'nesting']);
        $htmlSeparator = $this->html('span', ' &#8250; ', ['class' => 'nesting']);

        $htmlTbody = '';
        foreach ($driver->data() as $row) {
            $row[DriverInterface::MEASUREMENT_PATH] =
                str_repeat($htmlIndent, count($row[DriverInterface::MEASUREMENT_PATH]) - 1) .
                implode($htmlSeparator, $row[DriverInterface::MEASUREMENT_PATH]);

            $htmlTbody .= $this->html(
                'tr',
                $this->html('th', $row[DriverInterface::MEASUREMENT_INDEX]) .
                $this->html('td', $row[DriverInterface::MEASUREMENT_PATH]) .
                $this->html('td', $row[DriverInterface::MEASUREMENT_TIME]) .
                $this->html('td', $row[DriverInterface::MEASUREMENT_AVG]) .
                $this->html('td', $row[DriverInterface::MEASUREMENT_COUNT]) .
                $this->html('td', $row[DriverInterface::MEASUREMENT_REALMEM]) .
                $this->html('td', $row[DriverInterface::MEASUREMENT_EMALLOC]),
                ['class' => 'row']
            );
        }

        if ($htmlTbody) {
            $htmlThead = $this->html('thead', $htmlThead);
            $htmlTbody = $this->html('tbody', $htmlTbody);
            $htmlTable = $this->html('table', $htmlThead . $htmlTbody);

            print $htmlCss . $this->html('div', $htmlTable, ['class' => 'profiler']);
        }
    }

    /**
     * Create html-string by string and settings
     *
     * @param string $tag
     * @param string $string
     * @param array $config
     *
     * @return string
     */
    protected function html(string $tag, string $string, array $config = []): string
    {
        $attributes = [];
        foreach ($config as $attrName => $attrItems) {
            $attributes[] = sprintf('%s="%s"', $attrName, implode(' ', (array)$attrItems));
        }
        if ($attributes) {
            return sprintf('<%s %s>%s</%s>', $tag, implode(' ', $attributes), $string, $tag);
        } else {
            return sprintf('<%s>%s</%s>', $tag, $string, $tag);
        }
    }
}
