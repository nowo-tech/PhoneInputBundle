<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\IconSupport;

use Nowo\PhoneInputBundle\IconSupport\IconSupportChecker;
use PHPUnit\Framework\TestCase;

final class IconSupportCheckerTest extends TestCase
{
    public function testExplicitAvailabilityFlags(): void
    {
        $checker = new IconSupportChecker(uxIconsAvailable: true, httpClientAvailable: true);

        $this->assertTrue($checker->isUxIconsAvailable());
        $this->assertTrue($checker->isHttpClientAvailable());
        $this->assertTrue($checker->isIconRenderingSupported());
    }

    public function testMissingUxIcons(): void
    {
        $checker = new IconSupportChecker(uxIconsAvailable: false, httpClientAvailable: true);

        $this->assertFalse($checker->isIconRenderingSupported());
    }

    public function testDetectsLegacyUxIconsRuntimeClass(): void
    {
        $checker = new IconSupportChecker(
            classExistsChecker: static fn (string $class): bool => 'Symfony\UX\Icons\Twig\UXIconsRuntime' === $class,
        );

        $this->assertTrue($checker->isUxIconsAvailable());
    }

    public function testUxIconsNotDetectedWhenNoRuntimeClass(): void
    {
        $checker = new IconSupportChecker(
            classExistsChecker: static fn (string $class): bool => false,
        );

        $this->assertFalse($checker->isUxIconsAvailable());
    }
}
