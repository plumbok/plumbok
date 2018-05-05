<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 11.12.16
 * Time: 13:28
 */
namespace Plumbok\Compiler\Generator;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Serializer;
use phpDocumentor\Reflection\Types\Mixed;
use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Stmt\Expression;
use Plumbok\Compiler;

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
     * @return Node\Stmt\Return_
     */
    protected function createReturnProperty(string $propertyName) : Node\Stmt\Return_
    {
        return new Node\Stmt\Return_(new Node\Expr\PropertyFetch(new Node\Expr\Variable('this'), $propertyName));
    }

    /**
     * @param string $propertyName
     * @param string $propertySetter
     * @return Expression
     */
    protected function createPropertyMutation(string $propertyName, string $propertySetter = null): Expression
    {
        // $this->{$propertyName} = $$propertyName;
        if (empty($propertySetter)) {
            return new Expression(new Node\Expr\Assign(
                new Node\Expr\PropertyFetch(
                    new Node\Expr\Variable('this'),
                    $propertyName
                ),
                new Node\Expr\Variable($propertyName)
            )
            );
        }

        // $this->set{$propertyName}($$propertyName);
        return new Expression(new Node\Expr\MethodCall(
            new Node\Expr\Variable('this'),
            $propertySetter,
            [
                new Node\Expr\Variable($propertyName)
            ]
        ));
    }
}
