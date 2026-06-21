<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Phone;

use Nowo\PhoneInputBundle\Form\Model\PhoneNumber;
use Nowo\PhoneInputBundle\Validation\PhoneValidationMode;

/**
 * Validates phone values using country/prefix rules and optional libphonenumber.
 */
final class PhoneValidator
{
    public function __construct(
        private readonly E164Parser $e164Parser,
        private readonly PhonePatternCatalog $patternCatalog,
        private readonly bool $useLibPhoneNumber = true,
    ) {
    }

    public function isValid(mixed $value, PhoneValidationMode $mode, string $defaultCountryIso = 'ES'): bool
    {
        if ($this->isEmpty($value)) {
            return true;
        }

        $parts = $this->resolveParts($value, $defaultCountryIso);

        if ('' === $parts['national_number']) {
            return true;
        }

        if ($this->useLibPhoneNumber && class_exists(\libphonenumber\PhoneNumberUtil::class)) {
            return $this->isValidWithLibPhoneNumber($parts['iso'], $parts['national_number']);
        }

        $pattern = match ($mode) {
            PhoneValidationMode::PREFIX => $this->patternCatalog->forPrefix($parts['prefix']),
            PhoneValidationMode::COUNTRY => $this->patternCatalog->forCountry($parts['iso']),
            PhoneValidationMode::NONE => $this->patternCatalog->defaultPattern(),
        };

        return $pattern->matches($parts['national_number']);
    }

    /**
     * @return array{iso: string, prefix: string, national_number: string}
     */
    private function resolveParts(mixed $value, string $defaultCountryIso): array
    {
        if ($value instanceof PhoneNumber) {
            return $value->toSeparatedArray();
        }

        if (\is_array($value)) {
            $iso = strtoupper((string) ($value['iso'] ?? $defaultCountryIso));
            $prefix = (string) ($value['prefix'] ?? '');
            $national = $this->normalizeNationalNumber((string) ($value['national_number'] ?? ''));

            if ('' === $prefix) {
                $parts = $this->e164Parser->parse($national, $iso);

                return [
                    'iso' => $iso,
                    'prefix' => $parts['prefix'],
                    'national_number' => $parts['national_number'],
                ];
            }

            return [
                'iso' => $iso,
                'prefix' => $prefix,
                'national_number' => $national,
            ];
        }

        if (\is_string($value)) {
            return $this->e164Parser->parse($value, $defaultCountryIso);
        }

        return $this->e164Parser->emptyParts($defaultCountryIso);
    }

    private function isValidWithLibPhoneNumber(string $iso, string $nationalNumber): bool
    {
        try {
            $utilClass = 'libphonenumber\PhoneNumberUtil';
            if (!class_exists($utilClass)) {
                return false;
            }

            /** @var object $util */
            $util = forward_static_call([$utilClass, 'getInstance']);
            $parsed = $util->parse($nationalNumber, $iso);

            return $util->isValidNumber($parsed);
        } catch (\Throwable) {
            return false;
        }
    }

    private function normalizeNationalNumber(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/[^\d+]/', '', $value) ?? '';

        if (str_starts_with($value, '+')) {
            $parts = $this->e164Parser->parse($value, 'ES');

            return $parts['national_number'];
        }

        return ltrim($value, '0');
    }

    private function isEmpty(mixed $value): bool
    {
        if (null === $value || '' === $value) {
            return true;
        }

        if (\is_array($value)) {
            return '' === trim((string) ($value['national_number'] ?? ''));
        }

        if ($value instanceof PhoneNumber) {
            return '' === $value->nationalNumber;
        }

        return false;
    }
}
