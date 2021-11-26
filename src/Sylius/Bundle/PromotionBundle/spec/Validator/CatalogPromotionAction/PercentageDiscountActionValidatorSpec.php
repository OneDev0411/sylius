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

namespace spec\Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionAction;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionAction\ActionValidatorInterface;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionAction;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class PercentageDiscountActionValidatorSpec extends ObjectBehavior
{
    function it_is_an_action_validator(): void
    {
        $this->shouldHaveType(ActionValidatorInterface::class);
    }

    function it_adds_violation_if_catalog_promotion_action_has_invalid_discount(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        CatalogPromotionActionInterface $action
    ): void {
        $action->getConfiguration()->willReturn([]);

        $executionContext->buildViolation('sylius.catalog_promotion_action.percentage_discount.not_number_or_empty')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration.amount')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate([], new CatalogPromotionAction(), $executionContext);
    }

    function it_adds_violation_if_catalog_promotion_action_has_discount_in_wrong_range(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        CatalogPromotionActionInterface $action
    ): void {
        $action->getConfiguration()->willReturn(['amount' => 2]);

        $executionContext->buildViolation('sylius.catalog_promotion_action.percentage_discount.not_in_range')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration.amount')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate(['amount' => 2], new CatalogPromotionAction(), $executionContext);
    }

    function it_adds_violation_if_catalog_promotion_action_has_wrong_type_on_amount(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        CatalogPromotionActionInterface $action
    ): void {
        $action->getConfiguration()->willReturn(['amount' => 'text']);

        $executionContext->buildViolation('sylius.catalog_promotion_action.percentage_discount.not_number_or_empty')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration.amount')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate(['amount' => 'text'], new CatalogPromotionAction(), $executionContext);
    }

    function it_does_nothing_if_catalog_promotion_action_is_valid(
        ExecutionContextInterface $executionContext,
        CatalogPromotionActionInterface $action
    ): void {
        $action->getConfiguration()->willReturn(['amount' => 0.2]);

        $executionContext->buildViolation('sylius.catalog_promotion_action.invalid_type')->shouldNotBeCalled();
        $executionContext->buildViolation('sylius.catalog_promotion_action.percentage_discount.not_valid')->shouldNotBeCalled();
        $executionContext->buildViolation('sylius.catalog_promotion_action.percentage_discount.not_in_range')->shouldNotBeCalled();

        $this->validate(['amount' => 0.2], new CatalogPromotionAction(), $executionContext);
    }
}
