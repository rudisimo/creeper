<?php

/*
 * This file is part of the Creeper package.
 *
 * (c) Rodolfo Puig <rpuig@7gstudios.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Creeper\Bundle\Entity;

use Creeper\Bundle\Entity\Base\BaseEntity;

class OpenGraph extends BaseEntity {
    /**
     * @var string $url
     */
    public $url;

    /**
     * @var string $url
     */
    public $title;

    /**
     * @var string $url
     */
    public $image;
}