<?php

declare(strict_types=1);

namespace Sylius\Telemetry\Tests\Functional;

use Sylius\Telemetry\Tests\Double\InMemoryTelemetrySender;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TelemetryActivationTest extends WebTestCase
{
    protected function tearDown(): void
    {
        restore_exception_handler();
        unset($_ENV['SYLIUS_TELEMETRY_ENABLED'], $_SERVER['SYLIUS_TELEMETRY_ENABLED']);

        parent::tearDown();
    }

    /** @group smoke */
    public function testAdminLoginInProdTriggersTelemetry(): void
    {
        $client = static::createClient(['environment' => 'prod', 'debug' => true]);
        $client->request('GET', '/admin/login');

        $this->assertResponseIsSuccessful();

        /** @var InMemoryTelemetrySender $sender */
        $sender = static::getContainer()->get('sylius.telemetry.sender');
        $this->assertTrue($sender->wasCalled(), 'Telemetry sender should have been called on admin page request in prod.');
    }

    /** @group smoke */
    public function testAdminLoginInProdTriggersTelemetryEvenWhenDisabledViaEnv(): void
    {
        $_ENV['SYLIUS_TELEMETRY_ENABLED'] = '0';
        $_SERVER['SYLIUS_TELEMETRY_ENABLED'] = '0';

        $client = static::createClient(['environment' => 'prod', 'debug' => true]);
        $client->request('GET', '/admin/login');

        $this->assertResponseIsSuccessful();

        /** @var InMemoryTelemetrySender $sender */
        $sender = static::getContainer()->get('sylius.telemetry.sender');
        $this->assertTrue($sender->wasCalled(), 'Telemetry sender should have been called even when env disables it.');
    }
}
