<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Country;

/**
 * Loads and filters the bundled country catalog for phone prefix selection.
 */
final class CountryProvider
{
    /** @var array<string, Country>|null */
    private ?array $rawCountriesByIso = null;

    /** @var list<Country>|null */
    private ?array $countriesSortedByDialCode = null;

    public function __construct(
        private readonly string $countriesFile,
        private string $defaultCountryIso = 'ES',
        /** @var list<string> */
        private array $preferredCountries = [],
        /** @var list<string> */
        private array $allowedCountries = [],
        /** @var list<string> */
        private array $excludedCountries = [],
    ) {
        $this->defaultCountryIso = strtoupper($this->defaultCountryIso);
        $this->preferredCountries = $this->normalizeIsoList($this->preferredCountries);
        $this->allowedCountries = $this->normalizeIsoList($this->allowedCountries);
        $this->excludedCountries = $this->normalizeIsoList($this->excludedCountries);
    }

    public function getDefaultCountry(): Country
    {
        return $this->getByIso($this->defaultCountryIso)
            ?? array_values($this->getRawCountries())[0];
    }

    public function getByIso(string $iso): ?Country
    {
        $iso = strtoupper($iso);

        return $this->getRawCountries()[$iso] ?? null;
    }

    /**
     * @return array<string, Country>
     */
    public function getAllCountries(): array
    {
        return $this->filterCountries(
            $this->getRawCountries(),
            $this->allowedCountries,
            $this->excludedCountries,
        );
    }

    /**
     * @param list<string>|null $allowedCountries   When null, uses bundle configuration
     * @param list<string>|null $excludedCountries  When null, uses bundle configuration
     * @param list<string>|null $preferredCountries When null, uses bundle configuration
     *
     * @return list<Country>
     */
    public function getCountriesForSelector(
        ?array $allowedCountries = null,
        ?array $excludedCountries = null,
        ?array $preferredCountries = null,
    ): array {
        $allowed = null !== $allowedCountries
            ? $this->normalizeIsoList($allowedCountries)
            : $this->allowedCountries;
        $excluded = null !== $excludedCountries
            ? $this->normalizeIsoList($excludedCountries)
            : $this->excludedCountries;
        $preferred = null !== $preferredCountries
            ? $this->normalizeIsoList($preferredCountries)
            : $this->preferredCountries;

        $filtered = $this->filterCountries($this->getRawCountries(), $allowed, $excluded);

        return $this->orderCountries($filtered, $preferred);
    }

    /**
     * @return list<Country>
     */
    public function getCountriesSortedByDialCodeLength(): array
    {
        if (null !== $this->countriesSortedByDialCode) {
            return $this->countriesSortedByDialCode;
        }

        $countries = array_values($this->getRawCountries());
        usort(
            $countries,
            static fn (Country $a, Country $b): int => \strlen($b->dialCode) <=> \strlen($a->dialCode),
        );

        $this->countriesSortedByDialCode = $countries;

        return $this->countriesSortedByDialCode;
    }

    /**
     * @return list<Country>
     */
    public function getCountriesByDialCode(string $dialCode): array
    {
        $dialCode = str_starts_with($dialCode, '+') ? $dialCode : '+'.$dialCode;

        $matches = [];
        foreach ($this->getRawCountries() as $country) {
            if ($country->dialCode === $dialCode) {
                $matches[] = $country;
            }
        }

        return $matches;
    }

    /**
     * @return array<string, Country>
     */
    private function getRawCountries(): array
    {
        if (null !== $this->rawCountriesByIso) {
            return $this->rawCountriesByIso;
        }

        $json = file_get_contents($this->countriesFile);
        if (false === $json) {
            throw new \InvalidArgumentException(\sprintf('Unable to read countries file "%s".', $this->countriesFile));
        }

        /** @var list<array{iso: string, name: string, dial_code: string, flag?: string, flag_icon?: string}>|null $decoded */
        $decoded = json_decode($json, true);
        if (!\is_array($decoded)) {
            throw new \InvalidArgumentException(\sprintf('Invalid countries JSON in "%s".', $this->countriesFile));
        }

        $countries = [];
        foreach ($decoded as $entry) {
            $country = Country::fromArray($entry);
            $countries[$country->iso] = $country;
        }

        $this->rawCountriesByIso = $countries;

        return $this->rawCountriesByIso;
    }

    /**
     * @param array<string, Country> $countries
     * @param list<string>           $allowed
     * @param list<string>           $excluded
     *
     * @return array<string, Country>
     */
    private function filterCountries(array $countries, array $allowed, array $excluded): array
    {
        $filtered = [];

        foreach ($countries as $iso => $country) {
            if ([] !== $allowed && !\in_array($iso, $allowed, true)) {
                continue;
            }

            if (\in_array($iso, $excluded, true)) {
                continue;
            }

            $filtered[$iso] = $country;
        }

        return $filtered;
    }

    /**
     * @param array<string, Country> $countries
     * @param list<string>           $preferredCountries
     *
     * @return list<Country>
     */
    private function orderCountries(array $countries, array $preferredCountries): array
    {
        if ([] === $preferredCountries) {
            $ordered = array_values($countries);
            usort($ordered, static fn (Country $a, Country $b): int => $a->name <=> $b->name);

            return $ordered;
        }

        $preferred = [];
        $remaining = [];

        foreach ($countries as $country) {
            if (\in_array($country->iso, $preferredCountries, true)) {
                $preferred[$country->iso] = $country;
            } else {
                $remaining[] = $country;
            }
        }

        $orderedPreferred = [];
        foreach ($preferredCountries as $iso) {
            if (isset($preferred[$iso])) {
                $orderedPreferred[] = $preferred[$iso];
            }
        }

        usort($remaining, static fn (Country $a, Country $b): int => $a->name <=> $b->name);

        return [...$orderedPreferred, ...$remaining];
    }

    /**
     * @param list<string> $isoList
     *
     * @return list<string>
     */
    private function normalizeIsoList(array $isoList): array
    {
        return array_values(array_unique(array_map(strtoupper(...), $isoList)));
    }
}
