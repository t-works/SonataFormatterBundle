<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\DependencyInjection;

use Sonata\FormatterBundle\Formatter\ExtendableFormatter;
use Sonata\FormatterBundle\Twig\Loader\LoaderSelector;
use Sonata\FormatterBundle\Twig\SecurityPolicyContainerAware;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Twig\Environment;
use Twig\Extension\SandboxExtension;
use Twig\Lexer;
use Twig\Loader\ArrayLoader;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class SonataFormatterExtension extends Extension
{
    /**
     * Loads the url shortener configuration.
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('formatter.xml');
        $loader->load('twig.xml');

        $loader->load('validators.xml');

        $bundles = $container->getParameter('kernel.bundles');
        \assert(\is_array($bundles));

        if (isset($bundles['FOSCKEditorBundle'])) {
            $loader->load('form.xml');
        }

        if (isset($bundles['SonataBlockBundle'])) {
            $loader->load('block.xml');
        }

        if (isset($bundles['SonataMediaBundle'])) {
            $loader->load('ckeditor.xml');
        }

        if (!\array_key_exists($config['default_formatter'], $config['formatters'])) {
            throw new \InvalidArgumentException(sprintf(
                'SonataFormatterBundle - Invalid default formatter: %s, available: %s',
                $config['default_formatter'],
                sprintf('["%s"]', implode('", "', array_keys($config['formatters'])))
            ));
        }

        $pool = $container->getDefinition('sonata.formatter.pool');
        $pool->addArgument($config['default_formatter']);

        foreach ($config['formatters'] as $code => $formatterConfig) {
            if (0 === \count($formatterConfig['extensions'])) {
                $env = null;
            } else {
                $env = new Reference($this->createEnvironment(
                    $container,
                    $code,
                    $formatterConfig['service'],
                    $formatterConfig['extensions']
                ));
            }

            $pool->addMethodCall(
                'add',
                [$code, new Reference($formatterConfig['service']), $env]
            );
        }

        $container->setParameter(
            'sonata.formatter.ckeditor.configuration.templates',
            $config['ckeditor']['templates']
        );
    }

    public function getXsdValidationBasePath(): string
    {
        return __DIR__.'/../Resources/config/schema';
    }

    public function getNamespace(): string
    {
        return 'http://www.sonata-project.org/schema/dic/formatter';
    }

    public function getAlias(): string
    {
        return 'sonata_formatter';
    }

    /**
     * @param string[] $extensions
     */
    private function createEnvironment(
        ContainerBuilder $container,
        string $code,
        string $formatterId,
        array $extensions
    ): string {
        $loader = new Definition(ArrayLoader::class);

        $loader->setPublic(false);

        $container->setDefinition(sprintf('sonata.formatter.twig.loader.%s', $code), $loader);

        $loaderSelector = new Definition(LoaderSelector::class, [
            new Reference(sprintf('sonata.formatter.twig.loader.%s', $code)),
            new Reference('twig.loader'),
        ]);
        $loaderSelector->setPublic(false);

        $env = new Definition(Environment::class, [$loaderSelector, [
            'debug' => false,
            'strict_variables' => false,
            'charset' => 'UTF-8',
        ]]);
        $env->setPublic(false);

        $container->setDefinition(sprintf('sonata.formatter.twig.env.%s', $code), $env);

        $sandboxPolicy = new Definition(SecurityPolicyContainerAware::class, [new Reference('service_container'), $extensions]);
        $sandboxPolicy->setPublic(false);
        $container->setDefinition(sprintf('sonata.formatter.twig.sandbox.%s.policy', $code), $sandboxPolicy);

        $sandbox = new Definition(SandboxExtension::class, [$sandboxPolicy, true]);
        $sandbox->setPublic(false);

        $container->setDefinition(sprintf('sonata.formatter.twig.sandbox.%s', $code), $sandbox);

        $formatterDefinition = $container->getDefinition($formatterId);
        if (
            null !== $formatterDefinition->getClass()
            && is_a($formatterDefinition->getClass(), ExtendableFormatter::class, true)
        ) {
            $formatterDefinition->addMethodCall('addExtension', [new Reference(sprintf('sonata.formatter.twig.sandbox.%s', $code))]);

            foreach ($extensions as $extension) {
                $formatterDefinition->addMethodCall('addExtension', [new Reference($extension)]);
            }
        }

        $lexer = new Definition(Lexer::class, [new Reference(sprintf('sonata.formatter.twig.env.%s', $code)), [
            'tag_comment' => ['<#', '#>'],
            'tag_block' => ['<%', '%>'],
            'tag_variable' => ['<%=', '%>'],
        ]]);
        $lexer->setPublic(false);

        $container->setDefinition(sprintf('sonata.formatter.twig.lexer.%s', $code), $lexer);

        $env->addMethodCall('setLexer', [new Reference(sprintf('sonata.formatter.twig.lexer.%s', $code))]);

        return sprintf('sonata.formatter.twig.env.%s', $code);
    }
}
