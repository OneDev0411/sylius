<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;

class OrderUserListener
{
    protected $securityContext;

    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function setOrderUser(GenericEvent $event)
    {
        if (null === $user = $this->getUser()) {
            return;
        }

        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new \InvalidArgumentException(
                'Order user listener requires event subject to be instance of "Sylius\Bundle\CoreBundle\Model\OrderInterface"'
            );
        }

        $order->setUser($user);
    }

    protected function getUser()
    {
        if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->securityContext->getToken()->getUser();
        }
    }
}
