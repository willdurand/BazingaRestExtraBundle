<?php

namespace Bazinga\Bundle\RestExtraBundle\Tests\Fixtures\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Bazinga\Bundle\RestExtraBundle\Tests\Fixtures\Model\Test;

class TestController
{
    public function getAction($id)
    {
        return array('test' => new Test($id));
    }

    public function linkAction($id)
    {
        return new Response();
    }
}
