<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Form\DataTransformer;

use Nowo\PhoneInputBundle\Country\CountryProvider;
use Nowo\PhoneInputBundle\Form\Model\PhoneNumber;
use Nowo\PhoneInputBundle\Form\ValueFormat;
use Nowo\PhoneInputBundle\Phone\E164Parser;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Normalizes phone data between model values and compound form view data.
 *
 * @implements DataTransformerInterface<string|array<string, string>|PhoneNumber|null, array<string, string|null>>
 */
final class PhoneNumberTransformer implements DataTransformerInterface
{
    public function __construct(
        private readonly ValueFormat $valueFormat,
        private readonly bool $countryPrefixSelector,
        private readonly CountryProvider $countryProvider,
        private readonly E164Parser $e164Parser,
        private readonly string $defaultCountryIso,
    ) {
    }

    /**
     * @param string|array<string, string>|PhoneNumber|null $value
     *
     * @return array<string, string|null>
     */
    public function transform(mixed $value): array
    {
        $parts = $this->resolveParts($value);

        if (!$this->countryPrefixSelector) {
            return [
                'national_number' => $this->formatNationalForView($value, $parts),
            ];
        }

        return [
            'country_iso' => $parts['iso'],
            'national_number' => $parts['national_number'],
        ];
    }

    /**
     * @param array<string, string|null>|string|null $value
     *
     * @return string|array<string, string>|PhoneNumber|null
     */
    public function reverseTransform(mixed $value): mixed
    {
        if (!\is_array($value)) {
            throw new TransformationFailedException('Expected an array of phone field values.');
        }

        $nationalNumber = $this->normalizeNationalNumber((string) ($value['national_number'] ?? ''));
        $countryIso = strtoupper((string) ($value['country_iso'] ?? $this->defaultCountryIso));

        if (!$this->countryPrefixSelector) {
            return $this->buildModelWithoutSelector($nationalNumber);
        }

        $country = $this->countryProvider->getByIso($countryIso);
        if (!$country instanceof \Nowo\PhoneInputBundle\Country\Country) {
            throw new TransformationFailedException(\sprintf('Unknown country ISO code "%s".', $countryIso));
        }

        if ('' === $nationalNumber && $this->isEmptyAllowed()) {
            return $this->emptyModel();
        }

        $parts = [
            'iso' => $country->iso,
            'prefix' => $country->dialCode,
            'national_number' => $nationalNumber,
        ];

        return $this->buildModelFromParts($parts);
    }

    /**
     * @param string|array<string, string>|PhoneNumber|null $value
     *
     * @return array{iso: string, prefix: string, national_number: string}
     */
    private function resolveParts(mixed $value): array
    {
        if (null === $value || '' === $value || [] === $value) {
            return $this->e164Parser->emptyParts($this->defaultCountryIso);
        }

        if ($value instanceof PhoneNumber) {
            return $value->toSeparatedArray();
        }

        if (\is_array($value)) {
            $iso = strtoupper((string) ($value['iso'] ?? $this->defaultCountryIso));
            $prefix = (string) ($value['prefix'] ?? '');
            $national = (string) ($value['national_number'] ?? '');

            if ('' === $prefix) {
                $country = $this->countryProvider->getByIso($iso);
                $prefix = $country instanceof \Nowo\PhoneInputBundle\Country\Country ? $country->dialCode : '';
            }

            return [
                'iso' => $iso,
                'prefix' => $prefix,
                'national_number' => $national,
            ];
        }

        if (!\is_string($value)) {
            throw new TransformationFailedException('Unsupported phone model value.');
        }

        return $this->e164Parser->parse($value, $this->defaultCountryIso);
    }

    /**
     * @param string|array<string, string>|PhoneNumber|null               $originalValue
     * @param array{iso: string, prefix: string, national_number: string} $parts
     */
    private function formatNationalForView(mixed $originalValue, array $parts): string
    {
        if (\is_string($originalValue) && str_starts_with(trim($originalValue), '+')) {
            return trim($originalValue);
        }

        return $parts['national_number'];
    }

    private function normalizeNationalNumber(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/[^\d+]/', '', $value) ?? '';

        if (str_starts_with($value, '+')) {
            $parts = $this->e164Parser->parse($value, $this->defaultCountryIso);

            return $parts['national_number'];
        }

        return ltrim($value, '0');
    }

    /**
     * @return string|array<string, string>|PhoneNumber|null
     */
    private function buildModelWithoutSelector(string $nationalNumber): mixed
    {
        if ('' === $nationalNumber) {
            return $this->emptyModel();
        }

        if (str_starts_with($nationalNumber, '+')) {
            $parts = $this->e164Parser->parse($nationalNumber, $this->defaultCountryIso);

            return $this->buildModelFromParts($parts);
        }

        $defaultCountry = $this->countryProvider->getByIso($this->defaultCountryIso)
            ?? $this->countryProvider->getDefaultCountry();

        return $this->buildModelFromParts([
            'iso' => $defaultCountry->iso,
            'prefix' => $defaultCountry->dialCode,
            'national_number' => $nationalNumber,
        ]);
    }

    /**
     * @param array{iso: string, prefix: string, national_number: string} $parts
     *
     * @return string|array<string, string>|PhoneNumber
     */
    private function buildModelFromParts(array $parts): string|array|PhoneNumber
    {
        return match ($this->valueFormat) {
            ValueFormat::CONCATENATED => $parts['prefix'].$parts['national_number'],
            ValueFormat::SEPARATED => [
                'iso' => $parts['iso'],
                'prefix' => $parts['prefix'],
                'national_number' => $parts['national_number'],
            ],
            ValueFormat::OBJECT => new PhoneNumber(
                iso: $parts['iso'],
                prefix: $parts['prefix'],
                nationalNumber: $parts['national_number'],
            ),
        };
    }

    /**
     * @return string|array<string, string>|null
     */
    private function emptyModel(): string|array|null
    {
        return match ($this->valueFormat) {
            ValueFormat::CONCATENATED => '',
            ValueFormat::SEPARATED => [
                'iso' => '',
                'prefix' => '',
                'national_number' => '',
            ],
            ValueFormat::OBJECT => null,
        };
    }

    private function isEmptyAllowed(): bool
    {
        return true;
    }
}
