<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\Renderer;

use Sylius\Component\Metadata\Model\MetadataInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface MetadataRendererInterface
{
    /**
     * @param MetadataInterface $metadata
     *
     * @return string
     */
    public function render(MetadataInterface $metadata);

    /**
     * @param MetadataInterface $metadata
     *
     * @return boolean
     */
    public function supports(MetadataInterface $metadata);
}
