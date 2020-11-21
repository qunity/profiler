Qunity Component Profiler
=========================

Profiler for measurement runtime and memory usage.

Example
-------

Several classes of drivers and outputs can be used at the same time.

```
use Qunity\Component\Profiler;
use Qunity\Component\Profiler\Driver\Standard as StandardDriver;
use Qunity\Component\Profiler\Output\Csv as CsvOutput;
use Qunity\Component\Profiler\Output\Html as HtmlOutput;

Profiler::configure([
    'enabled' => true,
    'drivers' => [
        'standard' => [
            'class' => StandardDriver::class,
            'config' => [
                'enabled' => true,
                'time_minimum' => 0.001,
                'time_precision' => 5
            ]
        ]
    ],
    'outputs' => [
        'csv' => [
            'class' => CsvOutput::class,
            'config' => [
                'enabled' => false,
                'file' => '/custom/path/profiler.csv'
            ]
        ],
        'html' => [
            'class' => HtmlOutput::class,
            'config' => [
                'enabled' => true
            ]
        ]
    ],
    'allow_ips' => [
        'localhost'
    ]
]);

Profiler::start('code');
...
Profiler::stop('code');
Profiler::output();
```
