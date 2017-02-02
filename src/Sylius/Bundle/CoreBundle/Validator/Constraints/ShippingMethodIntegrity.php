<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ShippingMethodIntegrity extends Constraint
{
    /**
     * @var string
     */
    public $message = 'sylius.order.shipping_method_integrity';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'sylius_shipping_method_integrity_validator';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
