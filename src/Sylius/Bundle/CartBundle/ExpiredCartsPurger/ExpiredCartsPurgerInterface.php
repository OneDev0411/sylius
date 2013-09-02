<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\ExpiredCartsPurger;

use Sylius\Bundle\CartBundle\Model\CartInterface;

/**
 * Interface for the expired carts purger.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
interface ExpiredCartsPurgerInterface
{
    /**
     * Purge all expired carts.
     *
     * @return boolean
     */
    public function purge();
}
