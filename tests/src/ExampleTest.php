<?php
namespace Kazinduzi\Tests;

use PHPUnit\Framework\TestCase;
use Kazinduzi;
/**
 * Description of ExampleTest
 *
 * @author Emmanuel Ndayiragije <endayiragije@gmail.com>
 */
class ExampleTest extends TestCase
{
    /**
     * 
     */
    public function setUp()
    {
        parent::setUp();        
    }
    
    public function tearDown()
    {
        parent::tearDown();        
    }
    /**
     * 
     */
    public function testAppName()
    {
        Kazinduzi::setAppName('KasokoTest');
        $defAppName = Kazinduzi::getAppName();
        $this->assertEquals('KasokoTest', $defAppName);
    }

    /**
     * 
     */
    public function testPushAndPop()
    {     
        $stack = [];
        $this->assertEquals(0, count($stack));

        array_push($stack, 'foo');
        $this->assertEquals('foo', $stack[count($stack)-1]);
        $this->assertEquals(1, count($stack));

        $this->assertEquals('foo', array_pop($stack));
        $this->assertEquals(0, count($stack));
    }
}
