<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit;

use Nowo\PhoneInputBundle\NowoPhoneInputBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class NowoPhoneInputBundleTest extends TestCase
{
    public function testBuildRegistersTwigCompilerPass(): void
    {
        $bundle = new NowoPhoneInputBundle();
        $container = new ContainerBuilder();

        $bundle->build($container);

        $this->assertNotEmpty($container->getCompilerPassConfig()->getPasses());
    }

    public function testGetContainerExtension(): void
    {
        $bundle = new NowoPhoneInputBundle();

        $this->assertSame('nowo_phone_input', $bundle->getContainerExtension()?->getAlias());
    }
}
