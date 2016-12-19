<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 18.12.16
 * Time: 12:55
 */
namespace Plumbok\Compiler;

use phpDocumentor\Reflection\DocBlock\Serializer;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Context as TypeContext;
use Plumbok\Compiler\Code\Property;
use Plumbok\Compiler\Generator\AllArgsConstructor as AllArgsConstructorGenerator;
use Plumbok\Compiler\Generator\Getter as GetterGenerator;
use Plumbok\Compiler\Generator\Setter as SetterGenerator;

/**
 * Class GeneratorFactory
 * @package Plumbok\Compiler
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class GeneratorFactory
{
    /** @var TypeContext */
    private $typeContext;
    /** @var Serializer */
    private $docBlockSerializer;

    /**
     * GeneratorFactory constructor.
     * @param TypeContext $typeContext
     * @param Serializer $docBlockSerializer
     */
    public function __construct(TypeContext $typeContext, Serializer $docBlockSerializer)
    {
        $this->typeContext = $typeContext;
        $this->docBlockSerializer = $docBlockSerializer;
    }

    /**
     * @param string $propertyName
     * @param Type $type
     * @return Statements
     */
    public function generateGetter(string $propertyName, Type $type) : Statements
    {
        $generator = new GetterGenerator($this->docBlockSerializer);
        $generator->setPropertyName($propertyName);
        $generator->setType($type);
        $generator->setTypeContext($this->typeContext);

        return $generator->generate();
    }

    /**
     * @param string $propertyName
     * @param Type $type
     * @return Statements
     */
    public function generateSetter(string $propertyName, Type $type) : Statements
    {
        $generator = new SetterGenerator($this->docBlockSerializer);
        $generator->setPropertyName($propertyName);
        $generator->setType($type);
        $generator->setTypeContext($this->typeContext);

        return $generator->generate();
    }

    /**
     * @param string $className
     * @param Property[] $properties
     * @return Statements
     */
    public function generateAllArgsConstructor(string $className, Property ...$properties) : Statements
    {
        $generator = new AllArgsConstructorGenerator($this->docBlockSerializer);
        $generator->setClassName($className);
        $generator->setTypeContext($this->typeContext);
        $generator->setProperties(...$properties);

        return $generator->generate();
    }
}
