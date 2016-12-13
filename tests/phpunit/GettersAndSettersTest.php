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
        $loaded = class_exists(Person::class);
        $this->assertTrue($loaded, 'Autoloading failed');

        $reflection = new ReflectionClass(Person::class);

        foreach ($reflection->getProperties() as $property) {
            $getterExists = $reflection->hasMethod('get' . ucfirst($property->getName())) ||
                $reflection->hasMethod('is' . ucfirst(Inflector::singularize($property->getName())));

            $this->assertTrue($getterExists);
        }
    }

    public function testSettersGeneration()
    {
        $loaded = class_exists(Person::class);
        $this->assertTrue($loaded, 'Autoloading failed');

        $reflection = new ReflectionClass(Person::class);

        foreach ($reflection->getProperties() as $property) {
            $setterExists = $reflection->hasMethod('set' . ucfirst($property->getName()));
            $this->assertTrue($setterExists);
        }
    }
}
