<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\Twig;

use Nowo\PhoneInputBundle\Form\Type\PhoneType;
use Nowo\PhoneInputBundle\IconSupport\IconSupportChecker;
use Nowo\PhoneInputBundle\Tests\TestFixtures;
use Nowo\PhoneInputBundle\Twig\CountryFlagExtension;
use Nowo\PhoneInputBundle\Twig\CountryFlagRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\AppVariable;
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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\Translator;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;

/**
 * RF1-I1: the external prefix picker `<script src>` carries the request `csp_nonce` attribute when one is set (nonce-only
 * / 'strict-dynamic' CSP), and renders without a nonce attribute when there is no nonce provider or no request.
 */
final class PhoneInputWidgetCspNonceTest extends TestCase
{
    private const SCRIPT = '<script src="/bundles/nowophoneinput/js/nowo-phone-prefix-picker.js" defer';

    public function testScriptCarriesTheRequestNonce(): void
    {
        $request = Request::create('/');
        $request->attributes->set('csp_nonce', 'n0nce-TEST');

        $html = $this->render($request);

        $this->assertStringContainsString(self::SCRIPT.' nonce="n0nce-TEST"></script>', $html);
        $this->assertSame(1, substr_count($html, '<script'));
    }

    public function testNonceIsEscaped(): void
    {
        $request = Request::create('/');
        $request->attributes->set('csp_nonce', 'a"onload="x');

        $html = $this->render($request);

        $this->assertStringContainsString('nonce="a&quot;onload=&quot;x"', $html);
        $this->assertStringNotContainsString('onload="x"', $html);
    }

    public function testScriptWithoutNonceProvider(): void
    {
        $html = $this->render(Request::create('/'));

        $this->assertStringContainsString(self::SCRIPT.'></script>', $html);
        $this->assertStringNotContainsString('nonce=', $html);
    }

    public function testScriptWithoutRequest(): void
    {
        $html = $this->render(null);

        $this->assertStringContainsString(self::SCRIPT.'></script>', $html);
        $this->assertStringNotContainsString('nonce=', $html);
    }

    private function render(?Request $request): string
    {
        $translator = new Translator('en');

        $loader = new FilesystemLoader([\dirname((string) (new \ReflectionClass(FormExtension::class))->getFileName(), 2).'/Resources/views/Form']);
        $loader->addPath(__DIR__.'/../../../src/Resources/views', 'NowoPhoneInputBundle');
        $twig = new Environment($loader, ['strict_variables' => false]);
        $engine = new TwigRendererEngine(['form_div_layout.html.twig', '@NowoPhoneInputBundle/Form/phone_input_widget.html.twig'], $twig);
        $twig->addRuntimeLoader(new FactoryRuntimeLoader([FormRenderer::class => static fn (): FormRenderer => new FormRenderer($engine)]));
        $twig->addExtension(new FormExtension($translator));
        $twig->addExtension(new TranslationExtension($translator));
        $twig->addExtension(new AssetExtension(new Packages(null, ['nowo_phone_input' => new PathPackage('/bundles/nowophoneinput', new EmptyVersionStrategy())])));
        $icons = new IconSupportChecker(uxIconsAvailable: false, httpClientAvailable: false);
        $twig->addExtension(new CountryFlagExtension(new CountryFlagRenderer($icons)));

        $app = new AppVariable();
        $stack = new RequestStack();
        if ($request instanceof Request) {
            $stack->push($request);
        }
        $app->setRequestStack($stack);
        $twig->addGlobal('app', $app);

        $provider = TestFixtures::countryProvider();
        $factory = Forms::createFormFactoryBuilder()
            ->addExtension(new PreloadedExtension([new PhoneType($provider, TestFixtures::e164Parser($provider), $icons)], []))
            ->getFormFactory();
        $view = $factory->createBuilder(FormType::class)->add('phone', PhoneType::class)->getForm()->createView();

        return $twig->createTemplate('{{ form_widget(form.phone) }}')->render(['form' => $view]);
    }
}
