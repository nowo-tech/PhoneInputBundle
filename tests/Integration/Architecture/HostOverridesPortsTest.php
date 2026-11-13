<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Integration\Architecture;

use Nowo\PhoneInputBundle\Country\CountryProvider;
use Nowo\PhoneInputBundle\Country\CountryProviderInterface;
use Nowo\PhoneInputBundle\DependencyInjection\NowoPhoneInputExtension;
use Nowo\PhoneInputBundle\Phone\E164Parser;
use Nowo\PhoneInputBundle\Phone\E164ParserInterface;
use Nowo\PhoneInputBundle\Phone\NationalPhoneNumberChecker;
use Nowo\PhoneInputBundle\Phone\PhoneNumber;
use Nowo\PhoneInputBundle\Phone\PhoneValidator;
use Nowo\PhoneInputBundle\Phone\PhoneValidatorInterface;
use Nowo\PhoneInputBundle\Validation\PhoneValidationMode;
use Nowo\PhoneInputBundle\Validator\Constraints\ValidPhoneNumberValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * REQ-ARCH-001 rules 1 and 5: the public services are reachable by interface, a host replaces them by aliasing the
 * interface, and `national_number_checker` plugs a host checker in (compiled container, as in a real kernel).
 */
final class HostOverridesPortsTest extends TestCase
{
    public function testInterfacesResolveToTheDefaultServices(): void
    {
        $container = $this->compile([]);

        $this->assertInstanceOf(PhoneValidator::class, $container->get(PhoneValidatorInterface::class));
        $this->assertInstanceOf(E164Parser::class, $container->get(E164ParserInterface::class));
        $this->assertInstanceOf(CountryProvider::class, $container->get(CountryProviderInterface::class));
        // The old class ids still work.
        $this->assertInstanceOf(PhoneValidator::class, $container->get(PhoneValidator::class));
    }

    public function testHostCanReplaceThePhoneValidatorUsedByTheConstraint(): void
    {
        $container = $this->compile([], static function (ContainerBuilder $container): void {
            $container->setDefinition(AlwaysInvalidPhoneValidator::class, new Definition(AlwaysInvalidPhoneValidator::class));
            $container->setAlias(PhoneValidatorInterface::class, AlwaysInvalidPhoneValidator::class);
        });

        $this->assertInstanceOf(AlwaysInvalidPhoneValidator::class, $container->get(PhoneValidatorInterface::class));
        $constraintValidator = $container->get(ValidPhoneNumberValidator::class);
        $this->assertInstanceOf(ValidPhoneNumberValidator::class, $constraintValidator);
        $this->assertInstanceOf(
            AlwaysInvalidPhoneValidator::class,
            (new \ReflectionProperty(ValidPhoneNumberValidator::class, 'phoneValidator'))->getValue($constraintValidator),
        );
    }

    public function testHostCanReplaceTheCountryCatalog(): void
    {
        $container = $this->compile([], static function (ContainerBuilder $container): void {
            $container->setDefinition('app.countries', (new Definition(CountryProvider::class))
                ->setArguments([__DIR__.'/../../../src/Resources/data/countries.json', 'FR']));
            $container->setAlias(CountryProviderInterface::class, 'app.countries');
        });

        $parser = $container->get(E164ParserInterface::class);
        $this->assertInstanceOf(E164ParserInterface::class, $parser);
        // The parser reads the host catalog: its default country (FR) fills an empty value.
        $this->assertSame('FR', $parser->emptyParts('XX')['iso']);
    }

    public function testNationalNumberCheckerKeyPlugsAHostCheckerIn(): void
    {
        $container = $this->compile(
            ['use_libphonenumber' => false, 'national_number_checker' => 'app.phone_checker'],
            static function (ContainerBuilder $container): void {
                $container->setDefinition('app.phone_checker', new Definition(OnlySpainChecker::class));
            },
        );

        $validator = $container->get(PhoneValidatorInterface::class);
        $this->assertInstanceOf(PhoneValidatorInterface::class, $validator);
        // A Spanish number passes the host checker; a valid UK number does not (the checker decides, not the patterns).
        $this->assertTrue($validator->isValid(new PhoneNumber('ES', '+34', '612345678'), PhoneValidationMode::COUNTRY));
        $this->assertFalse($validator->isValid('+447911123456', PhoneValidationMode::COUNTRY));
        $this->assertInstanceOf(OnlySpainChecker::class, $container->get(NationalPhoneNumberChecker::class));
    }

    public function testWithoutTheKeyTheDefaultsAreUnchanged(): void
    {
        $container = $this->compile(['use_libphonenumber' => false]);

        $validator = $container->get(PhoneValidatorInterface::class);
        $this->assertInstanceOf(PhoneValidatorInterface::class, $validator);
        $this->assertTrue($validator->isValid('+447911123456', PhoneValidationMode::COUNTRY));
    }

    /**
     * @param array<string, mixed> $config
     */
    private function compile(array $config, ?\Closure $host = null): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->registerExtension(new NowoPhoneInputExtension());
        $container->loadFromExtension('nowo_phone_input', $config);
        $container->addCompilerPass(new class($host) implements CompilerPassInterface {
            public function __construct(private readonly ?\Closure $host)
            {
            }

            public function process(ContainerBuilder $container): void
            {
                if ($this->host instanceof \Closure) {
                    ($this->host)($container);
                }
                foreach ([PhoneValidator::class, ValidPhoneNumberValidator::class] as $id) {
                    $container->getDefinition($id)->setPublic(true);
                }
                foreach ([PhoneValidatorInterface::class, E164ParserInterface::class, CountryProviderInterface::class, NationalPhoneNumberChecker::class] as $id) {
                    if ($container->hasAlias($id)) {
                        $container->getAlias($id)->setPublic(true);
                    }
                }
            }
        }, PassConfig::TYPE_BEFORE_OPTIMIZATION, 100);
        $container->compile();

        return $container;
    }
}
