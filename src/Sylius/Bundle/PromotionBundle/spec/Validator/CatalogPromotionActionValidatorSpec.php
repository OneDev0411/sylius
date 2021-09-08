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

namespace spec\Sylius\Bundle\PromotionBundle\Validator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionAction;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class CatalogPromotionActionValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext): void
    {
        $this->initialize($executionContext);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_adds_violation_if_catalog_promotion_action_has_invalid_type(
        ExecutionContextInterface $executionContext,
        CatalogPromotionActionInterface $action
    ): void {
        $action->getType()->willReturn('wrong_type');

        $executionContext->addViolation('sylius.catalog_promotion_action.invalid_type')->shouldBeCalled();

        $this->validate($action, new CatalogPromotionAction());
    }

    function it_adds_violation_if_catalog_promotion_action_has_invalid_discount(
        ExecutionContextInterface $executionContext,
        CatalogPromotionActionInterface $action
    ): void {
        $action->getType()->willReturn(CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT);
        $action->getConfiguration()->willReturn([]);

        $executionContext->addViolation('sylius.catalog_promotion_action.percentage_discount.not_valid')->shouldBeCalled();

        $this->validate($action, new CatalogPromotionAction());
    }

    function it_adds_violation_if_catalog_promotion_action_has_discount_in_wrong_range(
        ExecutionContextInterface $executionContext,
        CatalogPromotionActionInterface $action
    ): void {
        $action->getType()->willReturn(CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT);
        $action->getConfiguration()->willReturn(['amount' => 2]);

        $executionContext->addViolation('sylius.catalog_promotion_action.percentage_discount.not_in_range')->shouldBeCalled();

        $this->validate($action, new CatalogPromotionAction());
    }

    function it_does_nothing_if_catalog_promotion_action_is_valid(
        ExecutionContextInterface $executionContext,
        CatalogPromotionActionInterface $action
    ): void {
        $action->getType()->willReturn(CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT);
        $action->getConfiguration()->willReturn(['amount' => 0.2]);

        $executionContext->addViolation('sylius.catalog_promotion_action.invalid_type')->shouldNotBeCalled();
        $executionContext->addViolation('sylius.catalog_promotion_action.percentage_discount.not_valid')->shouldNotBeCalled();
        $executionContext->addViolation('sylius.catalog_promotion_action.percentage_discount.not_in_range')->shouldNotBeCalled();

        $this->validate($action, new CatalogPromotionAction());
    }
}
