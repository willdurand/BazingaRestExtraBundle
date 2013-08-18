<?php

/**
 * This file is part of the RestExtraBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\RestExtraBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class CsrfDoubleSubmitListener
{
    /**
     * @var string
     */
    private $cookieName;

    /**
     * @var string
     */
    private $parameterName;

    /**
     * @param string $cookieName
     * @param string $parameterName
     */
    public function __construct($cookieName, $parameterName)
    {
        $this->cookieName    = $cookieName;
        $this->parameterName = $parameterName;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        if (!in_array($request->getMethod(), array('POST', 'PUT'))) {
            return;
        }

        if (null === $cookieValue = $request->cookies->get($this->cookieName)) {
            throw new BadRequestHttpException('Cookie not found.');
        }

        if (null === $paramValue = $request->request->get($this->parameterName)) {
            throw new BadRequestHttpException('Request parameter not found.');
        }

        if (0 !== strcmp($cookieValue, $paramValue)) {
            throw new BadRequestHttpException('CSRF values mismatch.');
        }
    }
}
