<?php
use Plumbok\Test\Email;

/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 26.12.16
 * Time: 07:09
 */
class ToStringTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        Plumbok\Autoload::register('Plumbok\\Test');
    }

    public function testEqual()
    {
        $this->assertTrue(class_exists(Email::class));
        $reflection = new ReflectionClass(Email::class);

        $this->assertTrue($reflection->hasMethod('toString'));
    }
}
