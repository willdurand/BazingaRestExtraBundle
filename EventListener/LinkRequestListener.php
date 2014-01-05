<?php

/**
 * This file is part of the RestExtraBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\RestExtraBundle\EventListener;

use Bazinga\Bundle\RestExtraBundle\Model\LinkHeader;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
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
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

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

        // The controller resolver needs a request to resolve the controller.
        $stubRequest = new Request();

        foreach ($links as $idx => $link) {
            $linkHeader = $this->parseLinkHeader($link);
            $resource   = $this->parseResource($linkHeader, $event->getRequest());

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

            // Make sure @ParamConverter and some other annotations are called
            $subEvent = new FilterControllerEvent($event->getKernel(), $controller, $stubRequest, HttpKernelInterface::SUB_REQUEST);
            $event->getDispatcher()->dispatch(KernelEvents::CONTROLLER, $subEvent);
            $controller = $subEvent->getController();

            $arguments = $this->resolver->getArguments($stubRequest, $controller);

            try {
                $result = call_user_func_array($controller, $arguments);

                $value = is_array($result) ? current($result) : $result;

                if ($linkHeader->hasRel()) {
                    unset($links[$idx]);
                    $links[$linkHeader->getRel()][] = $value;
                } else {
                    $links[$idx] = $value;
                }

            } catch (\Exception $e) {
                continue;
            }
        }

        $event->getRequest()->attributes->set('links', $links);
        $this->urlMatcher->getContext()->setMethod($requestMethod);
    }

    /**
     * @param string $link
     *
     * @return LinkHeader
     */
    protected function parseLinkHeader($link)
    {
        $linkParams = explode(';', trim($link));

        $url = array_shift($linkParams);
        $url = preg_replace('/<|>/', '', $url);

        $rel = empty($linkParams) ? '' : preg_replace("/rel=\"(.*)\"/", "$1", trim($linkParams[0]));

        return new LinkHeader($url, $rel);
    }

    /**
     * @param LinkHeader $linkHeader
     * @param Request    $request
     *
     * @return string
     */
    private function parseResource($linkHeader, $request)
    {
        // Link needs to be cleaned from 'http://host/basepath' when added
        $httpSchemaAndBasePath = $request->getSchemeAndHttpHost() . $request->getBaseUrl();

        return str_replace($httpSchemaAndBasePath, '', $linkHeader->getUrl());
    }
}
