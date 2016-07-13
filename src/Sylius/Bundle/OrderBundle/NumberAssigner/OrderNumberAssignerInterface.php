<?php

/*
 * This file is a part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\NumberAssigner;

use Sylius\Component\Order\Model\OrderInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface OrderNumberAssignerInterface
{
    /**
     * @param OrderInterface $order
     */
    public function assignNumber(OrderInterface $order);
}
