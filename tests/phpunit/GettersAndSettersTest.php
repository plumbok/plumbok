<?php
use Doctrine\Common\Inflector\Inflector;
use Plumbok\Test\Person;

/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 09.12.16
 * Time: 23:17
 */
class GettersAndSettersTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Plumbok\Autoload::register('Plumbok\\Test');
    }

    public function testGettersGeneration()
    {
        $this->assertTrue(class_exists(Person::class), 'Autoloading failed');
        $reflection = new ReflectionClass(Person::class);

        foreach ($reflection->getProperties() as $property) {
            $getterExists = $reflection->hasMethod('get' . ucfirst($property->getName())) ||
                $reflection->hasMethod('is' . ucfirst(Inflector::singularize($property->getName())));

            $this->assertTrue($getterExists);
        }
    }

    public function testSettersGeneration()
    {
        $this->assertTrue(class_exists(Person::class));
        $reflection = new ReflectionClass(Person::class);

        foreach ($reflection->getProperties() as $property) {
            $setterExists = $reflection->hasMethod($setter = 'set' . ucfirst($property->getName()));
            $this->assertTrue($setterExists);
        }
    }
}
