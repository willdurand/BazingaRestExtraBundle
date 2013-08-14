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
    protected function assertJsonResponse($response, $statusCode = 200)
    {
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );
        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            $response->headers
        );
    }

    protected function jsonRequest($verb, $endpoint, array $data = array())
    {
        $data = empty($data) ? null : json_encode($data);

        return $this->client->request($verb, $endpoint,
            array(),
            array(),
            array(
                'HTTP_ACCEPT'  => 'application/json',
                'CONTENT_TYPE' => 'application/json'
            ),
            $data
        );
    }
}
