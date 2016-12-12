<?php
use Doctrine\Common\Inflector\Inflector;
use Plumbok\Test\ValueObject;

/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 09.12.16
 * Time: 23:17
 */
class AutoloaderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Plumbok\Autoload::register('Plumbok\\Test');
    }

    public function testValueObjectGeneration()
    {
        $loaded = class_exists(ValueObject::class);
        $this->assertTrue($loaded, 'Autoloading failed');

        $reflection = new ReflectionClass(ValueObject::class);

        foreach ($reflection->getProperties() as $property) {
            $getterExists = $reflection->hasMethod('get' . ucfirst($property->getName())) ||
                $reflection->hasMethod('is' . ucfirst(Inflector::singularize($property->getName())));
            $this->assertTrue($getterExists);

            $setterExists = $reflection->hasMethod('set' . ucfirst($property->getName()));
            $this->assertTrue($setterExists);
        }
    }
}
