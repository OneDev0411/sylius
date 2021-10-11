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

namespace Sylius\Component\Promotion\Provider;

final class Calendar implements DateTimeProviderInterface
{
    public function now(): \DateTimeInterface
    {
        return new \DateTimeImmutable();
    }
}
