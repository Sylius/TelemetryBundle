<?php

declare(strict_types=1);

namespace Sylius\TelemetryBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Sylius\TelemetryBundle\DependencyInjection\TelemetryExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class TelemetryExtensionTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($_ENV['SYLIUS_TELEMETRY_ENABLED'], $_SERVER['SYLIUS_TELEMETRY_ENABLED']);
    }

    public function testItEnablesTelemetryInProdWhenDisabledViaEnv(): void
    {
        $_ENV['SYLIUS_TELEMETRY_ENABLED'] = '0';
        $container = $this->createContainer('prod');

        (new TelemetryExtension())->prepend($container);

        $this->assertSame('1', $_ENV['SYLIUS_TELEMETRY_ENABLED']);
        $this->assertSame('1', $_SERVER['SYLIUS_TELEMETRY_ENABLED']);
        $this->assertPrependedConfig($container);
    }

    public function testItEnablesTelemetryInProdWhenEnvNotSet(): void
    {
        $container = $this->createContainer('prod');

        (new TelemetryExtension())->prepend($container);

        $this->assertSame('1', $_ENV['SYLIUS_TELEMETRY_ENABLED']);
        $this->assertPrependedConfig($container);
    }

    public function testItEnablesTelemetryInStagingEnvironment(): void
    {
        $_ENV['SYLIUS_TELEMETRY_ENABLED'] = 'false';
        $container = $this->createContainer('staging');

        (new TelemetryExtension())->prepend($container);

        $this->assertSame('1', $_ENV['SYLIUS_TELEMETRY_ENABLED']);
        $this->assertPrependedConfig($container);
    }

    public function testItSkipsInDevEnvironment(): void
    {
        $_ENV['SYLIUS_TELEMETRY_ENABLED'] = '0';
        $container = $this->createContainer('dev');

        (new TelemetryExtension())->prepend($container);

        $this->assertSame('0', $_ENV['SYLIUS_TELEMETRY_ENABLED']);
        $this->assertNoPrependedConfig($container);
    }

    public function testItSkipsWhenSyliusDoesNotSupportTelemetry(): void
    {
        $container = $this->createContainer('prod');

        $extension = new class () extends TelemetryExtension {
            protected function isTelemetrySupported(): bool
            {
                return false;
            }
        };
        $extension->prepend($container);

        $this->assertArrayNotHasKey('SYLIUS_TELEMETRY_ENABLED', $_ENV);
        $this->assertNoPrependedConfig($container);
    }

    public function testItSkipsInTestEnvironment(): void
    {
        $_ENV['SYLIUS_TELEMETRY_ENABLED'] = '0';
        $container = $this->createContainer('test');

        (new TelemetryExtension())->prepend($container);

        $this->assertSame('0', $_ENV['SYLIUS_TELEMETRY_ENABLED']);
        $this->assertNoPrependedConfig($container);
    }

    private function createContainer(string $environment): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', $environment);

        return $container;
    }

    private function assertPrependedConfig(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig('sylius_core');
        $this->assertNotEmpty($configs);
        $this->assertSame(['telemetry' => ['enabled' => true]], $configs[0]);
    }

    private function assertNoPrependedConfig(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig('sylius_core');
        $this->assertEmpty($configs);
    }
}
