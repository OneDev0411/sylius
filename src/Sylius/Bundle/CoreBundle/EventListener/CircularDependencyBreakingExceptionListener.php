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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;

/**
 * For Symfony 4.
 *
 * `Symfony\Component\HttpKernel\EventListener\ExceptionListener::onKernelException` happens to set previous
 * exception for a wrapper exception. This is meant to improve DX while debugging nested exceptions,
 * but also creates some issues.
 *
 * After upgrading to ResourceBundle v1.7 and GridBundle v1.8, the test suite started to fail
 * because of a timeout. By artifically setting the previous exception, in some cases it created
 * an exception with circular dependencies, so that:
 *
 *   `$exception->getPrevious()->getPrevious()->getPrevious() === $exception`
 *
 * That exception is rethrown and other listeners like `Symfony\Component\Security\Http\Firewall\ExceptionListener`
 * try to deal with an exception and all their previous ones, causing infinite loops.
 *
 * This fix only works if "framework.templating" setting DOES NOT include "twig". Otherwise, TwigBundle
 * registers deprecated `Symfony\Component\HttpKernel\EventListener\ExceptionListener`, removes the non-deprecated
 * "exception_listener" service, so that the issue still persists.
 *
 * This listener behaves as a decorator, but also extends the original ExceptionListener, because of yet another
 * listener `ApiPlatform\Core\EventListener\ExceptionListener` requires the original class.
 *
 * @internal
 *
 * @see \Symfony\Component\HttpKernel\EventListener\ExceptionListener
 *
 * @psalm-suppress UndefinedClass
 * @psalm-suppress MissingDependency
 */
final class CircularDependencyBreakingExceptionListener extends ExceptionListener
{
    /** @var ExceptionListener */
    private $decoratedListener;

    public function __construct(ExceptionListener $decoratedListener)
    {
        $this->decoratedListener = $decoratedListener;
    }

    public function logKernelException(GetResponseForExceptionEvent $event): void
    {
        $this->decoratedListener->logKernelException($event);
    }

    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        try {
            $this->decoratedListener->onKernelException($event);
        } catch (\Throwable $throwable) {
            $this->breakCircularDependency($throwable);

            throw $throwable;
        }
    }

    private function breakCircularDependency(\Throwable $throwable): void
    {
        $throwables = [];

        do {
            $throwables[] = $throwable;

            if (in_array($throwable->getPrevious(), $throwables, true)) {
                $this->removePreviousFromThrowable($throwable);
            }

            $throwable = $throwable->getPrevious();
        } while (null !== $throwable);
    }

    private function removePreviousFromThrowable(\Throwable $throwable): void
    {
        $previous = new \ReflectionProperty($throwable instanceof \Exception ? \Exception::class : \Error::class, 'previous');
        $previous->setAccessible(true);
        $previous->setValue($throwable, null);
    }
}
