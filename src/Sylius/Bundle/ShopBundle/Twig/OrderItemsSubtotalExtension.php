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

namespace Sylius\Bundle\ShopBundle\Twig;

use Sylius\Bundle\ShopBundle\Calculator\OrderItemsSubtotalCalculatorInterface;
use Sylius\Component\Core\Model\OrderInterface;

class OrderItemsSubtotalExtension extends \Twig_Extension
{
    /** @var OrderItemsSubtotalCalculatorInterface */
    private $calculator;

    public function __construct(OrderItemsSubtotalCalculatorInterface $calculator)
    {
        $this->calculator = $calculator;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_Function('sylius_order_items_subtotal', [$this, 'getSubtotal']),
        ];
    }

    public function getSubtotal(OrderInterface $order): int
    {
        return $this->calculator->getSubtotal($order);
    }
}
