<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ArchetypeBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

class ArchetypeTranslationTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ArchetypeTranslation', ['sylius'], 'subject');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ArchetypeBundle\Form\Type\ArchetypeTranslationType');
    }

    function it_is_a_form()
    {
        $this->shouldHaveType(AbstractResourceType::class);
    }

    function it_buils_a_form(FormBuilderInterface $builder)
    {
        $builder->add('name', 'text', Argument::type('array'))->shouldBeCalled();

        $this->buildForm($builder, []);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_subject_archetype_translation');
    }
}
