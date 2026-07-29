<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests;

use Nowo\PhoneInputBundle\Country\CountryProvider;
use Nowo\PhoneInputBundle\Phone\E164Parser;

final class TestFixtures
{
    public static function countriesFile(): string
    {
        return __DIR__.'/Fixtures/countries.json';
    }

    /**
     * @param list<string> $preferred
     * @param list<string> $allowed
     * @param list<string> $excluded
     */
    public static function countryProvider(
        string $defaultCountry = 'ES',
        array $preferred = ['ES', 'FR'],
        array $allowed = [],
        array $excluded = [],
    ): CountryProvider {
        return new CountryProvider(
            self::countriesFile(),
            $defaultCountry,
            array_values($preferred),
            array_values($allowed),
            array_values($excluded),
        );
    }

    public static function e164Parser(?CountryProvider $provider = null): E164Parser
    {
        return new E164Parser($provider ?? self::countryProvider());
    }
}
