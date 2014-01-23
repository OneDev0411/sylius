<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomiesBundle\Repository;

use Sylius\Bundle\TaxonomiesBundle\Model\TaxonomyInterface;

interface TaxonRepositoryInterface
{
    public function getTaxonsAsList(TaxonomyInterface $taxonomy);
}
