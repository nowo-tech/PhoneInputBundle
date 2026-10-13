<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Integration;

use Nowo\PhoneInputBundle\DependencyInjection\NowoPhoneInputExtension;
use Nowo\PhoneInputBundle\Twig\CountryFlagRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Compiles a real container so the extension is loaded through MergeExtensionConfigurationPass, where
 * load() runs on an isolated container and cannot see services registered by other bundles.
 */
final class IconRendererWiringTest extends TestCase
{
    public function testUxIconRendererFromAnotherBundleIsInjected(): void
    {
        $container = $this->createContainer();
        // Simulates symfony/ux-icons registering its renderer in its own extension.
        $container->setDefinition('Symfony\UX\Icons\IconRendererInterface', new Definition(FakeIconRenderer::class));
        $container->compile();

        $renderer = $container->get(CountryFlagRenderer::class);
        $this->assertInstanceOf(CountryFlagRenderer::class, $renderer);
        $this->assertInstanceOf(FakeIconRenderer::class, $this->iconRenderer($renderer));
    }

    public function testRendererIsNullWithoutUxIcons(): void
    {
        $container = $this->createContainer();
        $container->compile();

        $renderer = $container->get(CountryFlagRenderer::class);
        $this->assertInstanceOf(CountryFlagRenderer::class, $renderer);
        $this->assertNull($this->iconRenderer($renderer));
    }

    private function createContainer(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->registerExtension(new NowoPhoneInputExtension());
        $container->loadFromExtension('nowo_phone_input', []);
        $container->addCompilerPass(new class implements CompilerPassInterface {
            public function process(ContainerBuilder $container): void
            {
                $container->getDefinition(CountryFlagRenderer::class)->setPublic(true);
            }
        }, PassConfig::TYPE_BEFORE_REMOVING);

        return $container;
    }

    private function iconRenderer(CountryFlagRenderer $renderer): ?object
    {
        $value = (new \ReflectionProperty(CountryFlagRenderer::class, 'iconRenderer'))->getValue($renderer);

        return \is_object($value) ? $value : null;
    }
}

final class FakeIconRenderer
{
    /**
     * @param array<string, string> $attributes
     */
    public function renderIcon(string $name, array $attributes = []): string
    {
        return '<svg data-name="'.$name.'"></svg>';
    }
}
