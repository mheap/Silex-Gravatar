<?php

namespace SilexGravatar\Tests\Extension;

use Silex\Application;

use Symfony\Component\HttpFoundation\Request;

use SilexGravatar\GravatarExtension;

use Gravatar\Service;

class GravatarExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!class_exists('Gravatar\\Service')) {
            $this->markTestSkipped('Gravatar was not installed.');
        }
    }

    public function testRegister()
    {
        $app = new Application();
        $app->register(new GravatarExtension(), array(
            'gravatar.options'  => array(
                'size' => 999,
                'rating' => 'pg',
                'default' => 'mm'
            )
        ));

        $app->get('/', function() use($app) {
            $app['gravatar'];    
        });
        $request = Request::create('/');
        $app->handle($request);

        $this->assertInstanceOf('Gravatar\Service', $app['gravatar']);
        $this->assertTrue($app['gravatar']->exist('sven.eisenschmidt@gmail.com'));

        $url = $app['gravatar']->get('sven.eisenschmidt@gmail.com', array(
            'size' => 666,
            'default' => 'monsterid',
            'secure'  => true
        ));

        $this->assertContains('https://', $url);
        $this->assertContains('r=pg', $url);
        $this->assertContains('s=666', $url);
        $this->assertContains('d=monsterid', $url);
    }

    public function testCache()
    {
        $app = new Application();
        $app->register(new GravatarExtension(), array(
            'gravatar.cache_dir'  => '/tmp/gravatar',
            'gravatar.cache_ttl'  => 500,
            'gravatar.options' => array(
                'size' => 100
            )
        ));

        // Force registering
        $app->get("/", function() use ($app){});

        $request = Request::create("/");
        $app->handle($request);

        $url = $app['gravatar']->exist('m@michaelheap.com');

        $this->assertFileExists("/tmp/gravatar/10838f67e1bc005832d48ec9ed42a8b8e98e0699");
        $this->assertFileExists("/tmp/gravatar/10838f67e1bc005832d48ec9ed42a8b8e98e0699.expires");

    }
}
