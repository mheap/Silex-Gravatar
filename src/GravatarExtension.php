<?php


namespace SilexGravatar;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Gravatar\Service;
use Gravatar\Cache\FilesystemCache;
use Gravatar\Cache\ExpiringCache;
use Gravatar\Extension\Twig\GravatarExtension as TwigGravatarExtension;

class GravatarExtension implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['gravatar.cache'] = function ($app) {
            $cache = null;
            if (isset($app['gravatar.cache_dir'])) {
                $ttl   = isset($app['gravatar.cache_ttl']) ? $app['gravatar.cache_ttl'] : 360;
                $file  = new FilesystemCache($app['gravatar.cache_dir']);
                $cache = new ExpiringCache($file, $ttl);
            }
            return $cache;
        };

        $app['gravatar'] = function ($app) {
            $options = isset($app['gravatar.options']) ? $app['gravatar.options'] : array();

            // HTTPS by default
            if (!isset($options['secure'])) {
                $options['secure'] = true;
            }

            return new Service($options, $app['gravatar.cache']);
        };

        if (isset($app['twig'])) {
            $app['twig'] = $app->extend('twig', function ($twig, $app) {
                $twig->addExtension(new TwigGravatarExtension($app['gravatar']));
                return $twig;
            });
        }
    }
}
