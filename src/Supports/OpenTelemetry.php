<?php

namespace OpenTelemetryManager\Supports;

use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\API\Trace\SpanInterface;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\Context\Context;
use OpenTelemetry\Context\ContextInterface;
use OpenTelemetry\Context\ScopeInterface;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;

class OpenTelemetry
{
    private static TracerInterface|null $tracer = null;
    private static ScopeInterface|null $spanScope = null;
    private static SpanInterface|null $parentSpan = null;


    public static function getTrace(): TracerInterface
    {
        if (!self::$tracer) {

            $transport = (new OtlpHttpTransportFactory())->create(config('open-telemetry-manager.otl-collector-endpoint'), 'application/x-protobuf');
            $exporter = new SpanExporter($transport);

            self::$tracer = (new TracerProvider(
                [
                    new SimpleSpanProcessor(
                        $exporter
                    ),

                ],

            //    new AlwaysOnSampler()
            ))->getTracer('Laravel Web Server');
        }

        return self::$tracer;
    }


    public static function startSpan(string $name, $parentContext = null): SpanInterface
    {
        $tracer = self::getTrace();

        if(!self::$parentSpan){

            if($parentContext){
                self::$parentSpan = $tracer->spanBuilder($name)->setParent($parentContext)->startSpan();
            }else{
                self::$parentSpan = $tracer->spanBuilder($name)->startSpan();
            }

            self::$spanScope = self::$parentSpan->activate();

            return self::$parentSpan;
        }

        $ctx = Context::getCurrent();

        return $tracer->spanBuilder($name)->setParent($ctx)->startSpan();
    }

    public  static function end(): void
    {
        if(self::$spanScope){
            self::$spanScope->detach();
        }
    }


    public static function outgoingPropagationHeader(): array
    {
        $carrier = [];

        TraceContextPropagator::getInstance()->inject($carrier);

        return $carrier;
    }

    public static function incomingPropagation($headers): ContextInterface
    {
        return TraceContextPropagator::getInstance()->extract($headers);
    }

}