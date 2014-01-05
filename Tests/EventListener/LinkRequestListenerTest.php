<?php

namespace Bazinga\Bundle\RestExtraBundle\Tests\EventListener;

use Bazinga\Bundle\RestExtraBundle\Tests\WebTestCase;

class LinkRequestListenerTest extends WebTestCase
{
    /**
     * @dataProvider dataProviderForTestLink
     */
    public function testLink($uri, $linkUri, $forceScriptName = false)
    {
        $client  = $this->createClient();
        $headers = array(
            'HTTP_LINK'   => sprintf($linkUri, 2),
            'HTTP_ORIGIN' => 'http://localhost',
        );

        if (true === $forceScriptName) {
            $headers['SCRIPT_FILENAME']     = '/app.php';
            $headers['SCRIPT_NAME']         = '/app.php';
            $headers['HTTP_X_ORIGINAL_URL'] = '/app.php' . $uri;
        }

        $client->request('LINK', $uri,
            array(),
            array(),
            $headers
        );
        $request = $client->getRequest();

        $this->assertTrue($request->attributes->has('links'));

        $links = $request->attributes->get('links');
        $this->assertCount(1, $links);

        $link = array_shift($links);
        $object = is_array($link) ? $link[0] : $link;

        $this->assertInstanceOf('Bazinga\Bundle\RestExtraBundle\Tests\Fixtures\Model\Test', $object);
        $this->assertEquals(2, $object->getId());
    }

    public function testLinkSupportsParamConverter()
    {
        $client  = $this->createClient();
        $client->request('LINK', '/tests/1',
            array(),
            array(),
            array('HTTP_LINK' => '</tests/paramconverter/2013-12-10>', 'HTTP_ORIGIN' => 'http://localhost')
        );
        $request = $client->getRequest();

        $this->assertTrue($request->attributes->has('links'));

        $links = $request->attributes->get('links');
        $this->assertCount(1, $links);

        $link = array_shift($links);
        $object = is_array($link) ? $link[0] : $link;

        $this->assertInstanceOf('DateTime', $object);
        $this->assertEquals('2013-12-10', $object->format('Y-m-d'));
    }

    public static function dataProviderForTestLink()
    {
        return array(
            array('/tests/1', '</tests/%d>'),
            array('/tests/1', '<http://localhost/tests/%d>'),
            array('/tests/1', '<http://localhost/app.php/tests/%d>', true),
            array('/tests/1', '</tests/noconventions/%d>'),
            array('/tests/1', '</tests/noconventions/%d>; rel="test"')
        );
    }
}
