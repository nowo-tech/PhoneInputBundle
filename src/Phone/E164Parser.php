<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Phone;

use Nowo\PhoneInputBundle\Country\CountryProvider;

/**
 * Parses and normalizes E.164 phone numbers using the country catalog.
 */
final readonly class E164Parser
{
    public function __construct(
        private CountryProvider $countryProvider,
    ) {
    }

    /**
     * Several countries share a dial code (+1 US/CA/PR/DO…, +7 RU/KZ). The ISO is then the first of
     * $preferredIsos (in order), then the default country, that uses the matched code; otherwise the
     * first catalog country with that code is returned.
     *
     * @param list<string> $preferredIsos e.g. the ISO selected in the form, preferred/allowed selector countries
     *
     * @return array{iso: string, prefix: string, national_number: string}
     */
    public function parse(string $value, string $defaultCountryIso = 'ES', array $preferredIsos = []): array
    {
        $value = $this->normalizeDigits($value);

        if ('' === $value) {
            return $this->emptyParts($defaultCountryIso);
        }

        if (str_starts_with($value, '+')) {
            $matched = $this->matchDialCode($value, [...$preferredIsos, $defaultCountryIso]);
            if (null !== $matched) {
                return $matched;
            }
        }

        $defaultCountry = $this->countryProvider->getByIso($defaultCountryIso)
            ?? $this->countryProvider->getDefaultCountry();

        return [
            'iso' => $defaultCountry->iso,
            'prefix' => $defaultCountry->dialCode,
            'national_number' => ltrim($value, '0'),
        ];
    }

    /**
     * @return array{iso: string, prefix: string, national_number: string}
     */
    public function emptyParts(string $defaultCountryIso = 'ES'): array
    {
        $defaultCountry = $this->countryProvider->getByIso($defaultCountryIso)
            ?? $this->countryProvider->getDefaultCountry();

        return [
            'iso' => $defaultCountry->iso,
            'prefix' => $defaultCountry->dialCode,
            'national_number' => '',
        ];
    }

    /**
     * @param list<string> $preferenceOrder
     *
     * @return array{iso: string, prefix: string, national_number: string}|null
     */
    private function matchDialCode(string $value, array $preferenceOrder): ?array
    {
        $prefix = null;
        $iso = null;
        /** @var array<string, true> $candidates ISO codes sharing the matched dial code, catalog order */
        $candidates = [];

        foreach ($this->countryProvider->getCountriesSortedByDialCodeLength() as $country) {
            if (null === $prefix) {
                if (!str_starts_with($value, $country->dialCode)) {
                    continue;
                }
                $prefix = $country->dialCode;
                $iso = $country->iso;
            } elseif ($country->dialCode !== $prefix) {
                continue;
            }

            $candidates[$country->iso] = true;
        }

        if (null === $prefix || null === $iso) {
            return null;
        }

        foreach ($preferenceOrder as $preferred) {
            $preferred = strtoupper($preferred);
            if (isset($candidates[$preferred])) {
                $iso = $preferred;
                break;
            }
        }

        return [
            'iso' => $iso,
            'prefix' => $prefix,
            'national_number' => ltrim(substr($value, \strlen($prefix)), '0'),
        ];
    }

    private function normalizeDigits(string $value): string
    {
        $value = trim($value);

        return preg_replace('/[^\d+]/', '', $value) ?? '';
    }
}
