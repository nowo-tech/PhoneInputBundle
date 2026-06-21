<?php

declare(strict_types=1);

namespace App\Form;

use App\DemoFramework;
use Nowo\PhoneInputBundle\Form\FlagDisplay;
use Nowo\PhoneInputBundle\Form\PrefixDisplay;
use Nowo\PhoneInputBundle\Form\Type\PhoneType;
use Nowo\PhoneInputBundle\Form\ValueFormat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Demo form exposing every PhoneType value_format × country_prefix_selector combination.
 */
final class DemoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $combinations = [
            'phone_concatenated_with_prefix' => [
                'label'                   => 'Concatenated (E.164 string) — with prefix selector',
                'value_format'            => ValueFormat::CONCATENATED,
                'country_prefix_selector' => true,
                'prefix_display'          => PrefixDisplay::FLAG_AND_PREFIX,
                'flag_display'            => FlagDisplay::CSS_ICON,
                'prefix_search'           => false,
                'help'                    => 'FlagDisplay::CSS_ICON · PrefixDisplay::FLAG_AND_PREFIX',
            ],
            'phone_concatenated_without_prefix' => [
                'label'                   => 'Concatenated (E.164 string) — without prefix selector',
                'value_format'            => ValueFormat::CONCATENATED,
                'country_prefix_selector' => false,
                'help'                    => 'Model: string; national input only (default country applied)',
            ],
            'phone_separated_with_prefix' => [
                'label'                   => 'Separated array — with prefix selector',
                'value_format'            => ValueFormat::SEPARATED,
                'country_prefix_selector' => true,
                'prefix_display'          => PrefixDisplay::FLAG_AND_PREFIX,
                'flag_display'            => FlagDisplay::CSS_ICON,
                'prefix_search'           => false,
                'help'                    => 'FlagDisplay::CSS_ICON · PrefixDisplay::FLAG_AND_PREFIX',
            ],
            'phone_separated_without_prefix' => [
                'label'                   => 'Separated array — without prefix selector',
                'value_format'            => ValueFormat::SEPARATED,
                'country_prefix_selector' => false,
                'help'                    => 'Model: {iso, prefix, national_number}',
            ],
            'phone_object_with_prefix' => [
                'label'                   => 'PhoneNumber object — with prefix selector',
                'value_format'            => ValueFormat::OBJECT,
                'country_prefix_selector' => true,
                'prefix_display'          => PrefixDisplay::FLAG_AND_PREFIX,
                'flag_display'            => FlagDisplay::CSS_ICON,
                'prefix_search'           => false,
                'help'                    => 'FlagDisplay::CSS_ICON · PrefixDisplay::FLAG_AND_PREFIX',
            ],
            'phone_object_without_prefix' => [
                'label'                   => 'PhoneNumber object — without prefix selector',
                'value_format'            => ValueFormat::OBJECT,
                'country_prefix_selector' => false,
                'help'                    => 'Model: PhoneNumber value object',
            ],
            'phone_with_prefix_search' => [
                'label'                   => 'Prefix selector with country search',
                'value_format'            => ValueFormat::CONCATENATED,
                'country_prefix_selector' => true,
                'prefix_display'          => PrefixDisplay::FLAG_AND_PREFIX,
                'flag_display'            => FlagDisplay::CSS_ICON,
                'prefix_search'           => true,
                'help'                    => 'FlagDisplay::CSS_ICON · prefix_search: true',
            ],
            'phone_selector_only' => [
                'label'                   => 'Prefix selector only (no search)',
                'value_format'            => ValueFormat::CONCATENATED,
                'country_prefix_selector' => true,
                'prefix_display'          => PrefixDisplay::FLAG_AND_PREFIX,
                'flag_display'            => FlagDisplay::CSS_ICON,
                'prefix_search'           => false,
                'help'                    => 'FlagDisplay::CSS_ICON · prefix_search: false',
            ],
            'phone_allowed_iberian' => [
                'label'                   => 'Allowed countries — Iberian only (ES, PT)',
                'value_format'            => ValueFormat::CONCATENATED,
                'country_prefix_selector' => true,
                'prefix_display'          => PrefixDisplay::FULL,
                'flag_display'            => FlagDisplay::CSS_ICON,
                'prefix_search'           => false,
                'allowed_countries'       => ['ES', 'PT'],
                'help'                    => 'FlagDisplay::CSS_ICON · allowed_countries: [ES, PT]',
            ],
            'phone_excluded_fr' => [
                'label'                   => 'Excluded countries — without France',
                'value_format'            => ValueFormat::CONCATENATED,
                'country_prefix_selector' => true,
                'prefix_display'          => PrefixDisplay::FULL,
                'flag_display'            => FlagDisplay::CSS_ICON,
                'prefix_search'           => false,
                'excluded_countries'      => ['FR'],
                'help'                    => 'FlagDisplay::CSS_ICON · excluded_countries: [FR]',
            ],
            'phone_without_flag' => [
                'label'                   => 'Prefix only — show_flag disabled',
                'value_format'            => ValueFormat::CONCATENATED,
                'country_prefix_selector' => true,
                'prefix_display'          => PrefixDisplay::PREFIX_ONLY,
                'flag_display'            => FlagDisplay::CSS_ICON,
                'prefix_search'           => false,
                'show_flag'               => false,
                'help'                    => 'show_flag: false (overrides FlagDisplay)',
            ],
            'phone_prefix_display_full' => [
                'label'                   => 'Prefix display — full (flag + prefix + country name)',
                'value_format'            => ValueFormat::CONCATENATED,
                'country_prefix_selector' => true,
                'prefix_display'          => PrefixDisplay::FULL,
                'flag_display'            => FlagDisplay::CSS_ICON,
                'prefix_search'           => false,
                'help'                    => 'PrefixDisplay::FULL · FlagDisplay::CSS_ICON',
            ],
            'phone_prefix_display_prefix_only' => [
                'label'                   => 'Prefix display — prefix only',
                'value_format'            => ValueFormat::CONCATENATED,
                'country_prefix_selector' => true,
                'prefix_display'          => PrefixDisplay::PREFIX_ONLY,
                'flag_display'            => FlagDisplay::CSS_ICON,
                'prefix_search'           => false,
                'help'                    => 'PrefixDisplay::PREFIX_ONLY · FlagDisplay::CSS_ICON',
            ],
            'phone_prefix_display_flag_and_prefix' => [
                'label'                   => 'Prefix display — flag and prefix (default)',
                'value_format'            => ValueFormat::CONCATENATED,
                'country_prefix_selector' => true,
                'prefix_display'          => PrefixDisplay::FLAG_AND_PREFIX,
                'flag_display'            => FlagDisplay::CSS_ICON,
                'prefix_search'           => false,
                'help'                    => 'PrefixDisplay::FLAG_AND_PREFIX · FlagDisplay::CSS_ICON',
            ],
            'phone_prefix_display_iso_and_prefix' => [
                'label'                   => 'Prefix display — ISO code and prefix',
                'value_format'            => ValueFormat::CONCATENATED,
                'country_prefix_selector' => true,
                'prefix_display'          => PrefixDisplay::ISO_AND_PREFIX,
                'flag_display'            => FlagDisplay::CSS_ICON,
                'prefix_search'           => false,
                'help'                    => 'PrefixDisplay::ISO_AND_PREFIX · FlagDisplay::CSS_ICON',
            ],
            'phone_prefix_display_flag_only' => [
                'label'                   => 'Prefix display — flag only (no prefix text)',
                'value_format'            => ValueFormat::CONCATENATED,
                'country_prefix_selector' => true,
                'prefix_display'          => PrefixDisplay::FLAG_ONLY,
                'flag_display'            => FlagDisplay::CSS_ICON,
                'prefix_search'           => false,
                'help'                    => 'PrefixDisplay::FLAG_ONLY · FlagDisplay::CSS_ICON',
            ],
            'phone_flag_display_emoji' => [
                'label'                   => 'Flag display — emoji',
                'value_format'            => ValueFormat::CONCATENATED,
                'country_prefix_selector' => true,
                'prefix_display'          => PrefixDisplay::FLAG_AND_PREFIX,
                'flag_display'            => FlagDisplay::EMOJI,
                'prefix_search'           => false,
                'help'                    => 'FlagDisplay::EMOJI',
            ],
            'phone_flag_display_css_icon' => [
                'label'                   => 'Flag display — CSS icons (default)',
                'value_format'            => ValueFormat::CONCATENATED,
                'country_prefix_selector' => true,
                'prefix_display'          => PrefixDisplay::FLAG_AND_PREFIX,
                'flag_display'            => FlagDisplay::CSS_ICON,
                'prefix_search'           => false,
                'help'                    => 'FlagDisplay::CSS_ICON',
            ],
            'phone_flag_display_ux_icon' => [
                'label'                   => 'Flag display — UX icons (fallback to CSS without symfony/ux-icons)',
                'value_format'            => ValueFormat::CONCATENATED,
                'country_prefix_selector' => true,
                'prefix_display'          => PrefixDisplay::FLAG_AND_PREFIX,
                'flag_display'            => FlagDisplay::UX_ICON,
                'prefix_search'           => false,
                'help'                    => 'FlagDisplay::UX_ICON',
            ],
            'phone_flag_display_none' => [
                'label'                   => 'Flag display — none',
                'value_format'            => ValueFormat::CONCATENATED,
                'country_prefix_selector' => true,
                'prefix_display'          => PrefixDisplay::FLAG_AND_PREFIX,
                'flag_display'            => FlagDisplay::NONE,
                'prefix_search'           => false,
                'help'                    => 'FlagDisplay::NONE · show_flag: true',
            ],
        ];

        $frameworkClasses = DemoFramework::phoneTypeClasses($options['demo_framework']);

        foreach ($combinations as $field => $config) {
            $fieldOptions = array_merge($frameworkClasses, [
                'label'                   => $config['label'],
                'value_format'            => $config['value_format'],
                'country_prefix_selector' => $config['country_prefix_selector'],
                'required'                => false,
                'help'                    => $config['help'],
            ]);

            foreach ([
                'allowed_countries',
                'excluded_countries',
                'show_flag',
                'prefix_display',
                'flag_display',
                'prefix_search',
                'national_number_attr',
            ] as $optionName) {
                if (\array_key_exists($optionName, $config)) {
                    $fieldOptions[$optionName] = $config[$optionName];
                }
            }

            $builder->add($field, PhoneType::class, $fieldOptions);
        }

        $builder->add('submit', SubmitType::class, [
            'label' => 'Submit all formats',
            'attr'  => ['class' => 'btn btn-primary btn-lg'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => false,
            'demo_framework' => DemoFramework::BOOTSTRAP5,
        ]);

        $resolver->setAllowedValues('demo_framework', DemoFramework::values());
    }
}
