<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Security;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class UserPasswordEncoderSpec extends ObjectBehavior
{
    function let(EncoderFactoryInterface $encoderFactory)
    {
        $this->beConstructedWith($encoderFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Security\UserPasswordEncoder');
    }

    function it_implements_password_updater_interface()
    {
        $this->shouldImplement('Sylius\Component\User\Security\UserPasswordEncoderInterface');
    }

    function it_encodes_password(EncoderFactoryInterface $encoderFactory, PasswordEncoderInterface $passwordEncoder, UserInterface $user)
    {
        $user->getPlainPassword()->willReturn('topSecretPlainPassword');
        $user->getSalt()->willReturn('typicalSalt');
        $encoderFactory->getEncoder($user)->willReturn($passwordEncoder);
        $passwordEncoder->encodePassword('topSecretPlainPassword', 'typicalSalt')->willReturn('topSecretEncodedPassword');

        $this->encode($user)->shouldReturn('topSecretEncodedPassword');
    }
}
