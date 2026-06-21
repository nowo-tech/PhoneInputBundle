<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\Twig;

use Nowo\PhoneInputBundle\Form\FlagDisplay;
use Nowo\PhoneInputBundle\IconSupport\IconSupportChecker;
use Nowo\PhoneInputBundle\Twig\CountryFlagExtension;
use Nowo\PhoneInputBundle\Twig\CountryFlagRenderer;
use PHPUnit\Framework\TestCase;

final class CountryFlagExtensionTest extends TestCase
{
    public function testGetFunctionsRegistersCountryFlagHelper(): void
    {
        $extension = new CountryFlagExtension(new CountryFlagRenderer(new IconSupportChecker()));

        $functions = $extension->getFunctions();
        $this->assertCount(1, $functions);
        $this->assertSame('nowo_phone_country_flag', $functions[0]->getName());
    }

    public function testRenderCountryFlagDelegatesToRenderer(): void
    {
        $extension = new CountryFlagExtension(new CountryFlagRenderer(new IconSupportChecker()));

        $html = $extension->renderCountryFlag(['iso' => 'ES'], FlagDisplay::CSS_ICON->value);

        $this->assertStringContainsString('fi-es', $html);
    }
}
