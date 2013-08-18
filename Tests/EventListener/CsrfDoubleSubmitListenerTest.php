<?php

namespace Bazinga\Bundle\RestExtraBundle\Tests\EventListener;

use Bazinga\Bundle\RestExtraBundle\Tests\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

class CsrfDoubleSubmitListenerTest extends WebTestCase
{
    public function testCsrfDoubleSubmit()
    {
        $csrfValue = 'Sup3r$ecr3t';

        $client = $this->createClient();
        $client->getCookieJar()->set(new Cookie('csrf_cookie', $csrfValue));

        $crawler = $client->request('POST', '/tests', array(
            '_csrf_token' => $csrfValue,
        ));

        $request  = $client->getRequest();
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            'Bazinga\Bundle\RestExtraBundle\Tests\Fixtures\Controller\TestController::createAction',
            $response->getContent()
        );
    }

    /**
     * @expectedException        Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Cookie not found.
     */
    public function testCsrfDoubleSubmitFailsIfNoCookieFound()
    {
        $client  = $this->createClient();
        $crawler = $client->request('POST', '/tests', array(
            '_csrf_token' => '',
        ));
    }

    /**
     * @expectedException        Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Request parameter not found.
     */
    public function testCsrfDoubleSubmitFailsIfNoRequestParameterFound()
    {
        $client = $this->createClient();
        $client->getCookieJar()->set(new Cookie('csrf_cookie', 'a token'));

        $crawler = $client->request('POST', '/tests');
    }

    /**
     * @expectedException        Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage CSRF values mismatch.
     */
    public function testCsrfDoubleSubmitFailsIfValuesMismatch()
    {
        $client = $this->createClient();
        $client->getCookieJar()->set(new Cookie('csrf_cookie', 'a token'));

        $crawler = $client->request('POST', '/tests', array(
            '_csrf_token' => 'another token',
        ));
    }
}
