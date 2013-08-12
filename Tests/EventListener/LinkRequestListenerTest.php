<?php

namespace Bazinga\Bundle\RestExtraBundle\Tests\EventListener;

use Bazinga\Bundle\RestExtraBundle\Tests\WebTestCase;

class LinkRequestListenerTest extends WebTestCase
{
    const GET_ROUTE_PATTERN = '</tests/%d>';

    public function testLink()
    {
        $client  = $this->createClient();
        $crawler = $client->request('LINK', '/tests/1',
            array(),
            array(),
            array('HTTP_LINK' => sprintf(self::GET_ROUTE_PATTERN, 2))
        );
        $request = $client->getRequest();

        $this->assertTrue($request->attributes->has('links'));

        $links = $request->attributes->get('links');
        $this->assertCount(1, $links);

        $this->assertInstanceOf('Bazinga\Bundle\RestExtraBundle\Tests\Fixtures\Model\Test', $links[0]);
        $this->assertEquals(2, $links[0]->getId());
    }
}
