<?php

/**
 * This file is part of the RestExtraBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Bazinga\Bundle\RestExtraBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class BazingaRestExtraExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (!empty($config['link_request_listener'])) {
            $loader->load('link_request_listener.xml');
        }

        if (!empty($config['csrf_double_submit_listener']) && true === $config['csrf_double_submit_listener']['enabled']) {
            $loader->load('csrf_double_submit_listener.xml');

            $container->getDefinition('bazinga_rest_extra.event_listener.csrf_double_submit')
                ->replaceArgument(1, $config['csrf_double_submit_listener']['cookie_name'])
                ->replaceArgument(2, $config['csrf_double_submit_listener']['parameter_name'])
                ;
        }
    }
}
