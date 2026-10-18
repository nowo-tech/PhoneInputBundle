<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\Twig;

use Nowo\PhoneInputBundle\Form\Type\PhoneType;
use Nowo\PhoneInputBundle\IconSupport\IconSupportChecker;
use Nowo\PhoneInputBundle\Tests\TestFixtures;
use Nowo\PhoneInputBundle\Twig\CountryFlagExtension;
use Nowo\PhoneInputBundle\Twig\CountryFlagRenderer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;

/**
 * The widget texts (prefix selector label, search placeholder, empty state) are messages of the bundle domain with
 * real translations in every shipped locale: no literal English looked up in the field domain (the profiler logged
 * "Country prefix", "Search country or prefix…" and "No countries found" as missing messages).
 */
final class PhoneInputWidgetTranslationTest extends TestCase
{
    private const TRANSLATIONS = __DIR__.'/../../../src/Resources/translations';

    /**
     * @return iterable<string, array{string, string, string, string}>
     */
    public static function locales(): iterable
    {
        yield 'en' => ['en', 'Country prefix', 'Search country or prefix…', 'No countries found'];
        yield 'es' => ['es', 'Prefijo del país', 'Buscar país o prefijo…', 'No se encontraron países'];
        yield 'de' => ['de', 'Landesvorwahl', 'Land oder Vorwahl suchen…', 'Keine Länder gefunden'];
    }

    #[DataProvider('locales')]
    public function testWidgetTextsAreBundleMessages(string $locale, string $label, string $placeholder, string $empty): void
    {
        $translator = new Translator($locale);
        $translator->addLoader('yaml', new YamlFileLoader());
        foreach (glob(self::TRANSLATIONS.'/*.yaml') ?: [] as $file) {
            [$domain, $fileLocale] = explode('.', basename($file));
            $translator->addResource('yaml', $file, $fileLocale, $domain);
        }
        $collector = new DataCollectorTranslator($translator);

        $loader = new FilesystemLoader([\dirname((string) (new \ReflectionClass(FormExtension::class))->getFileName(), 2).'/Resources/views/Form']);
        $loader->addPath(__DIR__.'/../../../src/Resources/views', 'NowoPhoneInputBundle');
        $twig = new Environment($loader, ['strict_variables' => false]);
        $engine = new TwigRendererEngine(['form_div_layout.html.twig', '@NowoPhoneInputBundle/Form/phone_input_widget.html.twig'], $twig);
        $twig->addRuntimeLoader(new FactoryRuntimeLoader([FormRenderer::class => static fn (): FormRenderer => new FormRenderer($engine)]));
        $twig->addExtension(new FormExtension($collector));
        $twig->addExtension(new TranslationExtension($collector));
        $twig->addExtension(new AssetExtension(new Packages(null, ['nowo_phone_input' => new PathPackage('/bundles/nowophoneinput', new EmptyVersionStrategy())])));
        $icons = new IconSupportChecker(uxIconsAvailable: false, httpClientAvailable: false);
        $twig->addExtension(new CountryFlagExtension(new CountryFlagRenderer($icons)));

        $provider = TestFixtures::countryProvider();
        $factory = Forms::createFormFactoryBuilder()
            ->addExtension(new PreloadedExtension([new PhoneType($provider, TestFixtures::e164Parser($provider), $icons)], []))
            ->getFormFactory();
        $view = $factory->createBuilder(FormType::class, null, ['translation_domain' => 'app'])->add('phone', PhoneType::class)->getForm()->createView();

        $html = $twig->createTemplate('{{ form_widget(form.phone) }}')->render(['form' => $view]);

        $this->assertStringContainsString('aria-label="'.$label.'"', $html);
        $this->assertStringContainsString('placeholder="'.$placeholder.'"', $html);
        $this->assertStringContainsString($empty, $html);

        $missing = array_filter(
            $collector->getCollectedMessages(),
            static fn (array $message): bool => DataCollectorTranslator::MESSAGE_MISSING === $message['state'],
        );
        $this->assertSame([], array_values(array_map(static fn (array $message): string => $message['locale'].' '.$message['domain'].': '.$message['id'], $missing)));
    }

    public function testEveryLocaleShipsTheSameKeys(): void
    {
        foreach (['NowoPhoneInputBundle', 'validators'] as $domain) {
            $reference = null;
            foreach (['en', 'es', 'fr', 'de', 'it', 'pt', 'nl'] as $locale) {
                $file = self::TRANSLATIONS.'/'.$domain.'.'.$locale.'.yaml';
                $this->assertFileExists($file);
                $keys = array_keys((array) Yaml::parseFile($file));
                $reference ??= $keys;
                $this->assertSame($reference, $keys, $domain.'.'.$locale);
            }
        }
    }
}
