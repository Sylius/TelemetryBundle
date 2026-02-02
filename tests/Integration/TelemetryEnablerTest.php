<?php

declare(strict_types=1);

namespace Sylius\TelemetryBundle\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Sylius\TestApplication\Kernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class TelemetryEnablerTest extends TestCase
{
    private ?string $previousConfigsToImport = null;

    protected function setUp(): void
    {
        $_SERVER['SYLIUS_TEST_APP_BUNDLES_TO_ENABLE'] = 'Sylius\TelemetryBundle\TelemetryBundle';

        $this->previousConfigsToImport = $_SERVER['SYLIUS_TEST_APP_CONFIGS_TO_IMPORT'] ?? null;
        unset($_SERVER['SYLIUS_TEST_APP_CONFIGS_TO_IMPORT']);
    }

    protected function tearDown(): void
    {
        if ($this->previousConfigsToImport !== null) {
            $_SERVER['SYLIUS_TEST_APP_CONFIGS_TO_IMPORT'] = $this->previousConfigsToImport;
        }

        unset(
            $_ENV['SYLIUS_TELEMETRY_ENABLED'],
            $_SERVER['SYLIUS_TELEMETRY_ENABLED'],
            $_SERVER['SYLIUS_TEST_APP_BUNDLES_TO_ENABLE'],
        );
    }

    /** @group prod */
    public function testProdWithTelemetryDisabledStillHasServicesRegistered(): void
    {
        $_ENV['SYLIUS_TELEMETRY_ENABLED'] = '0';
        $_SERVER['SYLIUS_TELEMETRY_ENABLED'] = '0';

        $container = $this->compileContainer('prod');

        $this->assertTrue($container->getParameter('sylius_core.telemetry.enabled'));
        $this->assertTrue($container->has('sylius.telemetry.sender'));
        $this->assertTrue($container->has('sylius.telemetry.send_manager'));
    }

    /** @group prod */
    public function testProdWithTelemetryEnabledHasServicesRegistered(): void
    {
        $_ENV['SYLIUS_TELEMETRY_ENABLED'] = '1';
        $_SERVER['SYLIUS_TELEMETRY_ENABLED'] = '1';

        $container = $this->compileContainer('prod');

        $this->assertTrue($container->getParameter('sylius_core.telemetry.enabled'));
        $this->assertTrue($container->has('sylius.telemetry.sender'));
        $this->assertTrue($container->has('sylius.telemetry.send_manager'));
    }

    /** @group dev */
    public function testDevWithTelemetryDisabledDoesNotHaveServicesRegistered(): void
    {
        $_ENV['SYLIUS_TELEMETRY_ENABLED'] = '0';
        $_SERVER['SYLIUS_TELEMETRY_ENABLED'] = '0';

        $container = $this->compileContainer('dev');

        $this->assertFalse($container->has('sylius.telemetry.sender'));
    }

    /** @group dev */
    public function testTestEnvWithTelemetryDisabledDoesNotHaveServicesRegistered(): void
    {
        $_ENV['SYLIUS_TELEMETRY_ENABLED'] = '0';
        $_SERVER['SYLIUS_TELEMETRY_ENABLED'] = '0';

        $container = $this->compileContainer('test');

        $this->assertFalse($container->has('sylius.telemetry.sender'));
    }

    private function compileContainer(string $env): ContainerBuilder
    {
        $kernel = new Kernel($env, true);

        $initBundles = new \ReflectionMethod($kernel, 'initializeBundles');
        $initBundles->invoke($kernel);

        $buildContainer = new \ReflectionMethod($kernel, 'buildContainer');

        /** @var ContainerBuilder $container */
        $container = $buildContainer->invoke($kernel);
        $container->compile();

        return $container;
    }
}
