<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\DependencyInjection;

use libphonenumber\PhoneNumberUtil;
use Nowo\PhoneInputBundle\Country\CountryProvider;
use Nowo\PhoneInputBundle\DependencyInjection\NowoPhoneInputExtension;
use Nowo\PhoneInputBundle\Form\Type\PhoneType;
use Nowo\PhoneInputBundle\Phone\LibPhoneNumberChecker;
use Nowo\PhoneInputBundle\Phone\PhoneValidator;
use Nowo\PhoneInputBundle\Twig\CountryFlagRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

final class NowoPhoneInputExtensionTest extends TestCase
{
    private NowoPhoneInputExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new NowoPhoneInputExtension();
    }

    public function testGetAlias(): void
    {
        $this->assertSame('nowo_phone_input', $this->extension->getAlias());
    }

    public function testLoadRegistersServicesAndParameters(): void
    {
        $container = new ContainerBuilder();
        $this->extension->load([], $container);

        $this->assertTrue($container->hasDefinition(PhoneType::class));
        $this->assertTrue($container->hasDefinition(CountryProvider::class));
        $this->assertTrue($container->hasParameter('nowo_phone_input.defaults'));
        $this->assertTrue($container->hasParameter('nowo_phone_input.countries_file'));
        $this->assertTrue($container->hasParameter('nowo_phone_input.patterns_file'));
        $this->assertTrue($container->hasParameter('nowo_phone_input.use_libphonenumber'));
        $this->assertTrue($container->hasDefinition(PhoneValidator::class));
    }

    public function testLoadWithCustomConfig(): void
    {
        $container = new ContainerBuilder();
        $this->extension->load([
            [
                'default_country' => 'FR',
                'preferred_countries' => ['FR', 'ES'],
                'allowed_countries' => ['FR'],
                'excluded_countries' => ['US'],
                'phone_validation' => 'PREFIX',
                'use_libphonenumber' => false,
            ],
        ], $container);

        $defaults = $container->getParameter('nowo_phone_input.defaults');
        $this->assertIsArray($defaults);
        $this->assertSame('FR', $defaults['default_country']);
        $this->assertSame(['FR', 'ES'], $defaults['preferred_countries']);
        $this->assertFalse($container->getParameter('nowo_phone_input.use_libphonenumber'));

        $countryProvider = $container->getDefinition(CountryProvider::class);
        $this->assertSame('FR', $countryProvider->getArgument('$defaultCountryIso'));
    }

    public function testLoadWiresUxIconRendererAsOptionalReference(): void
    {
        // The UX Icons service lives in another bundle and is invisible inside load(): always wire an optional reference.
        $container = new ContainerBuilder();
        $this->extension->load([], $container);

        $renderer = $container->getDefinition(CountryFlagRenderer::class);
        $this->assertEquals(
            new Reference('Symfony\UX\Icons\IconRendererInterface', ContainerInterface::NULL_ON_INVALID_REFERENCE),
            $renderer->getArgument('$iconRenderer'),
        );
    }

    public function testPrependRegistersFormThemeByDefault(): void
    {
        $container = $this->containerWithTwig();

        $this->extension->prepend($container);

        $this->assertSame([['form_themes' => [NowoPhoneInputExtension::FORM_THEME]]], $container->getExtensionConfig('twig'));
    }

    public function testPrependSkipsFormThemeWhenDisabled(): void
    {
        $container = $this->containerWithTwig();
        $container->loadFromExtension('nowo_phone_input', ['use_phone_form_theme' => false]);

        $this->extension->prepend($container);

        $this->assertSame([], $container->getExtensionConfig('twig'));
    }

    public function testPrependSkipsFormThemeAlreadyRegisteredByApp(): void
    {
        $container = $this->containerWithTwig();
        $container->loadFromExtension('twig', ['form_themes' => [NowoPhoneInputExtension::FORM_THEME]]);

        $this->extension->prepend($container);

        $this->assertSame([['form_themes' => [NowoPhoneInputExtension::FORM_THEME]]], $container->getExtensionConfig('twig'));
    }

    public function testPrependRegistersAssetPackage(): void
    {
        $container = new ContainerBuilder();
        $container->registerExtension(new class extends Extension {
            public function getAlias(): string
            {
                return 'framework';
            }

            public function load(array $configs, ContainerBuilder $container): void
            {
            }
        });

        $this->extension->prepend($container);

        $frameworkConfig = $container->getExtensionConfig('framework');
        $this->assertSame(
            '/bundles/nowophoneinput',
            $frameworkConfig[0]['assets']['packages']['nowo_phone_input']['base_path'] ?? null,
        );
    }

    public function testLoadWiresLibPhoneNumberCheckerWhenAvailable(): void
    {
        if (!class_exists(PhoneNumberUtil::class)) {
            $this->markTestSkipped('giggsey/libphonenumber-for-php not installed');
        }

        $container = new ContainerBuilder();
        $this->extension->load([], $container);

        $this->assertTrue($container->hasDefinition(LibPhoneNumberChecker::class));
        $validator = $container->getDefinition(PhoneValidator::class);
        $this->assertInstanceOf(Reference::class, $validator->getArgument('$nationalPhoneNumberChecker'));
    }

    public function testPrependSkipsWhenFrameworkExtensionMissing(): void
    {
        $container = new ContainerBuilder();

        $this->extension->prepend($container);

        $this->assertFalse($container->hasExtension('framework'));
        $this->assertSame([], $container->getExtensionConfig('framework'));
    }

    public function testPrependSkipsWhenAssetPackageUnavailable(): void
    {
        $extension = new class extends NowoPhoneInputExtension {
            protected function supportsAssetPackage(): bool
            {
                return false;
            }
        };

        $container = new ContainerBuilder();
        $container->registerExtension(new class extends Extension {
            public function getAlias(): string
            {
                return 'framework';
            }

            public function load(array $configs, ContainerBuilder $container): void
            {
            }
        });

        $extension->prepend($container);

        $this->assertSame([], $container->getExtensionConfig('framework'));
    }

    public function testLoadSetsNullNationalCheckerWhenLibPhoneNumberUnavailable(): void
    {
        $extension = new class extends NowoPhoneInputExtension {
            protected function supportsLibPhoneNumber(): bool
            {
                return false;
            }
        };

        $container = new ContainerBuilder();
        $extension->load([], $container);

        $validator = $container->getDefinition(PhoneValidator::class);
        $this->assertNull($validator->getArgument('$nationalPhoneNumberChecker'));
    }

    private function containerWithTwig(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->registerExtension($this->extension);
        $container->registerExtension(new class extends Extension {
            public function getAlias(): string
            {
                return 'twig';
            }

            public function load(array $configs, ContainerBuilder $container): void
            {
            }
        });

        return $container;
    }
}
