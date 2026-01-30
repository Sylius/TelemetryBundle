<?php

declare(strict_types=1);

use Sylius\Component\Core\Telemetry\Cache\TelemetryCacheInterface;
use Sylius\Component\Core\Telemetry\Sender\TelemetrySenderInterface;
use Sylius\TelemetryBundle\Tests\Spy\AlwaysSendTelemetryCache;
use Sylius\TelemetryBundle\Tests\Spy\SpyTelemetrySender;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('sylius.telemetry.sender', SpyTelemetrySender::class)
        ->public()
    ;

    $services->alias(TelemetrySenderInterface::class, 'sylius.telemetry.sender');

    $services->set('sylius.telemetry.cache', AlwaysSendTelemetryCache::class);
    $services->alias(TelemetryCacheInterface::class, 'sylius.telemetry.cache');
};
