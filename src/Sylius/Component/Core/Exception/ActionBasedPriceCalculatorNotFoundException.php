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

namespace Sylius\Component\Core\Exception;

final class ActionBasedPriceCalculatorNotFoundException extends \Exception
{
    public function __construct(?\Exception $previousException = null)
    {
        parent::__construct('Price calculator for given action type was not found.', 0, $previousException);
    }
}
