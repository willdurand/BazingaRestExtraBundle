BazingaRestExtraBundle
======================


Usage
-----


Installation
------------

Require `willdurand/rest-extra-bundle` to your `composer.json` file:

``` json
{
    "require": {
        "willdurand/rest-extra-bundle": "@stable"
    }
}
```

Register the bundle in `app/AppKernel.php`:

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Bazinga\Bundle\RestExtraBundle\BazingaRestExtraBundle(),
        );
    }

Enable the bundle's configuration in `app/config/config.yml`:

``` yaml
# app/config/config.yml
bazinga_rest_extra: ~
```
