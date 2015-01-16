<?php

namespace Bazinga\Bundle\RestExtraBundle\Tests\EventListener;

use Bazinga\Bundle\RestExtraBundle\Tests\WebTestCase;

class PatchRequestListenerTest extends WebTestCase
{
    /**
     * @expectedException        Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Invalid content received
     */
    public function testEmptyContent()
    {
        $client  = $this->createClient();
        $client->request(
            'PATCH',
            '/tests/1',
            array(),
            array(),
            array(
                'HTTP_ACCEPT'  => 'application/json',
                'CONTENT_TYPE' => 'application/json'
            )
        );
    }

    /**
     * @expectedException        Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Invalid patch request received
     */
    public function testBadPatch()
    {
        $client  = $this->createClient();
        $patches = array(
            array(
                'op' => 'replace',
                'path' => '/language',
                'value' => 'fr',
            ),
            array(
                'op' => 'remove',
                'value' => '/email',
            ),
        );
        $client->request(
            'PATCH',
            '/tests/1',
            array(),
            array(),
            array(
                'HTTP_ACCEPT'  => 'application/json',
                'CONTENT_TYPE' => 'application/json'
            ),
            json_encode($patches)
        );
    }

    public function testPatch()
    {
        $client  = $this->createClient();
        $patches = array(
            array(
                'op' => 'replace',
                'path' => '/email',
                'value' => 'newEmail@email.com',
            ),
            array(
                'op' => 'replace',
                'path' => '/language',
                'value' => 'fr',
            ),
        );
        $client->request(
            'PATCH',
            '/tests/1',
            array(),
            array(),
            array(
                'HTTP_ACCEPT'  => 'application/json',
                'CONTENT_TYPE' => 'application/json'
            ),
            json_encode($patches)
        );

        $request = $client->getRequest();

        $this->assertTrue($request->attributes->has('patches'));

        $patches = $request->attributes->get('patches');
        $this->assertCount(2, $patches);

        $patch = array_shift($patches);

        $this->assertInstanceOf('Bazinga\Bundle\RestExtraBundle\Model\Patch', $patch);
        $this->assertEquals('newEmail@email.com', $patch->getValue());
    }
}
