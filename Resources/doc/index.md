BazingaRestExtraBundle
======================

This bundle provides extra features for your REST APIs built using Symfony2.

Usage
-----

In the following, you will find the documentation for all features provided by
this bundle.

### Listeners

#### CsrfDoubleSubmitListener

The `CsrfDoubleSubmitListener` listener is a way to protect you against CSRF
attacks by leveraging the client side instead of the plain old server side. It
is particularly useful for REST APIs as the Symfony2 CSRF protection relies on
the session in order to store the secret. That is why you often have to disable
CSRF protection when you use Forms in your REST APIs.

<img src="http://1.bp.blogspot.com/-ukVC7jdLTrI/Twca9giiQ9I/AAAAAAAAAmI/2fTIQrwnW6s/s1600/double_submit.png" style="max-width:100%;" align="right" />

Using the **double submit** mechanism, there is no need to store anything on the
server. However, the client MUST:

* generate a random secret;
* set a cookie with this secret;
* send this secret as part of the request parameters.

For further information, you can read more about [Stateless CSRF
protection](http://appsandsecurity.blogspot.se/2012/01/stateless-csrf-protection.html)
and [Stateful vs Stateless CSRF
Defences](http://blog.astrumfutura.com/2013/08/stateful-vs-stateless-csrf-defences-know-the-difference/).

Here is the configuration section for this listener. First, you must enable it,
then you have to choose names for both `cookie_name` and `parameter_name`
configuration parameters:

``` yaml
# app/config/config.yml
bazinga_rest_extra:
    csrf_double_submit_listener:
        enabled:        true
        cookie_name:    cookie_csrf
        parameter_name: _csrf_token
```

Once done, you can configure each action you want to protect with **CSRF double
submit** mechanism by using the `@CsrfDoubleSubmit` annotation:

``` php
use Bazinga\Bundle\RestExtraBundle\Annotation\CsrfDoubleSubmit;

// ...

/**
 * @CsrfDoubleSubmit
 */
public function createAction()
{
    // ...
}
```

Or you could protect a controller with the **CSRF double submit** mechanism
by using the `@CsrfDoubleSubmit` annotation, all methods except `GET, HEAD, OPTIONS, TRACE`
will be protected:

``` php
use Bazinga\Bundle\RestExtraBundle\Annotation\CsrfDoubleSubmit;

// ...

/**
 * @CsrfDoubleSubmit
 */
class ApiController
{
    // ...
}
```

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
bazinga_rest_extra:
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

#### VersionListener

The `VersionListener` listener provides API versioning by looking at the
`Accept` header. Here is the default configuration:

``` yaml
# app/config/config.yml
bazinga_rest_extra:
    version_listener:
        enabled:              false
        attribute_name:       _api_version
        parameter_name:       v
        default_version:      1
```

Turn the `enabled` flag to `true` in order to activate the listener, and you
will be able to use the `attribute_name` value as requirement in your routing
definition:

``` yaml
# app/config/routing.yml
acme_demo.test_all_v1:
    pattern:  /tests
    defaults: { _controller: AcmeDemoBundle:Test:all, _format: ~ }
    requirements:
        _method:        GET
        _api_version:   "1"

acme_demo.test_all_v2:
    pattern:  /tests
    defaults: { _controller: AcmeDemoBundle:Test:allVersion2, _format: ~ }
    requirements:
        _method:        GET
        _api_version:   "2"
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
