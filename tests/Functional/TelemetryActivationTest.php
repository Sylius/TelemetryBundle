<?php

declare(strict_types=1);

namespace Sylius\TelemetryBundle\Tests\Functional;

use Sylius\TelemetryBundle\Tests\Spy\SpyTelemetrySender;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TelemetryActivationTest extends WebTestCase
{
    /** @group smoke */
    public function testAdminLoginInProdTriggersTelemetry(): void
    {
        $client = static::createClient(['environment' => 'prod', 'debug' => true]);
        $client->request('GET', '/admin/login');

        $this->assertResponseIsSuccessful();

        /** @var SpyTelemetrySender $sender */
        $sender = static::getContainer()->get('sylius.telemetry.sender');
        $this->assertTrue($sender->wasCalled(), 'Telemetry sender should have been called on admin page request in prod.');
    }

    public function testAdminLoginInTestDoesNotTriggerTelemetry(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/login');

        $this->assertResponseIsSuccessful();

        $container = static::getContainer();
        $this->assertFalse(
            $container->has('sylius.telemetry.send_manager'),
            'Telemetry send manager should not be registered in test environment.',
        );
    }
}
