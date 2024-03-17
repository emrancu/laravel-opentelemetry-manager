
## Start Span

```php
 use OpenTelemetryManager\Supports;

$parent = OpenTelemetry::startSpan("parent")

    $child = OpenTelemetry::startSpan("child")
 
    $child->end();

$parent->end();

//for detach active span ( call in end of the execution )
OpenTelemetry::end()
```


## Context Propagation ( sending )

```php
use OpenTelemetryManager\Supports;


$outgoing = OpenTelemetry::startSpan('Start remote Request');

   $response = Http::withHeaders(OpenTelemetry::outGoingPropagationHeader())
            ->get('url');

$outgoing->end();
 
 
//for detach active span ( call in end of the execution )
OpenTelemetry::end()
```

## Context Propagation ( Incoming )

```php
use OpenTelemetryManager\Supports;


$context = OpenTelemetry::incomingPropagation($request->header());

$span = OpenTelemetry::startSpan('Start second App', $context);

$span->end();
 
 
//for detach active span ( call in end of the execution )
OpenTelemetry::end()
```