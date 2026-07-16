<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\Phone;

use Nowo\PhoneInputBundle\Phone\PhonePatternCatalog;
use Nowo\PhoneInputBundle\Tests\TestFixtures;
use PHPUnit\Framework\TestCase;

final class PhonePatternCatalogTest extends TestCase
{
    public function testForPrefixUsesExplicitPrefixPattern(): void
    {
        $provider = TestFixtures::countryProvider();
        $catalog = new PhonePatternCatalog(
            __DIR__.'/../../../src/Resources/data/phone_patterns.json',
            $provider,
        );

        $pattern = $catalog->forPrefix('+34');

        $this->assertTrue($pattern->matches('612345678'));
    }

    public function testForPrefixWithoutLeadingPlusIsNormalized(): void
    {
        $provider = TestFixtures::countryProvider();
        $catalog = new PhonePatternCatalog(
            __DIR__.'/../../../src/Resources/data/phone_patterns.json',
            $provider,
        );

        $this->assertTrue($catalog->forPrefix('34')->matches('612345678'));
    }

    public function testForPrefixFallsBackToDefaultWhenMultipleCountriesShareDialCode(): void
    {
        $provider = new \Nowo\PhoneInputBundle\Country\CountryProvider(
            __DIR__.'/../Fixtures/countries-multi-plus-one.json',
        );
        $catalog = new PhonePatternCatalog(
            __DIR__.'/../../../src/Resources/data/phone_patterns.json',
            $provider,
        );

        $pattern = $catalog->forPrefix('+1');

        $this->assertTrue($pattern->matches('2125551234'));
    }
}
