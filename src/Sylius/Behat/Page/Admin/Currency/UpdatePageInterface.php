<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Currency;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpadatePageInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface UpdatePageInterface extends BaseUpadatePageInterface
{
    public function enable();
    public function disable();

    /**
     * @param string $exchangeRate
     */
    public function changeExchangeRate($exchangeRate);

    /**
     * @return string
     */
    public function getExchangeRateValue();
}
