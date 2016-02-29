<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Product;

use Sylius\Behat\Page\ElementNotFoundException;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class ProductShowPage extends SymfonyPage
{
    /**
     * @throws ElementNotFoundException
     */
    public function deleteProduct()
    {
        $this->getDocument()->pressButton('Delete');

        $confirmationModal = $this->getDocument()->find('css', '#confirmation-modal-confirm');
        $this->waitForModalToAppear($confirmationModal);
        $confirmationModal->find('css', 'a:contains("Delete")')->press();
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_backend_product_show';
    }
}
