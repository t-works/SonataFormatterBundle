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

namespace Sonata\FormatterBundle\Formatter;

use Twig\Environment;

interface PoolInterface
{
    public function add(string $code, Formatter $formatter, ?Environment $env = null): void;

    public function has(string $code): bool;

    /**
     * @return array{Formatter, Environment|null}
     */
    public function get(string $code): array;

    public function transform(string $code, string $text): string;

    /**
     * @return array<string, array{Formatter, Environment|null}>
     */
    public function getFormatters(): array;

    public function getDefaultFormatter(): string;
}
