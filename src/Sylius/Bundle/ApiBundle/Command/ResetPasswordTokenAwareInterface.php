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

namespace Sylius\Bundle\ApiBundle\Command;

interface ResetPasswordTokenAwareInterface extends CommandAwareDataTransformerInterface
{
    public function getResetPasswordToken(): string;

    public function setResetPasswordToken(string $token): void;
}
