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

namespace spec\Sylius\Bundle\ApiBundle\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Cart\ApplyCouponToCart;
use Sylius\Bundle\ApiBundle\Validator\Constraints\PromotionCouponEligibility;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class PromotionCouponEligibilityValidatorSpec extends ObjectBehavior
{
    function let(
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderRepositoryInterface $orderRepository,
        PromotionEligibilityCheckerInterface $promotionChecker,
        PromotionCouponEligibilityCheckerInterface $promotionCouponChecker
    ): void {
        $this->beConstructedWith(
            $promotionCouponRepository,
            $orderRepository,
            $promotionChecker,
            $promotionCouponChecker
        );
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_constraint_is_not_of_expected_type(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', new class() extends Constraint {}])
        ;
    }

    function it_does_not_add_violation_if_promotion_coupon_is_eligible(
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        PromotionCouponEligibilityCheckerInterface $promotionCouponChecker,
        PromotionEligibilityCheckerInterface $promotionChecker,
        PromotionCouponInterface $promotionCoupon,
        PromotionInterface $promotion,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $cart,
        ExecutionContextInterface $executionContext,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel
    ): void {
        $this->initialize($executionContext);
        $constraint = new PromotionCouponEligibility();

        $value = new ApplyCouponToCart('couponCode');
        $value->setOrderTokenValue('token');

        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn($promotionCoupon);

        $orderRepository->findCartByTokenValue('token')->willReturn($cart);
        $cart->getChannel()->willReturn($firstChannel);

        $cart->setPromotionCoupon($promotionCoupon)->shouldBeCalled();

        $promotionCouponChecker->isEligible($cart, $promotionCoupon)->willReturn(true);

        $promotionCoupon->getPromotion()->willReturn($promotion);
        $promotion->getChannels()->willReturn(new ArrayCollection([$firstChannel->getWrappedObject(), $secondChannel->getWrappedObject()]));

        $promotionChecker->isEligible($cart, $promotion)->willReturn(true);

        $executionContext->buildViolation('sylius.promotion_coupon.is_invalid')->shouldNotBeCalled();

        $this->validate($value, $constraint);
    }

    function it_adds_violation_if_promotion_coupon_is_not_eligible(
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        PromotionCouponEligibilityCheckerInterface $promotionCouponChecker,
        PromotionCouponInterface $promotionCoupon,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $cart,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder
    ): void {
        $this->initialize($executionContext);
        $constraint = new PromotionCouponEligibility();

        $value = new ApplyCouponToCart('couponCode');
        $value->setOrderTokenValue('token');

        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn($promotionCoupon);

        $orderRepository->findCartByTokenValue('token')->willReturn($cart);

        $cart->setPromotionCoupon($promotionCoupon)->shouldBeCalled();

        $promotionCouponChecker->isEligible($cart, $promotionCoupon)->willReturn(false);

        $executionContext->buildViolation('sylius.promotion_coupon.is_invalid')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('couponCode')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($value, $constraint);
    }

    function it_adds_violation_if_promotion_is_not_available_in_cart_channel(
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        PromotionCouponEligibilityCheckerInterface $promotionCouponChecker,
        PromotionEligibilityCheckerInterface $promotionChecker,
        PromotionCouponInterface $promotionCoupon,
        PromotionInterface $promotion,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $cart,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel,
        ChannelInterface $thirdChannel
    ): void {
        $this->initialize($executionContext);
        $constraint = new PromotionCouponEligibility();

        $value = new ApplyCouponToCart('couponCode');
        $value->setOrderTokenValue('token');

        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn($promotionCoupon);
        $promotionCoupon->getPromotion()->willReturn($promotion);
        $promotion->getChannels()->willReturn(new ArrayCollection([$firstChannel->getWrappedObject(), $secondChannel->getWrappedObject()]));

        $orderRepository->findCartByTokenValue('token')->willReturn($cart);
        $cart->getChannel()->willReturn($thirdChannel);

        $cart->setPromotionCoupon($promotionCoupon)->shouldBeCalled();

        $promotionCouponChecker->isEligible($cart, $promotionCoupon)->willReturn(true);

        $promotionChecker->isEligible($cart, $promotion)->willReturn(false);

        $executionContext->buildViolation('sylius.promotion_coupon.is_invalid')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('couponCode')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($value, $constraint);
    }

    function it_adds_violation_if_promotion_is_not_eligible(
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        PromotionCouponEligibilityCheckerInterface $promotionCouponChecker,
        PromotionEligibilityCheckerInterface $promotionChecker,
        PromotionCouponInterface $promotionCoupon,
        PromotionInterface $promotion,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $cart,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder
    ): void {
        $this->initialize($executionContext);
        $constraint = new PromotionCouponEligibility();

        $value = new ApplyCouponToCart('couponCode');
        $value->setOrderTokenValue('token');

        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn($promotionCoupon);

        $orderRepository->findCartByTokenValue('token')->willReturn($cart);

        $cart->setPromotionCoupon($promotionCoupon)->shouldBeCalled();

        $promotionCouponChecker->isEligible($cart, $promotionCoupon)->willReturn(true);

        $promotionCoupon->getPromotion()->willReturn($promotion);
        $promotionChecker->isEligible($cart, $promotion)->willReturn(false);

        $executionContext->buildViolation('sylius.promotion_coupon.is_invalid')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('couponCode')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($value, $constraint);
    }

    function it_does_add_violation_if_promotion_code_does_not_exist(
        PromotionCouponRepositoryInterface $promotionCouponRepository,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $cart,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder
    ): void {
        $this->initialize($executionContext);
        $constraint = new PromotionCouponEligibility();

        $value = new ApplyCouponToCart('couponCode');
        $value->setOrderTokenValue('token');

        $promotionCouponRepository->findOneBy(['code' => 'couponCode'])->willReturn(null);

        $orderRepository->findCartByTokenValue('token')->willReturn($cart);

        $cart->setPromotionCoupon(null)->shouldBeCalled();

        $executionContext->buildViolation('sylius.promotion_coupon.is_invalid')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('couponCode')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($value, $constraint);
    }
}
