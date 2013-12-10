<?php

namespace Bazinga\Bundle\RestExtraBundle\Tests\Fixtures\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Bazinga\Bundle\RestExtraBundle\Annotation\CsrfDoubleSubmit;
use Bazinga\Bundle\RestExtraBundle\Tests\Fixtures\Model\Test;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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

    /**
     * @ParamConverter("date", options={"format": "Y-m-d"})
     */
    public function getParamConverterAction(\DateTime $date)
    {
        return array('date' => $date);
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
