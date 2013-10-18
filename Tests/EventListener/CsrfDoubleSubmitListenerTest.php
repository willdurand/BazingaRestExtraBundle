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
        $this->assertCount(0, $response->headers->getCookies());
    }

    /**
     * @expectedException        Symfony\Component\HttpKernel\Exception\HttpException
     * @expectedExceptionMessage Cookie not found or invalid.
     */
    public function testCsrfDoubleSubmitFailsIfNoCookieFound()
    {
        $client  = $this->createClient();
        $crawler = $client->request('POST', '/tests', array(
            '_csrf_token' => '',
        ));
    }

    /**
     * @expectedException        Symfony\Component\HttpKernel\Exception\HttpException
     * @expectedExceptionMessage Cookie not found or invalid.
     *
     * @dataProvider dataProviderWithInvalidData
     */
    public function testCsrfDoubleSubmitFailsIfInvalidCookieValue($cookieValue)
    {
        $client = $this->createClient();
        $client->getCookieJar()->set(new Cookie('csrf_cookie', $cookieValue));

        $crawler = $client->request('POST', '/tests');
    }

    /**
     * @expectedException        Symfony\Component\HttpKernel\Exception\HttpException
     * @expectedExceptionMessage Request parameter not found or invalid.
     */
    public function testCsrfDoubleSubmitFailsIfNoRequestParameterFound()
    {
        $client = $this->createClient();
        $client->getCookieJar()->set(new Cookie('csrf_cookie', 'a token'));

        $crawler = $client->request('POST', '/tests');
    }

    /**
     * @expectedException        Symfony\Component\HttpKernel\Exception\HttpException
     * @expectedExceptionMessage Request parameter not found or invalid.
     *
     * @dataProvider dataProviderWithInvalidData
     */
    public function testCsrfDoubleSubmitFailsIfInvalidRequestParamValue($paramValue)
    {
        $client = $this->createClient();
        $client->getCookieJar()->set(new Cookie('csrf_cookie', 'a token'));

        $crawler = $client->request('POST', '/tests', array(
            '_csrf_token' => $paramValue,
        ));
    }

    /**
     * @expectedException        Symfony\Component\HttpKernel\Exception\HttpException
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

    public function testCsrfDoubleSubmitWithoutAnnotationIsInactive()
    {
        $client   = $this->createClient();
        $crawler  = $client->request('POST', '/without-csrf-double-submit');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            'Bazinga\Bundle\RestExtraBundle\Tests\Fixtures\Controller\TestController::createWithoutCsrfDoubleSubmitAction',
            $response->getContent()
        );
    }

    public function testCsrfDoubleSubmitClass()
    {
        $csrfValue = 'Sup3r$ecr3t';

        $client = $this->createClient();
        $client->getCookieJar()->set(new Cookie('csrf_cookie', $csrfValue));

        $crawler = $client->request('POST', '/tests-csrf-class', array(
            '_csrf_token' => $csrfValue,
        ));

        $request  = $client->getRequest();
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            'Bazinga\Bundle\RestExtraBundle\Tests\Fixtures\Controller\TestCsrfController::createAction',
            $response->getContent()
        );
        $this->assertCount(0, $response->headers->getCookies());
    }

    /**
     * @expectedException        Symfony\Component\HttpKernel\Exception\HttpException
     * @expectedExceptionMessage Cookie not found or invalid.
     */
    public function testCsrfDoubleSubmitClassFailsIfNoCookieFound()
    {
        $client  = $this->createClient();
        $crawler = $client->request('POST', '/tests-csrf-class', array(
            '_csrf_token' => '',
        ));
    }

    public function testCsrfDoubleSubmitClassGETMethod()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/tests-csrf-class');

        $request  = $client->getRequest();
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            'Bazinga\Bundle\RestExtraBundle\Tests\Fixtures\Controller\TestCsrfController::getAction',
            $response->getContent()
        );
    }

    public static function dataProviderWithInvalidData()
    {
        return array(
            array(null),
            array(false),
            array(''),
        );
    }
}
