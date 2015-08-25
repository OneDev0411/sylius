<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReviewBundle\Updater;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Review\Calculator\AverageRatingCalculatorInterface;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @author Mateusz Zalewski <mateusz.p.zalewski@gmail.com>
 */
class ReviewableAverageRatingUpdater implements ReviewableAverageRatingUpdaterInterface
{
    /**
     * @var AverageRatingCalculatorInterface
     */
    private $averageRatingCalculator;

    /**
     * @var ObjectManager
     */
    private $reviewSubjectManager;

    /**
     * @param AverageRatingCalculatorInterface $averageRatingCalculator
     * @param ObjectManager                    $reviewSubjectManager
     */
    public function __construct(AverageRatingCalculatorInterface $averageRatingCalculator, ObjectManager $reviewSubjectManager)
    {
        $this->averageRatingCalculator = $averageRatingCalculator;
        $this->reviewSubjectManager = $reviewSubjectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function update(ReviewInterface $review)
    {
        $reviewSubject = $review->getReviewSubject();
        $averageRating = $this->averageRatingCalculator->calculate($reviewSubject);

        $reviewSubject->setAverageRating($averageRating);
        $this->reviewSubjectManager->flush($reviewSubject);
    }
}
