<?php

/**
 * This file is part of the RestExtraBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\RestExtraBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class CsrfDoubleSubmitListener
{
    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @var string
     */
    private $cookieName;

    /**
     * @var string
     */
    private $parameterName;

    /**
     * @param Reader $annotationReader
     * @param string $cookieName
     * @param string $parameterName
     */
    public function __construct(Reader $annotationReader, $cookieName, $parameterName)
    {
        $this->annotationReader = $annotationReader;
        $this->cookieName       = $cookieName;
        $this->parameterName    = $parameterName;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        $request    = $event->getRequest();

        // these HTTP methods do not require CSRF protection
        if (in_array($request->getMethod(), array('GET', 'HEAD', 'OPTIONS', 'TRACE'))) {
            return;
        }

        // does not apply on closures
        if (!is_array($controller)) {
            return;
        }

        $object = new \ReflectionObject($controller[0]);
        $method = $object->getMethod($controller[1]);

        if (false === $this->isProtectedByCsrfDoubleSubmit($object, $method)) {
            return;
        }

        $cookieValue = $request->cookies->get($this->cookieName);
        $paramValue  = $request->request->get($this->parameterName);

        if (empty($cookieValue)) {
            throw new HttpException(400, 'Cookie not found or invalid.');
        }

        if (empty($paramValue)) {
            throw new HttpException(400, 'Request parameter not found or invalid.');
        }

        if (0 !== strcmp($cookieValue, $paramValue)) {
            throw new HttpException(400, 'CSRF values mismatch.');
        }

        $request->cookies->remove($this->cookieName);
        $request->request->remove($this->parameterName);
    }

    /**
     * @return boolean
     */
    private function isProtectedByCsrfDoubleSubmit(\ReflectionClass $class, \ReflectionMethod $method)
    {
        $annotation = $this->annotationReader->getClassAnnotation(
            $class,
            'Bazinga\Bundle\RestExtraBundle\Annotation\CsrfDoubleSubmit'
        );

        if (null !== $annotation) {
            return true;
        }

        $annotation = $this->annotationReader->getMethodAnnotation(
            $method,
            'Bazinga\Bundle\RestExtraBundle\Annotation\CsrfDoubleSubmit'
        );

        return null !== $annotation;
    }
}
