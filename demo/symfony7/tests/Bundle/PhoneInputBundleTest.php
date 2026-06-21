<?php

declare(strict_types=1);

namespace App\Tests\Bundle;

use Nowo\PhoneInputBundle\Form\Type\PhoneType;
use Nowo\PhoneInputBundle\NowoPhoneInputBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PhoneInputBundleTest extends KernelTestCase
{
    public function testBundleIsRegistered(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $this->assertTrue($container->has(PhoneType::class));
    }

    public function testBundleClass(): void
    {
        $this->assertSame('nowo_phone_input', (new NowoPhoneInputBundle())->getContainerExtension()?->getAlias());
    }
}
