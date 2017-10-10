# Silex-Markdown

[![Build Status](https://secure.travis-ci.org/mheap/Silex-Gravatar.png?branch=master)](http://travis-ci.org/mheap/Silex-Gravatar)

### Requirements

This extension only works with *PHP 7.1+* and *Silex 2*.
[Version 1.1.0](https://github.com/mheap/Silex-Gravater/releases/tag/1.1.0) is compatible
with Silex 1.

### Installation

Install with composer:

```bash
composer require mheap/silex-gravatar
```

### Usage

First, you need to register the Gravatar extension. All of the options shown are optional.

```php
$app->register(new SilexExtension\GravatarExtension(), array(
    'gravatar.cache_dir'  => sys_get_temp_dir() . '/gravatar',
    'gravatar.cache_ttl'  => 240, // 240 seconds
    'gravatar.options' => array(
        'size' => 100,
        'rating' => Gravatar\Service::RATING_G,
        'secure' => true,
        'default'   => Gravatar\Service::DEFAULT_404,
        'force_default' => true
    )
));
```

To fetch a Gravatar URL, use `$app['gravatar']`:

```php
$app->get('/', function() use($app) {
    return $app['gravatar']->get('m@michaelheap.com');
});
```

If you're using Twig via `Silex\Provider\TwigServiceProvider()`, a `gravatar` function will
be automatically registered for you. This allows you do do the following:

```twig
{% if gravatar_exist('m@michaelheap.com') %}
    Gravatar found
{% endif %}

<img src="{{ gravatar('m@michaelheap.com', {'size': 50}) }}" />
```

### Available configuration options

The *GravatarExtension* provides access to the Gravatar web service
through Sven Eisenschmidts's `Gravatar <https://github.com/fate/Gravatar-php>`_
library.

* **gravatar.cache_dir** (optional): A directory to cache the direct web service calls to gravatar.com
* **gravatar.cache_ttl** (optional): The time how long a cache entry will live, defaults to 360 seconds 
* **gravatar.options** (optional): An associative array of arguments for the [Gravatar\\Service class](https://github.com/sveneisenschmidt/gravatar-php/blob/master/src/Gravatar/Service.php#L84-L90)

### Running the tests

There are no external dependencies for this library. Just `composer install` then run `./vendor/bin/phpunit`
