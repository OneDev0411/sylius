<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\ProductReview;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CreatePage extends SymfonyPage implements CreatePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_product_reviews_create';
    }

    /**
     * {@inheritdoc}
     */
    public function titleReview($title)
    {
        $this->getDocument()->fillField('Title', $title);
    }

    /**
     * {@inheritdoc}
     */
    public function setComment($comment)
    {
        $this->getDocument()->fillField('sylius_product_review_comment', $comment);
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthor($author)
    {
        $this->getDocument()->fillField('Author', $author);
    }

    /**
     * {@inheritdoc}
     */
    public function rateReview($rate)
    {
        $this->getElement('rate', ['%rate%' => $rate])->click();
    }

    public function submitReview()
    {
        $this->getDocument()->pressButton('Add');
    }

    /**
     * {@inheritdoc}
     */
    public function getRateValidationMessage()
    {
        return $this->getElement('rating')->find('css', '.sylius-validation-error')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getTitleValidationMessage()
    {
        return $this->getElement('title')->find('css', '.sylius-validation-error')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getCommentValidationMessage()
    {
        return $this->getElement('comment')->find('css', '.sylius-validation-error')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorValidationMessage()
    {
        return $this->getElement('author')->find('css', '.sylius-validation-error')->getText();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'author' => 'form, .field:nth-child(4)',
            'comment' => 'form, .field:nth-child(3)',
            'rate' => '.star.rating .icon:nth-child(%rate%)',
            'rating' => 'form .field:first-child',
            'title' => 'form .field:nth-child(2)',
        ]);
    }
}
