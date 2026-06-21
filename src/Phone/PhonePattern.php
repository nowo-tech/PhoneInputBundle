<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Phone;

/**
 * National-number validation pattern for a country or prefix fallback.
 */
final readonly class PhonePattern
{
    public function __construct(
        public int $minLength,
        public int $maxLength,
        public string $pattern,
    ) {
    }

    public function matches(string $nationalNumber): bool
    {
        $length = \strlen($nationalNumber);
        if ($length < $this->minLength || $length > $this->maxLength) {
            return false;
        }

        return 1 === preg_match('/'.$this->pattern.'/', $nationalNumber);
    }
}
