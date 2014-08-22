<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\Twig;

/**
 * Search landing page controller.
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class SearchElementExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $getSearchResultSnippetFunction = new \Twig_SimpleFunction('getSearchResultSnippet', function ($object) {

            $pathArray = explode('\\',get_class($object));

            return 'SyliusSearchBundle:SearchResultSnippets:'.lcfirst(array_pop($pathArray)).'.html.twig';
        });

        return array(
            'getsearchresultsnippet' => $getSearchResultSnippetFunction
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'search_element_extension';
    }

}
