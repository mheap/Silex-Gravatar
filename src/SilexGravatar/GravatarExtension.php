<?php


namespace SilexGravatar;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Gravatar\Service,
    Gravatar\Cache\FilesystemCache,
    Gravatar\Cache\ExpiringCache,
    Gravatar\Extension\Twig\GravatarExtension as TwigGravatarExtension;

class GravatarExtension implements ServiceProviderInterface
{
    public function register(Application $app)
    {

    }

    public function boot(Application $app)
    {
        $app['gravatar.cache'] = $app->share(function () use ($app) {
            $cache = null;
            if(isset($app['gravatar.cache_dir'])) {
                $ttl   = isset($app['gravatar.cache_ttl']) ? $app['gravatar.cache_ttl'] : 360;
                $file  = new FilesystemCache($app['gravatar.cache_dir']);
                $cache = new ExpiringCache($file, $ttl);
            }
            return $cache;
        });

        $app['gravatar'] = $app->share(function () use ($app) {
            $options = isset($app['gravatar.options']) ? $app['gravatar.options'] : array();
            return new Service($options, $app['gravatar.cache']);
        });

        if (isset($app['twig'])) {
            $app['twig']->addExtension(new TwigGravatarExtension($app['gravatar']));
        }
    }
}
