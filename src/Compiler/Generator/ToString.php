<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 26.12.16
 * Time: 07:14
 */

namespace Plumbok\Compiler\Generator;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\Self_;
use phpDocumentor\Reflection\Types\String_;
use Plumbok\Compiler;
use Plumbok\Compiler\Code\Property;
use Plumbok\Compiler\Statements;
use PhpParser\Node;

/**
 * Class ToString
 * @package Plumbok\Compiler\Generator
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class ToString extends GeneratorBase
{
    use WithPropertyName;

    /**
     * @return Compiler\Statements
     */
    public function generate(): Compiler\Statements
    {
        $docBlock = new DocBlock(
            'Returns string from $' . $this->propertyName,
            null,
            [new Return_(new String_())]
        );
        $result = new Statements();
        $result->add(new Node\Stmt\ClassMethod(
            'toString',
            [
                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
                'stmts' => [
                    new Node\Stmt\Return_(new Node\Expr\Cast\String_(new Node\Expr\PropertyFetch(
                        new Node\Expr\Variable('this'),
                        $this->propertyName
                    ))),
                ],
                'returnType' => 'string',
            ],[
                'comments' => [$this->createComment($docBlock)],
            ]
        ));

        return $result;
    }
}