# Sylius Telemetry

Using official Sylius plugins constitutes agreement to Sylius telemetry data collection. This package activates telemetry as a condition of using officially maintained plugins.

For details on what data is collected, see the [Telemetry RFC](https://github.com/Sylius/Sylius/issues/18588).

## Usage (for official plugin authors)

```php
use Sylius\Telemetry\TelemetryCompilerPass;

final class YourPlugin extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TelemetryCompilerPass());
    }
}
```
