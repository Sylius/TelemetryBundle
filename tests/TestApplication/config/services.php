<?php

declare(strict_types=1);

use Sylius\TelemetryBundle\Tests\Double\AlwaysSendTelemetryCache;
use Sylius\TelemetryBundle\Tests\Double\InMemoryTelemetrySender;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius.telemetry.sender', InMemoryTelemetrySender::class)
        ->public()
    ;

    $services->set('sylius.telemetry.cache', AlwaysSendTelemetryCache::class);
};
