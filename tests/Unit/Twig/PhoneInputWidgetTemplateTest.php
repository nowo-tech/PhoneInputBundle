<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\Twig;

use Nowo\PhoneInputBundle\Form\FlagDisplay;
use Nowo\PhoneInputBundle\IconSupport\IconSupportChecker;
use Nowo\PhoneInputBundle\Twig\CountryFlagRenderer;
use PHPUnit\Framework\TestCase;

final class PhoneInputWidgetTemplateTest extends TestCase
{
    public function testWidgetTemplateContainsPrefixSelectorAndSearchHooks(): void
    {
        $path = __DIR__.'/../../../src/Resources/views/Form/phone_input_widget.html.twig';
        $content = (string) file_get_contents($path);

        $this->assertStringContainsString('nowo-phone-input', $content);
        $this->assertStringContainsString('<nowo-phone-input', $content);
        $this->assertStringContainsString('prefix_search', $content);
        $this->assertStringContainsString('@NowoPhoneInputBundle/Form/_phone_country_flag.html.twig', $content);
        $this->assertStringContainsString('data-controller="phone-prefix-picker"', $content);
        $this->assertStringContainsString('data-nowo-phone-prefix-picker', $content);
        $this->assertStringContainsString("asset('js/nowo-phone-prefix-picker.js', 'nowo_phone_input')", $content);
        $this->assertMatchesRegularExpression('/<script\s+src=/', $content);
        $this->assertDoesNotMatchRegularExpression('/<script>(?!\s*<\/script>)/', $content);
        $this->assertStringNotContainsString('(function ()', $content);
    }

    public function testPrefixPickerScriptAssetExists(): void
    {
        $path = __DIR__.'/../../../src/Resources/public/js/nowo-phone-prefix-picker.js';
        $this->assertFileExists($path);

        $content = (string) file_get_contents($path);
        $this->assertStringContainsString('NowoPhonePrefixPicker', $content);
        $this->assertStringContainsString('nowo-phone-input', $content);
        $this->assertStringContainsString('customElements.define(TAG, NowoPhoneInputElement)', $content);
        $this->assertStringContainsString('phone-prefix-picker', $content);
        $this->assertStringContainsString('nowo-phone-input__prefix-dropdown--portaled', $content);
    }

    public function testUxIconRendererFallsBackWhenRenderThrows(): void
    {
        $iconRenderer = new class {
            /**
             * @param array<string, mixed> $attributes
             */
            public function renderIcon(string $name, array $attributes = []): string
            {
                throw new \RuntimeException('icon unavailable');
            }
        };

        $renderer = new CountryFlagRenderer(
            new IconSupportChecker(uxIconsAvailable: true, httpClientAvailable: true),
            $iconRenderer,
        );

        $html = $renderer->render(['iso' => 'ES'], FlagDisplay::UX_ICON->value);

        $this->assertStringContainsString('fi-es', $html);
    }
}
