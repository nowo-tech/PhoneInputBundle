<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\DependencyInjection;

use Nowo\PhoneInputBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

final class ConfigurationTest extends TestCase
{
    public function testDefaultConfiguration(): void
    {
        $config = $this->processConfiguration([]);

        $this->assertTrue($config['country_prefix_selector']);
        $this->assertSame('ES', $config['default_country']);
        $this->assertSame('CONCATENATED', $config['value_format']);
        $this->assertSame('FLAG_AND_PREFIX', $config['prefix_display']);
        $this->assertSame('CSS_ICON', $config['flag_display']);
        $this->assertSame('COUNTRY', $config['phone_validation']);
        $this->assertTrue($config['use_libphonenumber']);
    }

    /**
     * @param list<array<string, mixed>> $configs
     *
     * @return array<string, mixed>
     */
    private function processConfiguration(array $configs): array
    {
        $processor = new Processor();

        return $processor->processConfiguration(new Configuration(), $configs);
    }
}
