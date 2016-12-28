<?php
use Plumbok\Test\Day\DayOfYear;

/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 26.12.16
 * Time: 07:09
 */
class EqualToTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Plumbok\Autoload::register('Plumbok\\Test');
    }

    public function testEqualTo()
    {
        $this->assertTrue(class_exists(DayOfYear::class));
        $reflection = new ReflectionClass(DayOfYear::class);

        $this->assertTrue($reflection->hasMethod('equalTo'));
    }
}
