<?php

declare(strict_types=1);

namespace Sylius\TelemetryBundle\Tests\Double;

use Sylius\Component\Core\Telemetry\Sender\TelemetrySenderInterface;

final class InMemoryTelemetrySender implements TelemetrySenderInterface
{
    /** @var list<array<string, mixed>> */
    private array $calls = [];

    public function send(array $telemetryData): bool
    {
        $this->calls[] = $telemetryData;

        return true;
    }

    public function wasCalled(): bool
    {
        return $this->calls !== [];
    }

    public function getCallCount(): int
    {
        return count($this->calls);
    }

    /** @return list<array<string, mixed>> */
    public function getCalls(): array
    {
        return $this->calls;
    }

    public function reset(): void
    {
        $this->calls = [];
    }
}
