<?php

if (!($loader = @include __DIR__ . '/../vendor/autoload.php')) {
    die(<<<EOT
You need to install the project dependencies using Composer:
$ wget http://getcomposer.org/composer.phar
OR
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar install --dev
$ phpunit
EOT
);
}

use Doctrine\Common\Annotations\AnnotationRegistry;

AnnotationRegistry::registerLoader(function ($class) {
    if (strpos($class, 'Bazinga\Bundle\RestExtraBundle\Annotation\\') === 0) {
        $path = __DIR__.'/../'.str_replace('\\', '/', substr($class, strlen('Bazinga\Bundle\RestExtraBundle\\')))   .'.php';

        require_once $path;
    }

    return class_exists($class, false);
});

AnnotationRegistry::registerLoader(function ($class) {
    if ($class == 'Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter') {
        $path = __DIR__.'/../vendor/sensio/framework-extra-bundle/Sensio/Bundle/FrameworkExtraBundle/Configuration/ParamConverter.php';

        require_once $path;
    }

    return class_exists($class, false);
});
