<?php

namespace Bazinga\Bundle\RestExtraBundle\Tests\Fixtures\Controller;

use Symfony\Component\HttpFoundation\Response;
use Bazinga\Bundle\RestExtraBundle\Annotation\CsrfDoubleSubmit;

/**
 * @CsrfDoubleSubmit
 */
class TestCsrfController
{
    public function createAction()
    {
        return new Response(__METHOD__);
    }

    public function getAction()
    {
        return new Response(__METHOD__);
    }
}
