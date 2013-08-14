BazingaRestExtraBundle
======================

This bundle provides extra features for your REST APIs built using Symfony2.

Usage
-----

In the following, you will find the documentation for all features provided by
this bundle.

### Listeners

#### LinkRequestListener

The `LinkRequestListener` listener is able to convert **links**, as described in
[RFC 5988](http://tools.ietf.org/html/rfc5988) and covered in [this blog post
about REST APIs with Symfony2](http://williamdurand.fr/2012/08/02/rest-apis-with-symfony2-the-right-way/#the-friendship-algorithm),
into PHP **objects**. This listener makes two **strong** assumptions:

* Your `getAction()` action (naming does not matter here), also known as the
  action used to retrieve a specific resource must take the `identifier` as is,
  and MUST NOT use [Param
  Converters](http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html);

* This method MUST return an `array`, such as `array('user' => $user)`.

If it is ok for you, then turn the listener on in the configuration:

``` yaml
# app/config/config.yml
bazinga_rest_extra: ~
    link_request_listener: true
```

Now you can retrieve your objects in the Request's attributes:

``` php
if (!$request->attributes->has('links')) {
    throw new HttpException(400, 'No links found');
}

foreach ($request->attributes->get('links') as $linkObject) {
    // ...
}
```

### Testing

The bundle provides a `WebTestCase` class that provides useful methods for
testing your REST APIs.

* The `assertJsonResponse()` method allows you to assert that you got a JSON
response:

``` php
$client   = static::createClient();
$crawler  = $client->request('GET', '/users');
$response = $client->getResponse();

$this->assertJsonResponse($response);
```

* The `jsonRequest()` method allows you to perform a JSON request by setting both
the `Content-Type` and the `Accept` headers to `application/json`:

``` php
$client   = static::createClient();
$crawler  = $this->jsonRequest('GET', '/users/123', $data = array());
$response = $client->getResponse();
```


Installation
------------

Require [`willdurand/rest-extra-bundle`](https://packagist.org/packages/willdurand/rest-extra-bundle)
to your `composer.json` file:


``` json
{
    "require": {
        "willdurand/rest-extra-bundle": "@stable"
    }
}
```

Register the bundle in `app/AppKernel.php`:

``` php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new Bazinga\Bundle\RestExtraBundle\BazingaRestExtraBundle(),
    );
}
```

Enable the bundle's configuration in `app/config/config.yml`:

``` yaml
# app/config/config.yml
bazinga_rest_extra: ~
```


Testing
-------

Setup the test suite using [Composer](http://getcomposer.org/):

    $ composer install --dev

Run it using PHPUnit:

    $ phpunit
