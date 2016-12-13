<?php
use Doctrine\Common\Inflector\Inflector;
use Plumbok\Test\Email;

/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 12.12.16
 * Time: 14:23
 */
class ValueTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Plumbok\Autoload::register('Plumbok\\Test');
    }

//    public function testAllArgsConstructorGeneration()
//    {
//        $loaded = class_exists(Email::class);
//        $this->assertTrue($loaded, 'Autoloading failed');
//
//        $reflection = new ReflectionClass(Email::class);
//        $reflectionConstructor = $reflection->getMethod('__construct');
//
//        $unMatchedArgsCount = count($reflection->getProperties());
//        foreach ($reflectionConstructor->getParameters() as $reflectionParameter) {
//            if ($reflection->hasProperty($reflectionParameter->getName())) {
//                --$unMatchedArgsCount;
//            }
//        }
//        $this->assertEquals(0, $unMatchedArgsCount, 'Unitialized by constructor properties exists');
//    }

    public function testGettersGeneration()
    {
        $loaded = class_exists(Email::class);
        $this->assertTrue($loaded, 'Autoloading failed');

        $reflection = new ReflectionClass(Email::class);

        foreach ($reflection->getProperties() as $property) {
            $getterExists = $reflection->hasMethod('get' . ucfirst($property->getName())) ||
                $reflection->hasMethod('is' . ucfirst(Inflector::singularize($property->getName())));
            $this->assertTrue($getterExists);

            $setterExists = $reflection->hasMethod('set' . ucfirst($property->getName()));
            $this->assertTrue($setterExists);
        }
    }
}
