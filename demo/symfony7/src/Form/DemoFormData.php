<?php

declare(strict_types=1);

namespace App\Form;

use Nowo\PhoneInputBundle\Form\Model\PhoneNumber;

/**
 * Default model values pre-filled in the demo form on first load.
 */
final class DemoFormData
{
    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'phone_concatenated_with_prefix' => '+34612345678',
            'phone_concatenated_without_prefix' => '612345679',
            'phone_separated_with_prefix' => [
                'iso' => 'FR',
                'prefix' => '+33',
                'national_number' => '612345680',
            ],
            'phone_separated_without_prefix' => [
                'iso' => 'ES',
                'prefix' => '+34',
                'national_number' => '612345681',
            ],
            'phone_object_with_prefix' => new PhoneNumber('GB', '+44', '7911123456'),
            'phone_object_without_prefix' => new PhoneNumber('ES', '+34', '612345682'),
            'phone_with_prefix_search' => '+34612345684',
            'phone_selector_only' => '+351912345678',
            'phone_allowed_iberian' => '+351912345679',
            'phone_excluded_fr' => '+34612345682',
            'phone_without_flag' => '+34612345683',
            'phone_prefix_display_full' => '+34612345685',
            'phone_prefix_display_prefix_only' => '+34612345686',
            'phone_prefix_display_flag_and_prefix' => '+34612345687',
            'phone_prefix_display_iso_and_prefix' => '+33612345688',
            'phone_prefix_display_flag_only' => '+34612345693',
            'phone_flag_display_emoji' => '+34612345689',
            'phone_flag_display_css_icon' => '+34612345690',
            'phone_flag_display_ux_icon' => '+34612345691',
            'phone_flag_display_none' => '+34612345692',
        ];
    }
}
