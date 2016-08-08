<?php

namespace Bazinga\Bundle\RestExtraBundle\Tests\Functional;

// Get the autoload file
require_once __DIR__ . '/../../../vendor/autoload.php';

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * App Test Kernel for functional tests.
 */
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \Bazinga\Bundle\RestExtraBundle\BazingaRestExtraBundle(),
            new \Bazinga\Bundle\RestExtraBundle\Tests\Fixtures\BazingaRestExtraTestBundle(),
        );
    }

    public function init()
    {
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir() . '/' . Kernel::VERSION . '/bazinga-extra-rest/cache/' . $this->environment;
    }

    public function getLogDir()
    {
        return sys_get_temp_dir() . '/' . Kernel::VERSION . '/bazinga-extra-rest/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/symfony-'.self::getRoutingVersion(). '/' . $this->environment . '.yml');
    }

    private static function getRoutingVersion()
    {
        $installedPackages = json_decode(file_get_contents(__DIR__.'/../../../vendor/composer/installed.json'));
        foreach($installedPackages as $package) {
            if($package->name === 'symfony/routing')
                return (int)($package->version_normalized);
        }
        return 2;
    }

    public function serialize()
    {
        return serialize(array($this->getEnvironment(), $this->isDebug()));
    }

    public function unserialize($str)
    {
        call_user_func_array(array($this, '__construct'), unserialize($str));
    }
}
