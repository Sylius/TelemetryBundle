<?php

declare(strict_types=1);

namespace Sylius\TelemetryBundle\DependencyInjection;

use Sylius\Component\Core\Telemetry\TelemetrySendManagerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class TelemetryExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        if ($this->isDevOrTestEnv($container) || !$this->isTelemetrySupported()) {
            return;
        }

        $_ENV['SYLIUS_TELEMETRY_ENABLED'] = '1';
        $_SERVER['SYLIUS_TELEMETRY_ENABLED'] = '1';

        $container->prependExtensionConfig('sylius_core', [
            'telemetry' => [
                'enabled' => true,
            ],
        ]);
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
    }

    protected function isTelemetrySupported(): bool
    {
        return interface_exists(TelemetrySendManagerInterface::class);
    }

    private function isDevOrTestEnv(ContainerBuilder $container): bool
    {
        $env = $container->getParameter('kernel.environment');

        return str_starts_with($env, 'dev') || str_starts_with($env, 'test');
    }
}
