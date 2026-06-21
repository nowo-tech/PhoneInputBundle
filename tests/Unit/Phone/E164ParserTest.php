<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\Phone;

use Nowo\PhoneInputBundle\Tests\TestFixtures;
use PHPUnit\Framework\TestCase;

final class E164ParserTest extends TestCase
{
    public function testParseE164Number(): void
    {
        $parser = TestFixtures::e164Parser();

        $this->assertSame([
            'iso' => 'ES',
            'prefix' => '+34',
            'national_number' => '612345678',
        ], $parser->parse('+34612345678'));
    }

    public function testParseNationalNumberUsesDefaultCountry(): void
    {
        $parser = TestFixtures::e164Parser();

        $this->assertSame([
            'iso' => 'ES',
            'prefix' => '+34',
            'national_number' => '612345678',
        ], $parser->parse('612345678'));
    }

    public function testEmptyPartsUsesDefaultCountry(): void
    {
        $parser = TestFixtures::e164Parser();

        $this->assertSame('ES', $parser->emptyParts()['iso']);
        $this->assertSame('+34', $parser->emptyParts()['prefix']);
    }
}
