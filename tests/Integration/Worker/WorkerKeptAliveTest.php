<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Integration\Worker;

use Nowo\PhoneInputBundle\Form\Type\PhoneType;
use Nowo\PhoneInputBundle\Form\ValueFormat;
use Nowo\PhoneInputBundle\IconSupport\IconSupportChecker;
use Nowo\PhoneInputBundle\Tests\TestFixtures;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Forms;

/**
 * REQ-RUNTIME-002: the phone type, the country provider and the E.164 parser are built once and kept, as a FrankenPHP
 * worker with kernel reset false keeps them; nothing is reset. N host forms are built, submitted and viewed
 * alternating two inputs (two countries, two value formats, two class sets); each result must contain only its own
 * data, and memory must stay flat.
 */
final class WorkerKeptAliveTest extends TestCase
{
    private const ITERATIONS = 1200;
    private const WARM_UP = 100;
    private const MARGIN = 4 * 1024 * 1024;

    public function testAlternatingInputsOnSharedServicesLeakNothingAndKeepMemoryFlat(): void
    {
        $provider = TestFixtures::countryProvider();
        $factory = Forms::createFormFactoryBuilder()
            ->addType(new PhoneType($provider, TestFixtures::e164Parser($provider), new IconSupportChecker(uxIconsAvailable: true, httpClientAvailable: true)))
            ->getFormFactory();

        $inputs = [
            'alpha' => [
                'options' => ['default_country' => 'ES', 'value_format' => ValueFormat::CONCATENATED, 'container_classes' => ['alpha-box']],
                'submit' => ['country_iso' => 'ES', 'national_number' => '612345678'],
                'expected' => '+34612345678',
            ],
            'beta' => [
                'options' => ['default_country' => 'FR', 'value_format' => ValueFormat::SEPARATED, 'container_classes' => ['beta-box']],
                'submit' => ['country_iso' => 'FR', 'national_number' => '712345678'],
                'expected' => ['iso' => 'FR', 'prefix' => '+33', 'national_number' => '712345678'],
            ],
        ];

        $baseline = 0;
        for ($i = 0; $i < self::ITERATIONS; ++$i) {
            if (self::WARM_UP === $i) {
                \gc_collect_cycles();
                $baseline = \memory_get_usage();
            }

            $who = 0 === $i % 2 ? 'alpha' : 'beta';
            $data = $inputs[$who];
            $form = $factory->createBuilder(FormType::class)->add('phone', PhoneType::class, $data['options'])->getForm();
            $form->submit(['phone' => $data['submit']]);

            self::assertTrue($form->isSynchronized(), \sprintf('iteration %d', $i));
            self::assertSame($data['expected'], $form->get('phone')->getData(), \sprintf('iteration %d', $i));

            $vars = $form->createView()['phone']->vars;
            self::assertSame($data['options']['default_country'], $vars['default_country'], \sprintf('iteration %d', $i));
            self::assertSame($data['options']['value_format']->value, $vars['value_format'], \sprintf('iteration %d', $i));
            self::assertSame($data['options']['container_classes'], $vars['container_classes'], \sprintf('iteration %d', $i));
        }

        \gc_collect_cycles();
        $growth = \memory_get_usage() - $baseline;
        self::assertLessThan(self::MARGIN, $growth, \sprintf('memory grew by %d bytes over %d iterations', $growth, self::ITERATIONS - self::WARM_UP));
    }
}
