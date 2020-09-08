Qunity Component Profiler
=========================

Profiler for measuring runtime and memory usage.

Example for use
---------------

```
\Qunity\Component\Profiler::enable();

\Qunity\Component\Profiler::addDriver(\Qunity\Component\Profiler\Driver\Standard::class)
\Qunity\Component\Profiler::addOutput(\Qunity\Component\Profiler\Output\Html::class);
// connecting other driver or output classes...

\Qunity\Component\Profiler::start(<measurement code>);
...
\Qunity\Component\Profiler::stop(<measurement code>);

\Qunity\Component\Profiler::output();
```
