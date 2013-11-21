<?php

namespace Bazinga\Bundle\RestExtraBundle\Tests\Fixtures\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Bazinga\Bundle\RestExtraBundle\Annotation\CsrfDoubleSubmit;
use Bazinga\Bundle\RestExtraBundle\Tests\Fixtures\Model\Test;

class TestController
{
    public function getAction($id)
    {
        return array('test' => new Test($id));
    }

    public function getNoConventionAction($id)
    {
        return new Test($id);
    }

    public function linkAction($id)
    {
        return new Response();
    }

    public function allAction()
    {
        return new Response(__METHOD__);
    }

    public function allVersion123Action()
    {
        return new Response(__METHOD__);
    }

    /**
     * @CsrfDoubleSubmit
     */
    public function createAction()
    {
        return new Response(__METHOD__);
    }

    public function createWithoutCsrfDoubleSubmitAction()
    {
        return new Response(__METHOD__);
    }
}
