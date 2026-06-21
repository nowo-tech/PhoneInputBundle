<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Integration;

use Nowo\PhoneInputBundle\DependencyInjection\NowoPhoneInputExtension;
use Nowo\PhoneInputBundle\Form\Type\PhoneType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class BundleServicesIntegrationTest extends TestCase
{
    public function testExtensionLoadsPhoneTypeInContainer(): void
    {
        $container = new ContainerBuilder();
        (new NowoPhoneInputExtension())->load([], $container);

        $this->assertTrue($container->hasDefinition(PhoneType::class));
        $this->assertTrue($container->hasParameter('nowo_phone_input.defaults'));
    }
}
