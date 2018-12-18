<?php

namespace Bazinga\Bundle\RestExtraBundle\Tests\Fixtures\Controller;

use Symfony\Component\HttpFoundation\Response;

class TestInvokeController
{
    public function __invoke()
    {
        return new Response(__METHOD__);
    }
}
