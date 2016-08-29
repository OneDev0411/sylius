<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Handler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Handler\LocaleChangeHandler;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Exception\HandleException;
use Sylius\Component\Core\Locale\Handler\RequestBasedHandlerInterface;
use Sylius\Component\Core\Locale\LocaleStorageInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\SyliusLocaleEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;

/**
 * @mixin LocaleChangeHandler
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class LocaleChangeHandlerSpec extends ObjectBehavior
{
    function let(
        LocaleStorageInterface $localeStorage,
        ChannelContextInterface $channelContext,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith($localeStorage, $channelContext, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(LocaleChangeHandler::class);
    }

    function it_is_a_request_based_handler()
    {
        $this->shouldImplement(RequestBasedHandlerInterface::class);
    }

    function it_handles_locale_change(
        LocaleStorageInterface $localeStorage,
        ChannelContextInterface $channelContext,
        EventDispatcherInterface $eventDispatcher,
        Channel $channel
    ) {
        $request = new Request(['code' => 'en_GB']);
        $channelContext->getChannel()->willReturn($channel);
        $localeStorage->set($channel, 'en_GB')->shouldBeCalled();
        $eventDispatcher->dispatch(SyliusLocaleEvents::CODE_CHANGED, new GenericEvent($request))->shouldBeCalled();

        $this->handle($request);
    }

    function it_throws_handle_exception_if_cannot_handle_request(
        LocaleStorageInterface $localeStorage,
        ChannelContextInterface $channelContext,
        EventDispatcherInterface $eventDispatcher
    ) {
        $request = new Request();
        $channelContext->getChannel()->shouldNotBeCalled();
        $localeStorage->set(Argument::any(), Argument::any())->shouldNotBeCalled();
        $eventDispatcher->dispatch(SyliusLocaleEvents::CODE_CHANGED, new GenericEvent($request))->shouldNotBeCalled();

        $this->shouldThrow(HandleException::class)->during('handle', [$request]);
    }

    function it_throws_handle_exception_if_channel_was_not_found(
        LocaleStorageInterface $localeStorage,
        ChannelContextInterface $channelContext,
        EventDispatcherInterface $eventDispatcher
    ) {
        $request = new Request(['code' => 'en_GB']);
        $channelContext->getChannel()->willThrow(ChannelNotFoundException::class);
        $localeStorage->set(Argument::any(), Argument::any())->shouldNotBeCalled();
        $eventDispatcher->dispatch(SyliusLocaleEvents::CODE_CHANGED, new GenericEvent($request))->shouldNotBeCalled();

        $this->shouldThrow(HandleException::class)->during('handle', [$request]);
    }
}
