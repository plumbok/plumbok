<?php
use Plumbok\Test\Day\DayOfMonth;
use Plumbok\Test\Day\DayOfWeek;
use Plumbok\Test\Day\DayOfYear;

/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 26.12.16
 * Time: 06:10
 */
class ConstructorTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        Plumbok\Autoload::register('Plumbok\\Test');
    }

    public function testNoArgsConstructor()
    {
        $this->assertTrue(class_exists(DayOfWeek::class));
        $reflection = new ReflectionClass(DayOfWeek::class);

        $this->assertTrue($reflection->hasMethod('__construct'));
        $this->assertEquals(0, $reflection->getMethod('__construct')->getNumberOfParameters());
    }

    public function testRequiredArgsConstructor()
    {
        $this->assertTrue(class_exists(DayOfMonth::class));
        $reflection = new ReflectionClass(DayOfMonth::class);

        $this->assertTrue($reflection->hasMethod('__construct'));
        $this->assertEquals(1, $reflection->getMethod('__construct')->getNumberOfParameters());
    }

    public function testAllArgsConstructor()
    {
        $this->assertTrue(class_exists(DayOfYear::class));
        $reflection = new ReflectionClass(DayOfYear::class);

        $this->assertTrue($reflection->hasMethod('__construct'));
        $this->assertEquals(2, $reflection->getMethod('__construct')->getNumberOfParameters());
    }
}
