<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 11.12.16
 * Time: 13:28
 */
namespace Plumbok\Generator;

use Plumbok\Compiler;
use Plumbok\Generator;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Serializer;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Mixed;
use PhpParser\Comment;
use PhpParser\Node;

/**
 * Class GeneratorBase
 * @package Plumbok\Generator
 * @author Michał Brzuchalski <m.brzuchalski@madkom.pl>
 */
abstract class GeneratorBase implements Generator
{
    /**
     * @var Serializer
     */
    private $docBlockSerializer;

    /**
     * GeneratorBase constructor.
     * @param Serializer $docBlockSerializer
     */
    public function __construct(Serializer $docBlockSerializer)
    {
        $this->docBlockSerializer = $docBlockSerializer;
    }

    /**
     * @param DocBlock $docblock
     * @return Comment
     */
    protected function createComment(DocBlock $docblock) : Comment
    {
        return new Comment($this->docBlockSerializer->getDocComment($docblock));
    }

    /**
     * @param string $propertyName
     * @param Type $type
     * @return Node\Param
     */
    protected function createParam(string $propertyName, Type $type) : Node\Param
    {
        $type = $this->convertTypeToString($type);
        return new Node\Param($propertyName, null, (string)$type);
    }

    /**
     * @param string $propertyName
     * @return Node\Stmt\Return_
     */
    protected function createReturnProperty(string $propertyName) : Node\Stmt\Return_
    {
        return new Node\Stmt\Return_(new Node\Expr\PropertyFetch(new Node\Expr\Variable('this'), $propertyName));
    }

    /**
     * @param string $propertyName
     * @return Node\Expr\Assign
     */
    protected function createPropertyMutator(string $propertyName) : Node\Expr\Assign
    {
        return new Node\Expr\Assign(
            new Node\Expr\PropertyFetch(new Node\Expr\Variable('this'), $propertyName),
            new Node\Expr\Variable($propertyName)
        );
    }

    /**
     * @param Type $type
     * @return string
     */
    protected function convertTypeToString(Type $type) : string
    {
        if ($type instanceof Array_) {
            return 'array';
        }
        if ($type instanceof Mixed) {
            return '';
        }

        return (string)$type;
    }
}
