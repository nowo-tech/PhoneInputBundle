<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Country;

/**
 * Immutable country metadata for phone prefix selection.
 */
final readonly class Country
{
    public function __construct(
        public string $iso,
        public string $name,
        public string $dialCode,
        public string $flagEmoji = '',
        public string $flagIcon = '',
    ) {
    }

    /**
     * @param array{iso: string, name: string, dial_code: string, flag?: string, flag_icon?: string} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            iso: strtoupper($data['iso']),
            name: $data['name'],
            dialCode: $data['dial_code'],
            flagEmoji: $data['flag'] ?? '',
            flagIcon: $data['flag_icon'] ?? ('circle-flags:'.strtolower($data['iso'])),
        );
    }

    /**
     * @return array{iso: string, name: string, dial_code: string, flag: string, flag_icon: string}
     */
    public function toArray(): array
    {
        return [
            'iso' => $this->iso,
            'name' => $this->name,
            'dial_code' => $this->dialCode,
            'flag' => $this->flagEmoji,
            'flag_icon' => $this->flagIcon,
        ];
    }
}
