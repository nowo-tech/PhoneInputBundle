<?php

declare(strict_types=1);

namespace App;

/**
 * CSS framework presets for the Phone Input Bundle demo page.
 */
final class DemoFramework
{
    public const BOOTSTRAP5 = 'bootstrap5';

    public const TAILWIND2 = 'tailwind2';

    public const FOUNDATION6 = 'foundation6';

    public const SYMFONY_DEFAULT = 'symfony-default';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return [
            self::BOOTSTRAP5,
            self::TAILWIND2,
            self::FOUNDATION6,
            self::SYMFONY_DEFAULT,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function choices(): array
    {
        return [
            self::BOOTSTRAP5 => 'Bootstrap 5',
            self::TAILWIND2 => 'Tailwind CSS 2',
            self::FOUNDATION6 => 'Foundation 6',
            self::SYMFONY_DEFAULT => 'Symfony default',
        ];
    }

    public static function resolve(?string $value): string
    {
        if (null === $value || '' === $value) {
            return self::BOOTSTRAP5;
        }

        return \in_array($value, self::values(), true) ? $value : self::BOOTSTRAP5;
    }

    public static function pageClass(string $framework): string
    {
        return 'demo-page--'.$framework;
    }

    /**
     * @return array{
     *     container_classes: list<string>,
     *     prefix_selector_classes: list<string>,
     *     national_number_classes: list<string>
     * }
     */
    public static function phoneTypeClasses(string $framework): array
    {
        return match ($framework) {
            self::TAILWIND2 => [
                'container_classes' => ['nowo-phone-input'],
                'prefix_selector_classes' => ['nowo-phone-input__prefix'],
                'national_number_classes' => ['nowo-phone-input__number'],
            ],
            self::FOUNDATION6 => [
                'container_classes' => ['input-group', 'nowo-phone-input'],
                'prefix_selector_classes' => ['nowo-phone-input__prefix'],
                'national_number_classes' => ['input-group-field', 'nowo-phone-input__number'],
            ],
            self::SYMFONY_DEFAULT => [
                'container_classes' => ['nowo-phone-input'],
                'prefix_selector_classes' => ['nowo-phone-input__prefix'],
                'national_number_classes' => ['nowo-phone-input__number'],
            ],
            default => [
                'container_classes' => ['input-group', 'nowo-phone-input'],
                'prefix_selector_classes' => ['form-select', 'nowo-phone-input__prefix'],
                'national_number_classes' => ['form-control', 'nowo-phone-input__number'],
            ],
        };
    }
}
