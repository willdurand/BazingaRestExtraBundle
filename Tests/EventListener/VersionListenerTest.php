<?php

namespace Bazinga\Bundle\RestExtraBundle\Tests\EventListener;

use Bazinga\Bundle\RestExtraBundle\Tests\WebTestCase;

class VersionListenerTest extends WebTestCase
{
    public function testVersionIsSetWithDefaultValue()
    {
        $client  = $this->createClient();
        $crawler = $client->request('GET', '/tests');

        $request  = $client->getRequest();
        $response = $client->getResponse();

        $this->assertTrue($request->attributes->has('_api_version'));
        $this->assertEquals(1, $request->attributes->get('_api_version'));

        $this->assertEquals('Bazinga\Bundle\RestExtraBundle\Tests\Fixtures\Controller\TestController::allAction', $response->getContent());
    }

    public function testVersionIsExtractedFromAcceptHeader()
    {
        $this->markTestSkipped('Fix it!');

        $client  = $this->createClient();
        $crawler = $client->request('GET', '/tests',
            array(),
            array(),
            array('HTTP_ACCEPT' => 'text/html;v=123')
        );

        $request  = $client->getRequest();
        $response = $client->getResponse();

        $this->assertTrue($request->attributes->has('_api_version'));
        $this->assertEquals(123, $request->attributes->get('_api_version'));

        $this->assertEquals('Bazinga\Bundle\RestExtraBundle\Tests\Fixtures\Controller\TestController::allVersion123Action', $response->getContent());
    }
}
