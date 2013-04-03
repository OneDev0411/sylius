<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShippingBundle\Checker;

use PHPSpec2\ObjectBehavior;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ItemCountRuleCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Checker\ItemCountRuleChecker');
    }

    function it_is_Sylius_rule_checker()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Checker\RuleCheckerInterface');
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface $subject
     * @param Countable                                                   $countable
     */
    function it_should_recognize_empty_subject_as_not_eligible($subject, $countable)
    {
        $countable->count()->shouldBeCalled()->willReturn(0);
        $subject->getShippables()->shouldBeCalled()->willReturn($countable);

        $this->isEligible($subject, array('count' => 10, 'equal' => false))->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface $subject
     * @param Countable                                                   $countable
     */
    function it_should_recognize_subject_as_not_eligible_if_item_count_is_less_then_configured($subject, $countable)
    {
        $countable->count()->shouldBeCalled()->willReturn(7);
        $subject->getShippables()->shouldBeCalled()->willReturn($countable);

        $this->isEligible($subject, array('count' => 10, 'equal' => false))->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface $subject
     * @param Countable                                                   $countable
     */
    function it_should_recognize_subject_as_eligible_if_item_count_is_greater_then_configured($subject, $countable)
    {
        $countable->count()->shouldBeCalled()->willReturn(12);
        $subject->getShippables()->shouldBeCalled()->willReturn($countable);

        $this->isEligible($subject, array('count' => 10, 'equal' => false))->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface $subject
     * @param Countable                                                   $countable
     */
    function it_should_recognize_subject_as_eligible_if_item_count_is_equal_with_configured_depending_on_equal_setting($subject, $countable)
    {
        $countable->count()->shouldBeCalled()->willReturn(10);
        $subject->getShippables()->shouldBeCalled()->willReturn($countable);

        $this->isEligible($subject, array('count' => 10, 'equal' => false))->shouldReturn(false);
        $this->isEligible($subject, array('count' => 10, 'equal' => true))->shouldReturn(true);
    }

    function it_returns_item_count_configuration_form_type()
    {
        $this->getConfigurationFormType()->shouldReturn('sylius_shipping_rule_item_count_configuration');
    }
}
