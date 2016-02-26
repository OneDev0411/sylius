<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Channel\ChannelIndexPage;
use Sylius\Behat\Page\Channel\ChannelUpdatePage;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ChannelIndexPage
     */
    private $channelIndexPage;

    /**
     * @var ChannelUpdatePage
     */
    private $channelUpdatePage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ChannelIndexPage $channelIndexPage
     * @param ChannelUpdatePage $channelUpdatePage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ChannelIndexPage $channelIndexPage,
        ChannelUpdatePage $channelUpdatePage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->channelIndexPage = $channelIndexPage;
        $this->channelUpdatePage = $channelUpdatePage;
    }

    /**
     * @When I set :channel channel theme to :theme
     */
    public function iSetChannelThemeTo(ChannelInterface $channel, ThemeInterface $theme)
    {
        $this->channelUpdatePage->open(['id' => $channel->getId()]);
        $this->channelUpdatePage->setTheme($theme);
        $this->channelUpdatePage->update();

        $this->sharedStorage->set('channel', $channel);
        $this->sharedStorage->set('theme', $theme);
    }

    /**
     * @When /^I unset theme on (that channel)$/
     */
    public function iUnsetThemeOnChannel(ChannelInterface $channel)
    {
        $this->channelUpdatePage->open(['id' => $channel->getId()]);
        $this->channelUpdatePage->unsetTheme();
        $this->channelUpdatePage->update();
    }

    /**
     * @Then /^(that channel) should not use any theme$/
     */
    public function channelShouldNotUseAnyTheme(ChannelInterface $channel)
    {
        $this->channelIndexPage->open();

        expect($this->channelIndexPage->getUsedThemeName($channel->getCode()))->toBe('');
    }

    /**
     * @Then /^(that channel) should use (that theme)$/
     */
    public function channelShouldUseTheme(ChannelInterface $channel, ThemeInterface $theme)
    {
        $this->channelIndexPage->open();

        expect($this->channelIndexPage->getUsedThemeName($channel->getCode()))->toBe($theme->getName());
    }
}
