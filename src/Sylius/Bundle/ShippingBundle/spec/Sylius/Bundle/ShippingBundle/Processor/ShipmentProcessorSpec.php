<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShippingBundle\Processor;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ShippingBundle\Model\ShipmentInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ShipmentProcessorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Processor\ShipmentProcessor');
    }

    function it_implements_Sylius_shipment_processor_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Processor\ShipmentProcessorInterface');
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface     $shipment
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentItemInterface $item
     */
    function it_updates_shipment_states($shipment, $item)
    {
        $shipment->getState()->shouldBeCalled()->willReturn(ShipmentInterface::STATE_READY);
        $shipment->setState(ShipmentInterface::STATE_SHIPPED)->shouldBeCalled();
        $shipment->getItems()->shouldBeCalled()->willReturn(array($item));

        $item->getShippingState()->shouldBeCalled()->willReturn(ShipmentInterface::STATE_READY);
        $item->setShippingState(ShipmentInterface::STATE_SHIPPED)->shouldBeCalled();

        $this->updateShipmentStates(array($shipment), ShipmentInterface::STATE_SHIPPED, ShipmentInterface::STATE_READY);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface     $shipment
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentItemInterface $item
     */
    function it_does_not_update_shipment_states_if_state_from_does_not_match($shipment, $item)
    {
        $shipment->getState()->shouldBeCalled()->willReturn(ShipmentInterface::STATE_SHIPPED);
        $shipment->setState(ShipmentInterface::STATE_SHIPPED)->shouldNotBeCalled();
        $shipment->getItems()->shouldBeCalled()->willReturn(array($item));

        $item->getShippingState()->shouldBeCalled()->willReturn(ShipmentInterface::STATE_SHIPPED);
        $item->setShippingState(ShipmentInterface::STATE_SHIPPED)->shouldNotBeCalled();

        $this->updateShipmentStates(array($shipment), ShipmentInterface::STATE_SHIPPED, ShipmentInterface::STATE_READY);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentItemInterface $item
     */
    function it_updates_item_states($item)
    {
        $item->getShippingState()->shouldBeCalled()->willReturn(ShipmentInterface::STATE_READY);
        $item->setShippingState(ShipmentInterface::STATE_SHIPPED)->shouldBeCalled();

        $this->updateItemStates(array($item), ShipmentInterface::STATE_SHIPPED, ShipmentInterface::STATE_READY);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentItemInterface $item
     */
    function it_does_not_update_item_states_if_state_from_does_not_match($item)
    {
        $item->getShippingState()->shouldBeCalled()->willReturn(ShipmentInterface::STATE_SHIPPED);
        $item->setShippingState(ShipmentInterface::STATE_SHIPPED)->shouldNotBeCalled();

        $this->updateItemStates(array($item), ShipmentInterface::STATE_SHIPPED, ShipmentInterface::STATE_READY);
    }
}
