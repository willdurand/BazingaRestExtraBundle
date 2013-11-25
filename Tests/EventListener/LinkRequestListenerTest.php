<?php

namespace Bazinga\Bundle\RestExtraBundle\Tests\EventListener;

use Bazinga\Bundle\RestExtraBundle\Tests\WebTestCase;

class LinkRequestListenerTest extends WebTestCase
{
    /**
     * @dataProvider linkDataProvider
     */
    public function testLink($uri, $linkUri)
    {
        $client  = $this->createClient();
        $crawler = $client->request('LINK', $uri,
            array(),
            array(),
            array('HTTP_LINK' => sprintf($linkUri, 2), 'HTTP_ORIGIN' => 'http://localhost')
        );
        $request = $client->getRequest();

        $this->assertTrue($request->attributes->has('links'));

        $links = $request->attributes->get('links');
        $this->assertCount(1, $links);

        $this->assertInstanceOf('Bazinga\Bundle\RestExtraBundle\Tests\Fixtures\Model\Test', $links[0]);
        $this->assertEquals(2, $links[0]->getId());
    }

    public function linkDataProvider()
    {
        return array(
          array('/tests/1', '</tests/%d>'),
          array('/tests/1', '<http://localhost/tests/%d>'),
          array('/tests/1', '</tests/noconventions/%d>')
        );
    }
}
