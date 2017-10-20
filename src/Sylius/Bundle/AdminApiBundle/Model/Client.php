<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminApiBundle\Model;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;

class Client extends BaseClient implements ClientInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPublicId(): string
    {
        return $this->getRandomId();
    }
}
