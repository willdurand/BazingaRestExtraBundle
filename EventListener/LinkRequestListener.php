<?php

/**
 * This file is part of the RestExtraBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\RestExtraBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author William Durand <william.durand1@gmail.com>
 * @author Samuel Gordalina <samuel.gordalina@gmail.com>
 */
class LinkRequestListener
{
    /**
     * @var ControllerResolverInterface
     */
    private $resolver;

    /**
     * @var UrlMatcherInterface
     */
    private $urlMatcher;

    /**
     * @param ControllerResolverInterface $controllerResolver The 'controller_resolver' service
     * @param UrlMatcherInterface         $urlMatcher         The 'router' service
     */
    public function __construct(ControllerResolverInterface $controllerResolver, UrlMatcherInterface $urlMatcher)
    {
        $this->resolver   = $controllerResolver;
        $this->urlMatcher = $urlMatcher;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->getRequest()->headers->has('link')) {
            return;
        }

        $links  = array();
        $header = $event->getRequest()->headers->get('link');

        /*
         * Due to limitations, multiple same-name headers are sent as comma
         * separated values.
         *
         * This breaks those headers into Link headers following the format
         * http://tools.ietf.org/html/rfc2068#section-19.6.2.4
         */
        while (preg_match('/^((?:[^"]|"[^"]*")*?),/', $header, $matches)) {
            $header  = trim(substr($header, strlen($matches[0])));
            $links[] = $matches[1];
        }

        if ($header) {
            $links[] = $header;
        }

        $requestMethod = $this->urlMatcher->getContext()->getMethod();
        // Force the GET method to avoid the use of the
        // previous method (LINK/UNLINK)
        $this->urlMatcher->getContext()->setMethod('GET');

        // Link needs to cleaned from HTTP_ORIGIN/BasePath when added
        $httpOriginAndBasePath = $event->getRequest()->getSchemeAndHttpHost() . $event->getRequest()->getBasePath();

        // The controller resolver needs a request to resolve the controller.
        $stubRequest = new Request();

        foreach ($links as $idx => $link) {
            $linkParams = explode(';', trim($link));
            $resource   = array_shift($linkParams);
            $resource   = preg_replace('/<|>/', '', $resource);
            $resource   = str_replace($httpOriginAndBasePath, '', $resource);

            try {
                $route = $this->urlMatcher->match($resource);
            } catch (\Exception $e) {
                // If we don't have a matching route we return
                // the original Link header
                continue;
            }

            $stubRequest->attributes->replace($route);

            if (false === $controller = $this->resolver->getController($stubRequest)) {
                continue;
            }

            $arguments = $this->resolver->getArguments($stubRequest, $controller);

            try {
                $result = call_user_func_array($controller, $arguments);

                $links[$idx] = is_array($result) ? current($result) : $result;
            } catch (\Exception $e) {
                continue;
            }
        }

        $event->getRequest()->attributes->set('links', $links);
        $this->urlMatcher->getContext()->setMethod($requestMethod);
    }
}
