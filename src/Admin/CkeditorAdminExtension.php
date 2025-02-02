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

namespace Sonata\FormatterBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\FormatterBundle\Controller\CkeditorAdminController;

/**
 * Adds browser and upload routes to the Admin.
 *
 * @author Kévin Dunglas <kevin@les-tilleuls.coop>
 *
 * @phpstan-extends AbstractAdminExtension<object>
 */
final class CkeditorAdminExtension extends AbstractAdminExtension
{
    public function configureRoutes(AdminInterface $admin, RouteCollectionInterface $collection): void
    {
        $collection->add('ckeditor_browser', 'ckeditor_browser', [
            '_controller' => 'sonata.formatter.ckeditor.extension.controller::browserAction',
        ]);

        $collection->add('ckeditor_upload', 'ckeditor_upload', [
            '_controller' => 'sonata.formatter.ckeditor.extension.controller::uploadAction',
        ]);
    }
}
