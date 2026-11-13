<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Country;

/**
 * Country catalog port for the phone prefix selector, the E.164 parser and the bundled patterns (since 1.7.0, REQ-ARCH-001
 * rule 1). The default, {@see CountryProvider}, reads the bundled `countries.json` filtered by the bundle
 * configuration (`allowed_countries`, `excluded_countries`, `preferred_countries`, `default_country`). Alias this
 * interface to your own service to serve the catalog from somewhere else.
 */
interface CountryProviderInterface
{
    /**
     * The configured default country (or the first catalog country when it is unknown).
     */
    public function getDefaultCountry(): Country;

    /**
     * A catalog country by ISO 3166-1 alpha-2 code (case-insensitive), or null.
     */
    public function getByIso(string $iso): ?Country;

    /**
     * The catalog filtered by the configured allowed / excluded countries, keyed by ISO code.
     *
     * @return array<string, Country>
     */
    public function getAllCountries(): array;

    /**
     * The selector list: filtered, preferred countries first. A null argument uses the bundle configuration.
     *
     * @param list<string>|null $allowedCountries
     * @param list<string>|null $excludedCountries
     * @param list<string>|null $preferredCountries
     *
     * @return list<Country>
     */
    public function getCountriesForSelector(
        ?array $allowedCountries = null,
        ?array $excludedCountries = null,
        ?array $preferredCountries = null,
    ): array;

    /**
     * Every catalog country, longest dial code first (prefix matching).
     *
     * @return list<Country>
     */
    public function getCountriesSortedByDialCodeLength(): array;

    /**
     * The catalog countries that use a dial code (with or without the leading `+`).
     *
     * @return list<Country>
     */
    public function getCountriesByDialCode(string $dialCode): array;
}
