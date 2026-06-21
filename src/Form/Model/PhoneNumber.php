<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Form\Model;

/**
 * Phone number value object with separated country prefix and national number.
 */
final readonly class PhoneNumber
{
    public function __construct(
        public string $iso,
        public string $prefix,
        public string $nationalNumber,
    ) {
    }

    public function getE164(): string
    {
        return $this->prefix.$this->nationalNumber;
    }

    /**
     * @return array{iso: string, prefix: string, national_number: string}
     */
    public function toSeparatedArray(): array
    {
        return [
            'iso' => $this->iso,
            'prefix' => $this->prefix,
            'national_number' => $this->nationalNumber,
        ];
    }
}
