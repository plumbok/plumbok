<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 11.12.16
 * Time: 13:45
 */
namespace Plumbok\Compiler\Generator;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Types\Void_;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use Plumbok\Compiler\Statements;

/**
 * Class Setter
 * @package Plumbok\Compiler\Generator
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class Setter extends GeneratorBase
{
    use WithPropertyName, WithType, WithTypeResolver;

    /**
     * @return Statements
     * @throws \Exception
     */
    public function generate(): Statements
    {
        $docBlock = new DocBlock(
            'Sets ' . $this->propertyName,
            null,
            [
                new DocBlock\Tags\Param($this->propertyName, $this->type),
                new DocBlock\Tags\Return_(new Void_()),
            ],
            $this->typeContext
        );
        $functionName = 'set' . ucfirst($this->propertyName);

        $result = new Statements();
        $result->add(new Node\Stmt\ClassMethod(
            $functionName, [
                'flags' => Class_::MODIFIER_PUBLIC,
                'params' => [new Node\Param(new Node\Expr\Variable($this->propertyName), null, $this->resolveType($this->type))],
                'stmts' => [$this->createPropertyMutation($this->propertyName)],
                'returnType' => PHP_VERSION_ID < 700100 ? null : 'void',
            ], [
                'comments' => [$this->createComment($docBlock)],
            ]
        ));

        return $result;
    }
}
