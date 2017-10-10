<?php

namespace SilexGravatar\Tests\Extension;

use Silex\Application;
use SilexGravatar\GravatarExtension;
use Gravatar\Service;
use Silex\Provider\TwigServiceProvider;

use PHPUnit\Framework\TestCase;

class GravatarExtensionTest extends TestCase
{
    public function testUsesDefaults()
    {
        $app = new Application();
        $app->register(new GravatarExtension(), array(
            'gravatar.options'  => array(
                'size' => 999,
                'rating' => 'pg',
                'default' => 'mm'
            )
        ));

        $this->assertInstanceOf(Service::class, $app['gravatar']);
        $this->assertTrue($app['gravatar']->exist('sven.eisenschmidt@gmail.com'));

        $url = $app['gravatar']->get('sven.eisenschmidt@gmail.com', array());

        $this->assertContains('https://', $url);
        $this->assertContains('r=pg', $url);
        $this->assertContains('s=999', $url);
        $this->assertContains('d=mm', $url);
    }

    public function testOverrideOptionsWhenFetching()
    {
        $app = new Application();
        $app->register(new GravatarExtension(), array(
            'gravatar.options'  => array(
                'size' => 999,
                'rating' => 'g',
                'default' => 'mm'
            )
        ));

        $this->assertInstanceOf(Service::class, $app['gravatar']);
        $this->assertTrue($app['gravatar']->exist('sven.eisenschmidt@gmail.com'));

        $url = $app['gravatar']->get('sven.eisenschmidt@gmail.com', array(
            'size' => 666,
            'default' => 'monsterid',
            'secure'  => true
        ));

        $this->assertContains('https://', $url);
        $this->assertContains('r=g', $url);
        $this->assertContains('s=666', $url);
        $this->assertContains('d=monsterid', $url);
    }

    public function testUseNonSecureHttp()
    {
        $app = new Application();
        $app->register(new GravatarExtension(), array(
            'gravatar.options'  => array(
                'secure' => false,
            )
        ));

        $url = $app['gravatar']->get('sven.eisenschmidt@gmail.com', array());

        $this->assertContains('http://', $url);
    }

    public function testCache()
    {
        $tmp = sys_get_temp_dir();
        $app = new Application();
        $app->register(new GravatarExtension(), array(
            'gravatar.cache_dir'  => $tmp.'/gravatar',
            'gravatar.cache_ttl'  => 500,
            'gravatar.options' => array(
                'size' => 100
            )
        ));


        $url = $app['gravatar']->exist('m@michaelheap.com');

        $this->assertFileExists($tmp."/gravatar/d9dde4b568df1db3980ea4d9bf974bf4ac283b04");
        $this->assertFileExists($tmp."/gravatar/d9dde4b568df1db3980ea4d9bf974bf4ac283b04.expires");
    }

    public function testRegisterTwigExtension()
    {
        $app = new Application();

        $app->register(new TwigServiceProvider(), array(
            'twig.templates' => array(
                'test' => '{{ gravatar("m@michaelheap.com")}}'
            )
        ));

        $app->register(new GravatarExtension(), array(
            'gravatar.options' => array(
                'size' => 100
            )
        ));


        $url = $app['twig']->render('test');
        $this->assertEquals('https://www.gravatar.com/avatar/bbf9decfbfc2ab5b450ec503749ded28?s=100&r=g', $url);
    }
}
