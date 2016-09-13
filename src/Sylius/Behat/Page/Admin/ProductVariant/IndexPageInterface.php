<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ProductVariant;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface IndexPageInterface extends BaseIndexPageInterface
{
    /**
     * @param ProductVariantInterface $productVariant
     * @param int $quantity
     *
     * @return bool
     */
    public function hasOnHandQuantity(ProductVariantInterface $productVariant, $quantity);

    /**
     * @param ProductVariantInterface $productVariant
     * @param int $quantity
     *
     * @return bool
     */
    public function hasOnHoldQuantity(ProductVariantInterface $productVariant, $quantity);
}
