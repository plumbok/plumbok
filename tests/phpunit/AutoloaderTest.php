<?php
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 27.12.16
 * Time: 05:33
 */
class AutoloaderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('src'));
    }

    public function testRegisterWithoutNamespace()
    {
        $this->expectException(InvalidArgumentException::class);
        \Plumbok\Autoload::register('');
    }

    public function testRegister()
    {
        $count = count(spl_autoload_functions());
        \Plumbok\Autoload::register('Plumbok\\Test');
        $this->assertGreaterThan($count, count(spl_autoload_functions()));
    }

    public function testRegisterWithCache()
    {
        \Plumbok\Autoload::register('Plumbok\\Test', new \Plumbok\Cache\FileCache(vfsStream::url('src/cache')));
        $this->assertTrue(class_exists(Plumbok\Test\Day\DayOfWeek::class));
        $this->assertTrue(class_exists(Plumbok\Test\Day\DayOfWeek::class, false));
        $this->assertFileExists(vfsStream::url('src/cache/Plumbok.Test.Day.DayOfWeek.php'));
    }

    public function testLoad()
    {
        \Plumbok\Autoload::register('Plumbok\\Test');

        $this->assertTrue(class_exists(Plumbok\Test\Day\DayOfWeek::class));
        $this->assertTrue(class_exists(Plumbok\Test\Day\DayOfWeek::class, false));
    }


}
