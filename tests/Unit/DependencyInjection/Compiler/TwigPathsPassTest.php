<?php

declare(strict_types=1);

namespace Nowo\PhoneInputBundle\Tests\Unit\DependencyInjection\Compiler;

use Nowo\PhoneInputBundle\DependencyInjection\Compiler\TwigPathsPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class TwigPathsPassTest extends TestCase
{
    public function testProcessSkipsWhenNativeLoaderIsMissing(): void
    {
        $container = new ContainerBuilder();
        (new TwigPathsPass())->process($container);

        $this->assertFalse($container->hasDefinition('twig.loader.native'));
    }

    public function testProcessAddsPathToNativeFilesystemLoaderDefinition(): void
    {
        $container = new ContainerBuilder();
        $container->setDefinition('twig.loader.native_filesystem', new Definition(\stdClass::class));

        (new TwigPathsPass())->process($container);

        $methodCalls = $container->getDefinition('twig.loader.native_filesystem')->getMethodCalls();
        $this->assertCount(1, $methodCalls);
        $this->assertSame('addPath', $methodCalls[0][0]);
        $this->assertStringEndsWith('/src/Resources/views', $methodCalls[0][1][0]);
        $this->assertSame('NowoPhoneInputBundle', $methodCalls[0][1][1]);
    }

    public function testProcessUsesNativeLoaderAliasWhenPresent(): void
    {
        $container = new ContainerBuilder();
        $container->setDefinition('custom.loader', new Definition(\stdClass::class));
        $container->setAlias('twig.loader.native', 'custom.loader');

        (new TwigPathsPass())->process($container);

        $methodCalls = $container->getDefinition('custom.loader')->getMethodCalls();
        $this->assertSame('NowoPhoneInputBundle', $methodCalls[0][1][1]);
    }
}
