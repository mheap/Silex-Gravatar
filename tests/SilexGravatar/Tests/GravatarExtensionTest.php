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
}