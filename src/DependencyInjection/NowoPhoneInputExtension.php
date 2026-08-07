<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\DependencyInjection;

use libphonenumber\PhoneNumberUtil;
use Nowo\PhoneInputBundle\Country\CountryProvider;
use Nowo\PhoneInputBundle\Phone\LibPhoneNumberChecker;
use Nowo\PhoneInputBundle\Phone\NationalPhoneNumberChecker;
use Nowo\PhoneInputBundle\Phone\PhoneValidator;
use Nowo\PhoneInputBundle\Twig\CountryFlagRenderer;
use Symfony\Component\Asset\Package;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Dependency injection extension for the Phone Input bundle.
 */
class NowoPhoneInputExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('nowo_phone_input.defaults', $config);
        $container->setParameter('nowo_phone_input.countries_file', \dirname(__DIR__).'/Resources/data/countries.json');
        $container->setParameter('nowo_phone_input.patterns_file', \dirname(__DIR__).'/Resources/data/phone_patterns.json');
        $container->setParameter('nowo_phone_input.use_libphonenumber', $config['use_libphonenumber']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $countryProviderDefinition = $container->getDefinition(CountryProvider::class);
        $countryProviderDefinition->replaceArgument('$defaultCountryIso', $config['default_country']);
        $countryProviderDefinition->replaceArgument('$preferredCountries', $config['preferred_countries']);
        $countryProviderDefinition->replaceArgument('$allowedCountries', $config['allowed_countries']);
        $countryProviderDefinition->replaceArgument('$excludedCountries', $config['excluded_countries']);

        $countryFlagRendererDefinition = $container->getDefinition(CountryFlagRenderer::class);
        if ($container->has('Symfony\UX\Icons\IconRendererInterface')) {
            $countryFlagRendererDefinition->setArgument(
                '$iconRenderer',
                new Reference('Symfony\UX\Icons\IconRendererInterface'),
            );
        }

        $phoneValidatorDefinition = $container->getDefinition(PhoneValidator::class);
        if ($this->supportsLibPhoneNumber()) {
            if (!$container->hasDefinition(PhoneNumberUtil::class)) {
                $container->register(PhoneNumberUtil::class)
                    ->setFactory([PhoneNumberUtil::class, 'getInstance'])
                    ->setPublic(false);
            }
            $container->register(LibPhoneNumberChecker::class)
                ->setAutowired(true)
                ->setAutoconfigured(true)
                ->setPublic(false);
            $container->setAlias(NationalPhoneNumberChecker::class, LibPhoneNumberChecker::class);
            $phoneValidatorDefinition->setArgument(
                '$nationalPhoneNumberChecker',
                new Reference(NationalPhoneNumberChecker::class),
            );
        } else {
            $phoneValidatorDefinition->setArgument('$nationalPhoneNumberChecker', null);
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('framework')) {
            return;
        }

        // Only register the package when the Asset component is available (host apps / demos).
        if (!$this->supportsAssetPackage()) {
            return;
        }

        $container->prependExtensionConfig('framework', [
            'assets' => [
                'packages' => [
                    Configuration::ALIAS => [
                        'base_path' => '/bundles/nowophoneinput',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @internal overridable for unit tests when the optional dependency is installed
     */
    protected function supportsLibPhoneNumber(): bool
    {
        return class_exists(PhoneNumberUtil::class);
    }

    /**
     * @internal overridable for unit tests when symfony/asset is installed
     */
    protected function supportsAssetPackage(): bool
    {
        return class_exists(Package::class);
    }

    public function getAlias(): string
    {
        return Configuration::ALIAS;
    }
}
