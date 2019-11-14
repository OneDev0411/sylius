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

namespace Sylius\Component\Product\Model;

use Sylius\Component\Attribute\Model\Attribute as BaseAttribute;
use Sylius\Component\Attribute\Model\AttributeTranslationInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

class ProductAttribute extends BaseAttribute implements ProductAttributeInterface
{
    /**
     * @return ProductAttributeTranslation
     */
    protected function createTranslation(): TranslationInterface
    {
        return new ProductAttributeTranslation();
    }
}
