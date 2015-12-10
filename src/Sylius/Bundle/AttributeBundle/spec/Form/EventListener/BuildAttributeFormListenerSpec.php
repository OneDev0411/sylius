<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AttributeBundle\Form\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class BuildAttributeFormListenerSpec extends ObjectBehavior
{
    function let(FormFactoryInterface $formFactory)
    {
        $this->beConstructedWith($formFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AttributeBundle\Form\EventListener\BuildAttributeFormListener');
    }

    function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_adds_configuration_field_if_necessary(
        $formFactory,
        AttributeInterface $attribute,
        Form $form,
        Form $configurationForm,
        FormEvent $event
    ) {
        $event->getData()->willReturn($attribute);
        $event->getForm()->willReturn($form);

        $attribute->getType()->willReturn('datetime');

        $formFactory->createNamed(
            'configuration',
            'sylius_attribute_type_configuration_datetime',
            null,
            Argument::type('array')
        )->willReturn($configurationForm);

        $form->add($configurationForm)->shouldBeCalled();

        $this->addConfigurationFields($event);
    }

    function it_does_nothing_if_configuration_form_does_not_exist(
        $formFactory,
        AttributeInterface $attribute,
        Form $form,
        FormEvent $event
    ) {
        $event->getData()->willReturn($attribute);
        $event->getForm()->willReturn($form);

        $attribute->getType()->willReturn('text');

        $formFactory->createNamed(
            'configuration',
            'sylius_attribute_type_configuration_text',
            null,
            Argument::type('array')
        )->willThrow('Symfony\Component\Form\Exception\InvalidArgumentException');

        $form->add(Argument::any())->shouldNotBeCalled();

        $this->addConfigurationFields($event);
    }
}
