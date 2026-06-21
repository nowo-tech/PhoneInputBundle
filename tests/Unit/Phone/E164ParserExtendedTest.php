<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\Phone;

use Nowo\PhoneInputBundle\Tests\TestFixtures;
use PHPUnit\Framework\TestCase;

final class E164ParserExtendedTest extends TestCase
{
    public function testParseUnknownInternationalPrefixFallsBackToDefaultCountry(): void
    {
        $parser = TestFixtures::e164Parser();

        $parts = $parser->parse('+9991234567890', 'ES');

        $this->assertSame('ES', $parts['iso']);
        $this->assertSame('+9991234567890', $parts['national_number']);
    }

    public function testParseStripsFormattingCharacters(): void
    {
        $parser = TestFixtures::e164Parser();

        $parts = $parser->parse('+34 612-345-678', 'ES');

        $this->assertSame('612345678', $parts['national_number']);
    }

    public function testParseEmptyStringReturnsEmptyNationalNumber(): void
    {
        $parser = TestFixtures::e164Parser();

        $parts = $parser->parse('   ', 'ES');

        $this->assertSame('', $parts['national_number']);
        $this->assertSame('ES', $parts['iso']);
    }
}
