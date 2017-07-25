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

namespace Sylius\Bundle\FixturesBundle\Suite;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface SuiteRegistryInterface
{
    /**
     * @param string $name
     *
     * @return SuiteInterface
     *
     * @throws SuiteNotFoundException
     */
    public function getSuite($name);

    /**
     * @return SuiteInterface[]
     */
    public function getSuites();
}
