<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 11.12.16
 * Time: 13:28
 */
namespace Plumbok\Compiler\Generator;

use Plumbok\Compiler;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Serializer;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Mixed;
use PhpParser\Comment;
use PhpParser\Node;

/**
 * Class GeneratorBase
 * @package Plumbok\Compiler\Generator
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
abstract class GeneratorBase
{
    /**
     * @return Compiler\Statements
     */
    abstract public function generate() : Compiler\Statements;

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
        return new Comment\Doc($this->docBlockSerializer->getDocComment($docblock));
    }

    /**
     * @param string $propertyName
     * @param Type $type
     * @return Node\Param
     * @deprecated
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
     * @param string $propertySetter
     * @return Node\Expr
     */
    protected function createPropertyMutation(string $propertyName, string $propertySetter = null) : Node\Expr
    {
        // $this->{$propertyName} = $$propertyName;
        if (empty($propertySetter)) {
            return new Node\Expr\Assign(
                new Node\Expr\PropertyFetch(
                    new Node\Expr\Variable('this'),
                    $propertyName
                ),
                new Node\Expr\Variable($propertyName)
            );
        }

        // $this->set{$propertyName}($$propertyName);
        return new Node\Expr\MethodCall(
            new Node\Expr\Variable('this'),
            $propertySetter,
            [
                new Node\Expr\Variable($propertyName)
            ]
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
