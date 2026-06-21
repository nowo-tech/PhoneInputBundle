<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Form\Type;

use Nowo\PhoneInputBundle\Country\Country;
use Nowo\PhoneInputBundle\Country\CountryProvider;
use Nowo\PhoneInputBundle\Form\DataTransformer\PhoneNumberTransformer;
use Nowo\PhoneInputBundle\Form\FlagDisplay;
use Nowo\PhoneInputBundle\Form\PrefixDisplay;
use Nowo\PhoneInputBundle\Form\ValueFormat;
use Nowo\PhoneInputBundle\IconSupport\IconSupportChecker;
use Nowo\PhoneInputBundle\Phone\E164Parser;
use Nowo\PhoneInputBundle\Validation\PhoneValidationMode;
use Nowo\PhoneInputBundle\Validator\Constraints\ValidPhoneNumber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Phone form type with optional country prefix selector and flexible value formats.
 *
 * @extends AbstractType<mixed>
 */
final class PhoneType extends AbstractType
{
    /**
     * @param array<string, mixed> $defaults
     */
    public function __construct(
        private readonly CountryProvider $countryProvider,
        private readonly E164Parser $e164Parser,
        private readonly IconSupportChecker $iconSupportChecker,
        private array $defaults = [],
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['country_prefix_selector']) {
            $builder->add('country_iso', ChoiceType::class, [
                'choices' => $this->buildCountryChoices($options),
                'choice_label' => static fn (string $iso): string => $iso,
                'label' => false,
                'required' => false,
            ]);
        }

        $builder->add('national_number', TelType::class, [
            'label' => false,
            'required' => $options['required'],
            'trim' => $options['trim'],
            'attr' => $options['national_number_attr'],
        ]);

        $builder->addModelTransformer(new PhoneNumberTransformer(
            valueFormat: $options['value_format'],
            countryPrefixSelector: $options['country_prefix_selector'],
            countryProvider: $this->countryProvider,
            e164Parser: $this->e164Parser,
            defaultCountryIso: $options['default_country'],
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['country_prefix_selector'] = $options['country_prefix_selector'];
        $view->vars['prefix_display'] = $options['prefix_display']->value;
        $view->vars['show_flag'] = $options['show_flag'];
        $view->vars['prefix_search'] = $options['prefix_search'];
        $view->vars['flag_display'] = $options['flag_display']->value;
        $view->vars['container_classes'] = $options['container_classes'];
        $view->vars['prefix_selector_classes'] = $options['prefix_selector_classes'];
        $view->vars['national_number_classes'] = $options['national_number_classes'];
        $view->vars['countries'] = array_map(
            static fn (Country $country): array => $country->toArray(),
            $this->resolveCountries($options),
        );
        $view->vars['default_country'] = $options['default_country'];
        $view->vars['value_format'] = $options['value_format']->value;
        $view->vars['icons_available'] = $this->iconSupportChecker->isIconRenderingSupported();
        $view->vars['attr']['data-nowo-phone-input-flag-display'] = $options['flag_display']->value;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'compound' => true,
            'country_prefix_selector' => $this->defaults['country_prefix_selector'] ?? true,
            'default_country' => $this->defaults['default_country'] ?? 'ES',
            'value_format' => $this->defaults['value_format'] ?? ValueFormat::CONCATENATED->value,
            'prefix_display' => $this->defaults['prefix_display'] ?? PrefixDisplay::FLAG_AND_PREFIX->value,
            'show_flag' => $this->defaults['show_flag'] ?? true,
            'prefix_search' => $this->defaults['prefix_search'] ?? true,
            'flag_display' => $this->defaults['flag_display'] ?? FlagDisplay::CSS_ICON->value,
            'container_classes' => $this->defaults['container_classes'] ?? ['input-group', 'nowo-phone-input'],
            'prefix_selector_classes' => $this->defaults['prefix_selector_classes'] ?? ['form-select', 'nowo-phone-input__prefix'],
            'national_number_classes' => $this->defaults['national_number_classes'] ?? ['form-control', 'nowo-phone-input__number'],
            'national_number_attr' => [],
            'allowed_countries' => null,
            'excluded_countries' => null,
            'preferred_countries' => null,
            'trim' => $this->defaults['trim'] ?? true,
            'invalid_message' => $this->defaults['invalid_message'] ?? 'The phone number is invalid.',
            'phone_validation' => $this->defaults['phone_validation'] ?? PhoneValidationMode::COUNTRY->value,
            'use_phone_form_theme' => $this->defaults['use_phone_form_theme'] ?? true,
            'constraints' => [],
            'error_bubbling' => false,
            'empty_data' => '',
        ]);

        $resolver->setAllowedTypes('country_prefix_selector', 'bool');
        $resolver->setAllowedTypes('default_country', 'string');
        $resolver->setAllowedTypes('value_format', ['string', ValueFormat::class]);
        $resolver->setAllowedTypes('prefix_display', ['string', PrefixDisplay::class]);
        $resolver->setAllowedTypes('show_flag', 'bool');
        $resolver->setAllowedTypes('prefix_search', 'bool');
        $resolver->setAllowedTypes('flag_display', ['string', FlagDisplay::class]);
        $resolver->setAllowedTypes('container_classes', 'array');
        $resolver->setAllowedTypes('prefix_selector_classes', 'array');
        $resolver->setAllowedTypes('national_number_classes', 'array');
        $resolver->setAllowedTypes('national_number_attr', 'array');
        $resolver->setAllowedTypes('allowed_countries', ['null', 'array']);
        $resolver->setAllowedTypes('excluded_countries', ['null', 'array']);
        $resolver->setAllowedTypes('preferred_countries', ['null', 'array']);
        $resolver->setAllowedTypes('trim', 'bool');
        $resolver->setAllowedTypes('invalid_message', 'string');
        $resolver->setAllowedTypes('phone_validation', ['bool', 'string', PhoneValidationMode::class]);
        $resolver->setAllowedTypes('constraints', 'array');
        $resolver->setAllowedTypes('use_phone_form_theme', 'bool');

        $resolver->setNormalizer('value_format', static function (Options $options, mixed $value): ValueFormat {
            if ($value instanceof ValueFormat) {
                return $value;
            }

            return ValueFormat::from(strtoupper((string) $value));
        });

        $resolver->setNormalizer('prefix_display', static function (Options $options, mixed $value): PrefixDisplay {
            if ($value instanceof PrefixDisplay) {
                $display = $value;
            } else {
                $display = PrefixDisplay::from(strtoupper((string) $value));
            }

            if (PrefixDisplay::FLAG_ONLY === $display && !$options['show_flag']) {
                return PrefixDisplay::PREFIX_ONLY;
            }

            return $display;
        });

        $resolver->setNormalizer('flag_display', static function (Options $options, mixed $value): FlagDisplay {
            if (!$options['show_flag']) {
                return FlagDisplay::NONE;
            }

            if ($value instanceof FlagDisplay) {
                return $value;
            }

            return FlagDisplay::from(strtoupper((string) $value));
        });

        $resolver->setNormalizer('phone_validation', static function (Options $options, mixed $value): PhoneValidationMode {
            if ($value instanceof PhoneValidationMode) {
                return $value;
            }

            if (\is_bool($value)) {
                return $value ? PhoneValidationMode::COUNTRY : PhoneValidationMode::NONE;
            }

            return PhoneValidationMode::from(strtoupper((string) $value));
        });

        $resolver->setNormalizer('constraints', static function (Options $options, ?array $value): array {
            $constraints = $value ?? [];

            if (PhoneValidationMode::NONE === $options['phone_validation']) {
                return $constraints;
            }

            $hasPhoneConstraint = false;
            foreach ($constraints as $constraint) {
                if ($constraint instanceof ValidPhoneNumber) {
                    $hasPhoneConstraint = true;
                    break;
                }
            }

            if ($hasPhoneConstraint) {
                return $constraints;
            }

            array_unshift($constraints, new ValidPhoneNumber(
                mode: $options['phone_validation'],
                message: $options['invalid_message'],
                defaultCountry: $options['default_country'],
            ));

            return $constraints;
        });

        $resolver->setNormalizer('empty_data', static function (Options $options): mixed {
            $format = $options['value_format'];
            if (!$format instanceof ValueFormat) {
                $format = ValueFormat::from(strtoupper((string) $format));
            }

            return match ($format) {
                ValueFormat::CONCATENATED => '',
                ValueFormat::SEPARATED => [
                    'iso' => '',
                    'prefix' => '',
                    'national_number' => '',
                ],
                ValueFormat::OBJECT => null,
            };
        });
    }

    public function getBlockPrefix(): string
    {
        return 'nowo_phone_input';
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return list<Country>
     */
    private function resolveCountries(array $options): array
    {
        return $this->countryProvider->getCountriesForSelector(
            $options['allowed_countries'],
            $options['excluded_countries'],
            $options['preferred_countries'],
        );
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, string>
     */
    private function buildCountryChoices(array $options): array
    {
        $choices = [];
        foreach ($this->resolveCountries($options) as $country) {
            $choices[$country->iso] = $country->iso;
        }

        return $choices;
    }
}
