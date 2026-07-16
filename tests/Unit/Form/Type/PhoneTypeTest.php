<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\Form\Type;

use Nowo\PhoneInputBundle\Form\FlagDisplay;
use Nowo\PhoneInputBundle\Form\PrefixDisplay;
use Nowo\PhoneInputBundle\Form\Type\PhoneType;
use Nowo\PhoneInputBundle\Form\ValueFormat;
use Nowo\PhoneInputBundle\IconSupport\IconSupportChecker;
use Nowo\PhoneInputBundle\Tests\TestFixtures;
use Nowo\PhoneInputBundle\Validation\PhoneValidationMode;
use Nowo\PhoneInputBundle\Validator\Constraints\ValidPhoneNumber;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PhoneTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $provider = TestFixtures::countryProvider();

        return [
            new PreloadedExtension([
                new PhoneType(
                    $provider,
                    TestFixtures::e164Parser($provider),
                    new IconSupportChecker(uxIconsAvailable: true, httpClientAvailable: true),
                ),
            ], []),
        ];
    }

    public function testSubmitConcatenatedFormat(): void
    {
        $form = $this->factory->create(PhoneType::class, null, [
            'value_format' => ValueFormat::CONCATENATED,
        ]);

        $form->submit([
            'country_iso' => 'ES',
            'national_number' => '612345678',
        ]);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame('+34612345678', $form->getData());
    }

    public function testSubmitSeparatedFormat(): void
    {
        $form = $this->factory->create(PhoneType::class, null, [
            'value_format' => ValueFormat::SEPARATED,
        ]);

        $form->submit([
            'country_iso' => 'FR',
            'national_number' => '612345678',
        ]);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame([
            'iso' => 'FR',
            'prefix' => '+33',
            'national_number' => '612345678',
        ], $form->getData());
    }

    public function testSubmitObjectFormat(): void
    {
        $form = $this->factory->create(PhoneType::class, null, [
            'value_format' => ValueFormat::OBJECT,
        ]);

        $form->submit([
            'country_iso' => 'GB',
            'national_number' => '7911123456',
        ]);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame('+447911123456', $form->getData()?->getE164());
    }

    public function testWithoutCountryPrefixSelector(): void
    {
        $form = $this->factory->create(PhoneType::class, null, [
            'country_prefix_selector' => false,
            'value_format' => ValueFormat::CONCATENATED,
        ]);

        $this->assertFalse($form->has('country_iso'));

        $form->submit(['national_number' => '612345678']);
        $this->assertSame('+34612345678', $form->getData());
    }

    public function testDefaultOptionsFromBundleDefaults(): void
    {
        $provider = TestFixtures::countryProvider();
        $phoneType = new PhoneType(
            $provider,
            TestFixtures::e164Parser($provider),
            new IconSupportChecker(),
            [
                'country_prefix_selector' => false,
                'value_format' => 'separated',
                'default_country' => 'FR',
            ],
        );

        $resolver = new OptionsResolver();
        $phoneType->configureOptions($resolver);
        $resolved = $resolver->resolve([]);

        $this->assertFalse($resolved['country_prefix_selector']);
        $this->assertSame(ValueFormat::SEPARATED, $resolved['value_format']);
        $this->assertSame('FR', $resolved['default_country']);
    }

    public function testBuildViewExposesCountries(): void
    {
        $provider = TestFixtures::countryProvider();
        $phoneType = new PhoneType(
            $provider,
            TestFixtures::e164Parser($provider),
            new IconSupportChecker(uxIconsAvailable: true, httpClientAvailable: true),
        );

        $form = $this->createMock(FormInterface::class);
        $view = new FormView();

        $phoneType->buildView($view, $form, [
            'country_prefix_selector' => true,
            'prefix_display' => PrefixDisplay::FLAG_AND_PREFIX,
            'show_flag' => true,
            'prefix_search' => true,
            'flag_display' => FlagDisplay::EMOJI,
            'container_classes' => ['input-group'],
            'prefix_selector_classes' => ['form-select'],
            'national_number_classes' => ['form-control'],
            'default_country' => 'ES',
            'value_format' => ValueFormat::CONCATENATED,
            'allowed_countries' => null,
            'excluded_countries' => null,
            'preferred_countries' => null,
        ]);

        $this->assertTrue($view->vars['country_prefix_selector']);
        $this->assertNotEmpty($view->vars['countries']);
        $this->assertSame('CONCATENATED', $view->vars['value_format']);
        $this->assertTrue($view->vars['icons_available']);
        $this->assertSame('EMOJI', $view->vars['flag_display']);
    }

    public function testAllowedCountriesFieldOptionLimitsSelector(): void
    {
        $form = $this->factory->create(PhoneType::class, null, [
            'allowed_countries' => ['ES', 'GB'],
        ]);

        $countryField = $form->get('country_iso');
        $choices = $countryField->getConfig()->getOption('choices');

        $this->assertSame(['ES', 'GB'], array_values($choices));
    }

    public function testExcludedCountriesFieldOptionRemovesCountries(): void
    {
        $form = $this->factory->create(PhoneType::class, null, [
            'excluded_countries' => ['FR'],
        ]);

        $countryField = $form->get('country_iso');
        $choices = $countryField->getConfig()->getOption('choices');

        $this->assertNotContains('FR', $choices);
        $this->assertContains('ES', $choices);
    }

    public function testShowFlagFalseDisablesFlagDisplay(): void
    {
        $resolver = new OptionsResolver();
        $provider = TestFixtures::countryProvider();
        $phoneType = new PhoneType(
            $provider,
            TestFixtures::e164Parser($provider),
            new IconSupportChecker(),
        );
        $phoneType->configureOptions($resolver);

        $resolved = $resolver->resolve([
            'show_flag' => false,
            'flag_display' => FlagDisplay::CSS_ICON,
        ]);

        $this->assertFalse($resolved['show_flag']);
        $this->assertSame(FlagDisplay::NONE, $resolved['flag_display']);
    }

    public function testPrefixSearchCanBeDisabled(): void
    {
        $resolver = new OptionsResolver();
        $provider = TestFixtures::countryProvider();
        $phoneType = new PhoneType(
            $provider,
            TestFixtures::e164Parser($provider),
            new IconSupportChecker(),
        );
        $phoneType->configureOptions($resolver);

        $resolved = $resolver->resolve(['prefix_search' => false]);

        $this->assertFalse($resolved['prefix_search']);
    }

    public function testLegacyLowercaseValueFormatStringIsNormalized(): void
    {
        $resolver = new OptionsResolver();
        $provider = TestFixtures::countryProvider();
        $phoneType = new PhoneType(
            $provider,
            TestFixtures::e164Parser($provider),
            new IconSupportChecker(),
        );
        $phoneType->configureOptions($resolver);

        $resolved = $resolver->resolve(['value_format' => 'separated']);

        $this->assertSame(ValueFormat::SEPARATED, $resolved['value_format']);
    }

    public function testLegacyLowercasePrefixDisplayStringIsNormalized(): void
    {
        $resolver = new OptionsResolver();
        $provider = TestFixtures::countryProvider();
        $phoneType = new PhoneType(
            $provider,
            TestFixtures::e164Parser($provider),
            new IconSupportChecker(),
        );
        $phoneType->configureOptions($resolver);

        $resolved = $resolver->resolve(['prefix_display' => 'prefix_only']);

        $this->assertSame(PrefixDisplay::PREFIX_ONLY, $resolved['prefix_display']);
    }

    public function testFlagOnlyDisplayFallsBackWhenShowFlagIsFalse(): void
    {
        $resolver = new OptionsResolver();
        $provider = TestFixtures::countryProvider();
        $phoneType = new PhoneType(
            $provider,
            TestFixtures::e164Parser($provider),
            new IconSupportChecker(),
        );
        $phoneType->configureOptions($resolver);

        $resolved = $resolver->resolve([
            'prefix_display' => PrefixDisplay::FLAG_ONLY,
            'show_flag' => false,
        ]);

        $this->assertSame(PrefixDisplay::PREFIX_ONLY, $resolved['prefix_display']);
    }

    public function testLegacyLowercaseFlagDisplayStringIsNormalized(): void
    {
        $resolver = new OptionsResolver();
        $provider = TestFixtures::countryProvider();
        $phoneType = new PhoneType(
            $provider,
            TestFixtures::e164Parser($provider),
            new IconSupportChecker(),
        );
        $phoneType->configureOptions($resolver);

        $resolved = $resolver->resolve(['flag_display' => 'css_icon']);

        $this->assertSame(FlagDisplay::CSS_ICON, $resolved['flag_display']);
    }

    public function testPhoneValidationAddsConstraintByDefault(): void
    {
        $resolver = new OptionsResolver();
        $provider = TestFixtures::countryProvider();
        $phoneType = new PhoneType(
            $provider,
            TestFixtures::e164Parser($provider),
            new IconSupportChecker(),
        );
        $phoneType->configureOptions($resolver);

        $resolved = $resolver->resolve([]);

        $this->assertSame(PhoneValidationMode::COUNTRY, $resolved['phone_validation']);
        $this->assertInstanceOf(ValidPhoneNumber::class, $resolved['constraints'][0]);
    }

    public function testPhoneValidationCanBeDisabled(): void
    {
        $resolver = new OptionsResolver();
        $provider = TestFixtures::countryProvider();
        $phoneType = new PhoneType(
            $provider,
            TestFixtures::e164Parser($provider),
            new IconSupportChecker(),
        );
        $phoneType->configureOptions($resolver);

        $resolved = $resolver->resolve(['phone_validation' => false]);

        $this->assertSame(PhoneValidationMode::NONE, $resolved['phone_validation']);
        $this->assertSame([], $resolved['constraints']);
    }

    public function testExistingValidPhoneNumberConstraintIsNotDuplicated(): void
    {
        $resolver = new OptionsResolver();
        $provider = TestFixtures::countryProvider();
        $phoneType = new PhoneType(
            $provider,
            TestFixtures::e164Parser($provider),
            new IconSupportChecker(),
        );
        $phoneType->configureOptions($resolver);

        $existing = new ValidPhoneNumber(mode: PhoneValidationMode::PREFIX);
        $resolved = $resolver->resolve(['constraints' => [$existing]]);

        $this->assertCount(1, $resolved['constraints']);
        $this->assertSame($existing, $resolved['constraints'][0]);
    }

    public function testGetBlockPrefix(): void
    {
        $provider = TestFixtures::countryProvider();
        $phoneType = new PhoneType(
            $provider,
            TestFixtures::e164Parser($provider),
            new IconSupportChecker(),
        );

        $this->assertSame('nowo_phone_input', $phoneType->getBlockPrefix());
    }

    public function testEmptyDataDependsOnValueFormat(): void
    {
        $resolver = new OptionsResolver();
        $provider = TestFixtures::countryProvider();
        $phoneType = new PhoneType(
            $provider,
            TestFixtures::e164Parser($provider),
            new IconSupportChecker(),
        );
        $phoneType->configureOptions($resolver);

        $this->assertSame('', $resolver->resolve(['value_format' => ValueFormat::CONCATENATED])['empty_data']);
        $this->assertNull($resolver->resolve(['value_format' => ValueFormat::OBJECT])['empty_data']);
        $this->assertSame([
            'iso' => '',
            'prefix' => '',
            'national_number' => '',
        ], $resolver->resolve(['value_format' => 'separated'])['empty_data']);
    }

    public function testFlagDisplayNormalizerAcceptsEnumInstance(): void
    {
        $resolver = new OptionsResolver();
        $provider = TestFixtures::countryProvider();
        $phoneType = new PhoneType(
            $provider,
            TestFixtures::e164Parser($provider),
            new IconSupportChecker(),
        );
        $phoneType->configureOptions($resolver);

        $resolved = $resolver->resolve([
            'show_flag' => true,
            'flag_display' => FlagDisplay::EMOJI,
        ]);

        $this->assertSame(FlagDisplay::EMOJI, $resolved['flag_display']);
    }

    public function testPhoneValidationNormalizerAcceptsEnumInstance(): void
    {
        $resolver = new OptionsResolver();
        $provider = TestFixtures::countryProvider();
        $phoneType = new PhoneType(
            $provider,
            TestFixtures::e164Parser($provider),
            new IconSupportChecker(),
        );
        $phoneType->configureOptions($resolver);

        $resolved = $resolver->resolve([
            'phone_validation' => PhoneValidationMode::PREFIX,
        ]);

        $this->assertSame(PhoneValidationMode::PREFIX, $resolved['phone_validation']);
    }

    public function testEmptyDataNormalizerAcceptsStringValueFormat(): void
    {
        $resolver = new OptionsResolver();
        $provider = TestFixtures::countryProvider();
        $phoneType = new PhoneType(
            $provider,
            TestFixtures::e164Parser($provider),
            new IconSupportChecker(),
        );
        $phoneType->configureOptions($resolver);

        $resolved = $resolver->resolve(['value_format' => 'object']);

        $this->assertNull($resolved['empty_data']);
    }
}
