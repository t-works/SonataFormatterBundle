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

namespace Sonata\FormatterBundle\Extension;

use Twig\Environment;
use Twig\Extension\AbstractExtension;

abstract class BaseExtension extends AbstractExtension implements ExtensionInterface
{
    public function getAllowedFilters(): array
    {
        return [];
    }

    public function getAllowedTags(): array
    {
        return [];
    }

    public function getAllowedFunctions(): array
    {
        return [];
    }

    public function getAllowedProperties(): array
    {
        return [];
    }

    public function getAllowedMethods(): array
    {
        return [];
    }

    public function initRuntime(Environment $environment): void
    {
    }

    public function getTokenParsers(): array
    {
        return [];
    }

    public function getNodeVisitors(): array
    {
        return [];
    }

    public function getFilters(): array
    {
        return [];
    }

    public function getTests(): array
    {
        return [];
    }

    public function getFunctions(): array
    {
        return [];
    }

    /**
     * @return array{0?: array<string, array{precedence: int, class: class-string}>, 1?: array<string, array{precedence: int, class: class-string, associativity: int}>}
     */
    public function getOperators(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public function getGlobals(): array
    {
        return [];
    }
}
