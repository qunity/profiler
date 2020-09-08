Qunity Component Profiler
=========================

Profiler for measurement runtime and memory usage.

Example
-------

Several classes of drivers and outputs can be used at the same time.

```
use Qunity\Component\Profiler
use Qunity\Component\Profiler\Driver\Standard as StandardDriver
use Qunity\Component\Profiler\Output\Html as HtmlOutput

Profiler::enable()

Profiler::addDriver(StandardDriver::class)
Profiler::addOutput(HtmlOutput::class)

Profiler::start('code')
...
Profiler::stop('code')

Profiler::output()
```
