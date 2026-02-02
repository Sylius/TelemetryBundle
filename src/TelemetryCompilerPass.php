<?php

declare(strict_types=1);

namespace Sylius\Telemetry;

use Sylius\Bundle\CoreBundle\SyliusCoreBundle;
use Sylius\Component\Core\Telemetry\TelemetrySendManagerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class TelemetryCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->has('sylius.telemetry.send_manager')) {
            return;
        }

        if (!$this->isTelemetrySupported()) {
            return;
        }

        $env = (string) $container->getParameter('kernel.environment');
        if (str_starts_with($env, 'dev') || str_starts_with($env, 'test')) {
            return;
        }

        $this->loadTelemetryServices($container);
    }

    protected function isTelemetrySupported(): bool
    {
        return interface_exists(TelemetrySendManagerInterface::class);
    }

    private function loadTelemetryServices(ContainerBuilder $container): void
    {
        $bundleDir = dirname((new \ReflectionClass(SyliusCoreBundle::class))->getFileName());
        $configDir = $bundleDir . '/Resources/config/services/telemetry';

        $locator = new FileLocator($configDir);

        $loaders = [
            new PhpFileLoader($container, $locator),
            new DirectoryLoader($container, $locator),
        ];

        if (class_exists(XmlFileLoader::class)) {
            $loaders[] = new XmlFileLoader($container, $locator);
        }

        $loader = new DelegatingLoader(new LoaderResolver($loaders));
        $loader->load($configDir, 'directory');
    }
}
