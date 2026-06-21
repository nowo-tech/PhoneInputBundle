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

    public static function countryProvider(
        string $defaultCountry = 'ES',
        /* @var list<string> $preferred */
        array $preferred = ['ES', 'FR'],
        /* @var list<string> $allowed */
        array $allowed = [],
        /* @var list<string> $excluded */
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
