<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Phone;

use Nowo\PhoneInputBundle\Country\CountryProvider;

/**
 * Loads bundled national-number patterns per ISO country code.
 */
final class PhonePatternCatalog
{
    /** @var array<string, PhonePattern>|null */
    private ?array $countryPatterns = null;

    private ?PhonePattern $defaultPattern = null;

    /** @var array<string, PhonePattern>|null */
    private ?array $prefixPatterns = null;

    public function __construct(
        private readonly string $patternsFile,
        private readonly CountryProvider $countryProvider,
    ) {
    }

    public function forCountry(string $iso): PhonePattern
    {
        $this->load();

        $iso = strtoupper($iso);

        return $this->countryPatterns[$iso] ?? $this->defaultPattern ?? new PhonePattern(4, 14, '^\d{4,14}$');
    }

    public function forPrefix(string $prefix): PhonePattern
    {
        $this->load();

        $normalizedPrefix = str_starts_with($prefix, '+') ? $prefix : '+'.$prefix;

        if (isset($this->prefixPatterns[$normalizedPrefix])) {
            return $this->prefixPatterns[$normalizedPrefix];
        }

        $countries = $this->countryProvider->getCountriesByDialCode($normalizedPrefix);
        if (1 === \count($countries)) {
            return $this->forCountry($countries[0]->iso);
        }

        return $this->defaultPattern ?? new PhonePattern(4, 14, '^\d{4,14}$');
    }

    public function defaultPattern(): PhonePattern
    {
        $this->load();

        return $this->defaultPattern ?? new PhonePattern(4, 14, '^\d{4,14}$');
    }

    private function load(): void
    {
        if (null !== $this->countryPatterns) {
            return;
        }

        if (!is_readable($this->patternsFile)) {
            $this->defaultPattern = new PhonePattern(4, 14, '^\d{4,14}$');
            $this->countryPatterns = [];
            $this->prefixPatterns = [];

            return;
        }

        /** @var array{
         *     default?: array{min_length?: int, max_length?: int, pattern?: string},
         *     countries?: array<string, array{min_length?: int, max_length?: int, pattern?: string}>,
         *     prefixes?: array<string, array{min_length?: int, max_length?: int, pattern?: string}>
         * } $data
         */
        $data = json_decode((string) file_get_contents($this->patternsFile), true, 512, \JSON_THROW_ON_ERROR);

        $this->defaultPattern = $this->createPattern($data['default'] ?? []);
        $this->countryPatterns = [];
        foreach ($data['countries'] ?? [] as $iso => $patternData) {
            $this->countryPatterns[strtoupper((string) $iso)] = $this->createPattern($patternData);
        }

        $this->prefixPatterns = [];
        foreach ($data['prefixes'] ?? [] as $prefix => $patternData) {
            $normalizedPrefix = str_starts_with((string) $prefix, '+') ? (string) $prefix : '+'.$prefix;
            $this->prefixPatterns[$normalizedPrefix] = $this->createPattern($patternData);
        }
    }

    /**
     * @param array{min_length?: int, max_length?: int, pattern?: string} $patternData
     */
    private function createPattern(array $patternData): PhonePattern
    {
        return new PhonePattern(
            minLength: (int) ($patternData['min_length'] ?? 4),
            maxLength: (int) ($patternData['max_length'] ?? 14),
            pattern: (string) ($patternData['pattern'] ?? '^\d{4,14}$'),
        );
    }
}
