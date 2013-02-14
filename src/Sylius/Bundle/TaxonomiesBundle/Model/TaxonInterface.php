<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomiesBundle\Model;

/**
 * Interface for taxons.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface TaxonInterface
{
    /**
     * Get taxon id.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Get the taxonomy.
     *
     * @return TaxonomyInterface
     */
    public function getTaxonomy();

    /**
     * Set the taxonomy.
     *
     * @param null|TaxonomyInterface $taxonomy
     */
    public function setTaxonomy(TaxonomyInterface $taxonomy = null);

    /**
     * Is root taxon?
     *
     * @return Boolean
     */
    public function isRoot();

    /**
     * Get parent taxon.
     *
     * @return TaxonInterface
     */
    public function getParent();

    /**
     * Set parent taxon.
     *
     * @param null|TaxonInterface $taxon
     */
    public function setParent(TaxonInterface $taxon = null);

    /**
     * Get children taxons.
     *
     * @return Collection
     */
    public function getChildren();

    /**
     * Get taxon name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set taxon name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug();

    /**
     * Set slug.
     *
     * @param string $slug
     */
    public function setSlug($slug);

    /**
     * Get permalink.
     *
     * @return string
     */
    public function getPermalink();

    /**
     * Set permalink.
     *
     * @param string $permalink
     */
    public function setPermalink($permalink);
}
