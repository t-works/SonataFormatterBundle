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

interface ExtensionInterface
{
    /**
     * @return string[]
     */
    public function getAllowedFilters(): array;

    /**
     * @return string[]
     */
    public function getAllowedTags(): array;

    /**
     * @return string[]
     */
    public function getAllowedFunctions(): array;

    /**
     * @return array<class-string, array<string>>
     */
    public function getAllowedProperties(): array;

    /**
     * @return array<class-string, array<string>>
     */
    public function getAllowedMethods(): array;
}
