<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\DependencyInjection;

use Nowo\PhoneInputBundle\Country\CountryProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Dependency injection extension for the Phone Input bundle.
 */
class NowoPhoneInputExtension extends Extension
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

        $countryFlagRendererDefinition = $container->getDefinition(\Nowo\PhoneInputBundle\Twig\CountryFlagRenderer::class);
        if ($container->has('Symfony\UX\Icons\IconRendererInterface')) {
            $countryFlagRendererDefinition->setArgument(
                '$iconRenderer',
                new \Symfony\Component\DependencyInjection\Reference('Symfony\UX\Icons\IconRendererInterface'),
            );
        }
    }

    public function getAlias(): string
    {
        return 'nowo_phone_input';
    }
}
