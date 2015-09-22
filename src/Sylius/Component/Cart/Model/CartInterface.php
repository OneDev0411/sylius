<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Cart\Model;

use Sylius\Component\Order\Model\OrderInterface;

/**
 * Cart model interface.
 * All driver cart entities or documents should implement this interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface CartInterface extends OrderInterface
{
    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @return \DateTime
     */
    public function getExpiresAt();

    /**
     * @param \DateTime|null $expiresAt
     */
    public function setExpiresAt(\DateTime $expiresAt = null);

    /**
     * Bumps the expiration time.
     * Default is +3 hours.
     */
    public function incrementExpiresAt();

    /**
     * @return bool
     */
    public function isExpired();
}
