<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Model;

/**
 * Promotion rule model interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface RuleInterface
{
    const TYPE_ORDER_TOTAL = 1;
    const TYPE_ITEM_COUNT = 2;

    public function getId();
    public function getType();
    public function setType($type);
    public function getConfiguration();
    public function setConfiguration(array $configuration);
    public function getPromotion();
    public function setPromotion(PromotionInterface $promotion = null);
}
