<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\Country;

use Nowo\PhoneInputBundle\Country\Country;
use Nowo\PhoneInputBundle\Country\CountryProvider;
use Nowo\PhoneInputBundle\Tests\TestFixtures;
use PHPUnit\Framework\TestCase;

final class CountryProviderTest extends TestCase
{
    public function testPreferredCountriesAreListedFirst(): void
    {
        $provider = TestFixtures::countryProvider();
        $countries = $provider->getCountriesForSelector();

        $this->assertSame('ES', $countries[0]->iso);
        $this->assertSame('FR', $countries[1]->iso);
    }

    public function testAllowedCountriesFilter(): void
    {
        $provider = TestFixtures::countryProvider(allowed: ['ES']);
        $countries = $provider->getAllCountries();

        $this->assertCount(1, $countries);
        $this->assertArrayHasKey('ES', $countries);
    }

    public function testExcludedCountriesFilter(): void
    {
        $provider = TestFixtures::countryProvider(excluded: ['FR', 'GB']);
        $countries = $provider->getAllCountries();

        $this->assertArrayNotHasKey('FR', $countries);
        $this->assertArrayNotHasKey('GB', $countries);
        $this->assertArrayHasKey('ES', $countries);
    }

    public function testFieldLevelAllowedCountriesOverride(): void
    {
        $provider = TestFixtures::countryProvider(excluded: ['FR']);
        $countries = $provider->getCountriesForSelector(['ES', 'GB'], []);

        $this->assertCount(2, $countries);
        $this->assertSame('ES', $countries[0]->iso);
        $this->assertSame('GB', $countries[1]->iso);
    }

    public function testFieldLevelExcludedCountriesOverride(): void
    {
        $provider = TestFixtures::countryProvider();
        $countries = $provider->getCountriesForSelector(null, ['FR']);
        $isos = array_map(static fn (Country $country): string => $country->iso, $countries);

        $this->assertNotContains('FR', $isos);
        $this->assertContains('ES', $isos);
    }

    public function testGetDefaultCountry(): void
    {
        $provider = TestFixtures::countryProvider(defaultCountry: 'FR');

        $this->assertSame('FR', $provider->getDefaultCountry()->iso);
    }

    public function testGetByIsoReturnsNullForUnknownCountry(): void
    {
        $provider = TestFixtures::countryProvider();

        $this->assertNull($provider->getByIso('ZZ'));
    }

    public function testFieldLevelPreferredCountriesOverride(): void
    {
        $provider = TestFixtures::countryProvider(preferred: ['ES']);
        $countries = $provider->getCountriesForSelector(null, null, ['GB', 'ES']);

        $this->assertSame('GB', $countries[0]->iso);
        $this->assertSame('ES', $countries[1]->iso);
    }

    public function testGetRawCountriesThrowsOnInvalidJson(): void
    {
        $path = sys_get_temp_dir().'/nowo-phone-countries-invalid-'.uniqid('', true).'.json';
        file_put_contents($path, 'not-json');

        $this->expectException(\InvalidArgumentException::class);

        try {
            (new CountryProvider($path))->getAllCountries();
        } finally {
            @unlink($path);
        }
    }

    public function testGetRawCountriesThrowsOnMissingFile(): void
    {
        $provider = new CountryProvider('/tmp/nowo-phone-countries-missing-'.uniqid('', true).'.json');

        $this->expectException(\InvalidArgumentException::class);
        @$provider->getAllCountries();
    }

    public function testCountryFromArray(): void
    {
        $country = Country::fromArray([
            'iso' => 'es',
            'name' => 'Spain',
            'dial_code' => '+34',
            'flag' => '🇪🇸',
        ]);

        $this->assertSame('ES', $country->iso);
        $this->assertSame('circle-flags:es', $country->flagIcon);
    }

    public function testGetCountriesByDialCode(): void
    {
        $provider = TestFixtures::countryProvider();
        $matches = $provider->getCountriesByDialCode('34');

        $this->assertCount(1, $matches);
        $this->assertSame('ES', $matches[0]->iso);
    }

    public function testGetCountriesSortedByDialCodeLengthCachesResult(): void
    {
        $provider = TestFixtures::countryProvider();
        $first = $provider->getCountriesSortedByDialCodeLength();
        $second = $provider->getCountriesSortedByDialCodeLength();

        $this->assertSame($first, $second);
        $this->assertGreaterThanOrEqual(2, \count($first));
    }

    public function testSelectorWithoutPreferredCountriesSortsAlphabetically(): void
    {
        $provider = TestFixtures::countryProvider(preferred: []);
        $countries = $provider->getCountriesForSelector();

        $this->assertGreaterThan(1, \count($countries));
        $names = array_map(static fn (Country $country): string => $country->name, $countries);
        $sorted = $names;
        sort($sorted);
        $this->assertSame($sorted, $names);
    }
}
