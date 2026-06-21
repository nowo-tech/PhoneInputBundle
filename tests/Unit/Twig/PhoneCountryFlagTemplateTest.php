<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\Twig;

use PHPUnit\Framework\TestCase;

final class PhoneCountryFlagTemplateTest extends TestCase
{
    public function testFlagPartialDoesNotReferenceUxIconTwigFunction(): void
    {
        $content = (string) file_get_contents(__DIR__.'/../../../src/Resources/views/Form/_phone_country_flag.html.twig');

        $this->assertDoesNotMatchRegularExpression('/\bux_icon\s*\(/', $content);
        $this->assertStringContainsString('nowo_phone_country_flag', $content);
    }
}
