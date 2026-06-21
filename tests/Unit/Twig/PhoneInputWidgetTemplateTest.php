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
        $this->assertStringContainsString('prefix_search', $content);
        $this->assertStringContainsString('_phone_country_flag.html.twig', $content);
    }

    public function testUxIconRendererFallsBackWhenRenderThrows(): void
    {
        $iconRenderer = new class {
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
