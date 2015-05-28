<?php

namespace Sylius\Bundle\ThemeBundle\Asset;

use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface PathResolverInterface
{
    /**
     * Applies theme hashcode to given asset file in order to distinguish it from
     * another same named assets files with another theme or without it.
     *
     * @param string $path
     * @param ThemeInterface $theme
     *
     * @return string
     */
    public function resolve($path, ThemeInterface $theme);
}