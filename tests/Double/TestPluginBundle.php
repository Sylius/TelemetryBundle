<?php

declare(strict_types=1);

namespace Sylius\Telemetry\Tests\Double;

use Sylius\Telemetry\TelemetryCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Simulates an official Sylius plugin that registers the telemetry compiler pass.
 */
final class TestPluginBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TelemetryCompilerPass());
    }
}
