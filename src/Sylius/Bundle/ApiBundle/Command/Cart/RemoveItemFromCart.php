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

namespace Sylius\Bundle\ApiBundle\Command\Cart;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;

/** @experimental */
class RemoveItemFromCart implements OrderTokenValueAwareInterface
{
    /** @var string|null */
    public $orderTokenValue;

    /**
     * @var string
     * @psalm-immutable
     */
    public $itemId;

    public function __construct(?string $orderTokenValue, string $itemId)
    {
        $this->orderTokenValue = $orderTokenValue;
        $this->itemId = $itemId;
    }

    public function getOrderTokenValue(): ?string
    {
        return $this->orderTokenValue;
    }

    public function setOrderTokenValue(?string $orderTokenValue): void
    {
        $this->orderTokenValue = $orderTokenValue;
    }

    public function getItemId(): string
    {
        return $this->itemId;
    }

    public function setItemId(string $itemId): void
    {
        $this->itemId = $itemId;
    }
}
