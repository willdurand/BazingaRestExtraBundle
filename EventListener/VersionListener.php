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
use Symfony\Component\HttpFoundation\Request;
use Negotiation\FormatNegotiator;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class VersionListener
{
    private $negotiator;

    private $attributeName;

    private $parameterName;

    private $defaultVersion;

    /**
     * @param string $parameterName
     */
    public function __construct(FormatNegotiator $negotiator, $attributeName, $parameterName, $defaultVersion)
    {
        $this->negotiator     = $negotiator;
        $this->attributeName  = $attributeName;
        $this->parameterName  = sprintf('/;\s*%s=(\d+)/', preg_quote($parameterName));
        $this->defaultVersion = $defaultVersion;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request    = $event->getRequest();
        $bestAccept = $this->negotiator->getBest($request->headers->get('Accept'));

        $matches = array();
        if (1 === preg_match($this->parameterName, $bestAccept->getValue(), $matches)) {
            $version = $matches[1];
        } else {
            $version = $this->defaultVersion;
        }

        $request->attributes->set($this->attributeName, $version);
    }
}
