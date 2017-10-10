<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/twig',
));

$app->register(new SilexGravatar\GravatarExtension(), array(
    'gravatar.cache_dir'  => sys_get_temp_dir() . '/gravatar',
    'gravatar.cache_ttl'  => 500,
    'gravatar.options' => array(
        'size' => 100
    )
));

$app->get('/', function () use ($app) {
    return $app['twig']->render('gravatar.twig');
});

$app->run();
