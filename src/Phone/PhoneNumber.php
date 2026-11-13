<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Phone;

/**
 * Phone number value object: ISO country, dial prefix and national number (since 1.7.0). Plain PHP with no framework
 * type, so an adapter or a domain can depend on it.
 *
 * `ValueFormat::OBJECT` still returns the deprecated form-layer subclass `Form\Model\PhoneNumber`, which IS a
 * `Phone\PhoneNumber`: type your code against this class. Not meant to be extended outside the bundle (it is not final
 * only so that the deprecated subclass can exist until the next major).
 */
readonly class PhoneNumber
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
