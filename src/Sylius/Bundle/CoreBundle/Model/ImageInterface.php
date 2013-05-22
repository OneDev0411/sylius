<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Model;

use SplFileInfo;
use DateTime;

interface ImageInterface
{
    public function getId();
    public function getCreatedAt();
    public function setCreatedAt(DateTime $createdAt);
    public function getUpdatedAt();
    public function setUpdatedAt(DateTime $updatedAt);
}
