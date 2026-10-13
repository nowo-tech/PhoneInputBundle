<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\Phone;

use Nowo\PhoneInputBundle\Country\CountryProvider;
use Nowo\PhoneInputBundle\Phone\E164Parser;
use PHPUnit\Framework\TestCase;

/**
 * +1 is shared by US and CA in the fixture (US listed first).
 */
final class E164ParserSharedDialCodeTest extends TestCase
{
    public function testFirstCatalogCountryWinsWithoutPreference(): void
    {
        $this->assertSame('US', $this->parser()->parse('+14165550123')['iso']);
    }

    public function testPreferredIsoSharingTheDialCodeIsKept(): void
    {
        $this->assertSame([
            'iso' => 'CA',
            'prefix' => '+1',
            'national_number' => '4165550123',
        ], $this->parser()->parse('+14165550123', 'ES', ['ca']));
    }

    public function testDefaultCountrySharingTheDialCodeIsKept(): void
    {
        $this->assertSame('CA', $this->parser()->parse('+14165550123', 'CA')['iso']);
    }

    public function testPreferredIsosAreTriedInOrderBeforeTheDefault(): void
    {
        $this->assertSame('US', $this->parser()->parse('+12125550123', 'CA', ['ES', 'US'])['iso']);
    }

    public function testPreferenceWithAnotherDialCodeIsIgnored(): void
    {
        $this->assertSame('ES', $this->parser()->parse('+34612345678', 'CA', ['US'])['iso']);
    }

    private function parser(): E164Parser
    {
        return new E164Parser(new CountryProvider(__DIR__.'/../../Fixtures/countries-multi-plus-one.json', 'ES'));
    }
}
