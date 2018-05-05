<?php
use Doctrine\Common\Inflector\Inflector;
use Plumbok\Test\Email;

/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 12.12.16
 * Time: 14:23
 */
class ValueTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        Plumbok\Autoload::register('Plumbok\\Test');
    }

    public function testGettersGeneration()
    {
        $this->assertTrue(class_exists(Email::class));
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
