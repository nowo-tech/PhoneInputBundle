<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Phone;

use Nowo\PhoneInputBundle\Country\CountryProvider;

/**
 * Parses and normalizes E.164 phone numbers using the country catalog.
 */
final class E164Parser
{
    public function __construct(
        private readonly CountryProvider $countryProvider,
    ) {
    }

    /**
     * @return array{iso: string, prefix: string, national_number: string}
     */
    public function parse(string $value, string $defaultCountryIso = 'ES'): array
    {
        $value = $this->normalizeDigits($value);

        if ('' === $value) {
            return $this->emptyParts($defaultCountryIso);
        }

        if (str_starts_with($value, '+')) {
            $matched = $this->matchDialCode($value);
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
     * @return array{iso: string, prefix: string, national_number: string}|null
     */
    private function matchDialCode(string $value): ?array
    {
        foreach ($this->countryProvider->getCountriesSortedByDialCodeLength() as $country) {
            $prefix = $country->dialCode;
            if (!str_starts_with($value, $prefix)) {
                continue;
            }

            $national = substr($value, \strlen($prefix));
            $national = ltrim($national, '0');

            return [
                'iso' => $country->iso,
                'prefix' => $prefix,
                'national_number' => $national,
            ];
        }

        return null;
    }

    private function normalizeDigits(string $value): string
    {
        $value = trim($value);

        return preg_replace('/[^\d+]/', '', $value) ?? '';
    }
}
