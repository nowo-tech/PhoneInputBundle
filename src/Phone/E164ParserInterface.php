<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Phone;

/**
 * Splits a phone value into ISO country, dial prefix and national number (since 1.7.0, REQ-ARCH-001 rule 1). The
 * default is {@see E164Parser}, backed by the country catalog.
 */
interface E164ParserInterface
{
    /**
     * When several countries share a dial code, the ISO is the first of $preferredIsos, then the default country,
     * that uses it; otherwise the first catalog country with that code.
     *
     * @param list<string> $preferredIsos
     *
     * @return array{iso: string, prefix: string, national_number: string}
     */
    public function parse(string $value, string $defaultCountryIso = 'ES', array $preferredIsos = []): array;

    /**
     * The parts of an empty value for the default country (its ISO and prefix, empty national number).
     *
     * @return array{iso: string, prefix: string, national_number: string}
     */
    public function emptyParts(string $defaultCountryIso = 'ES'): array;
}
