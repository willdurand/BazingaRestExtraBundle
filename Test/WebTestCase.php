<?php

/**
 * This file is part of the RestExtraBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\RestExtraBundle\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
abstract class WebTestCase extends BaseWebTestCase
{
    use JsonTestTrait;
}
