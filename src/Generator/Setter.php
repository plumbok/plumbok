<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 11.12.16
 * Time: 13:45
 */
namespace Plumbok\Generator;

use PhpParser\Node\Stmt\Class_;
use Plumbok\GenerationResult;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Types\Void_;
use PhpParser\Node;

/**
 * Class Setter
 * @package Plumbok\Generator
 * @author Michał Brzuchalski <m.brzuchalski@madkom.pl>
 */
class Setter extends GeneratorBase
{
    use WithPropertyName, WithType;

    /**
     * @return GenerationResult
     */
    public function generate(): GenerationResult
    {
        $docblock = new DocBlock('Sets ' . $this->propertyName, null, [
            new DocBlock\Tags\Param($this->propertyName, $this->type),
            new DocBlock\Tags\Return_(new Void_()),
        ]);
        $functionName = 'set' . ucfirst($this->propertyName);

        $result = new GenerationResult();
        $result->addTag(new DocBlock\Tags\Method(
            $functionName,
            [['name' => $this->propertyName, 'type' => $this->type]],
            new Void_(),
            false,
            $docblock->getDescription()
        ));
        $result->addStmt(new Node\Stmt\ClassMethod(
            $functionName, [
                'flags' => Class_::MODIFIER_PUBLIC,
                'params' => [$this->createParam($this->propertyName, $this->type)],
                'stmts' => [$this->createPropertyMutator($this->propertyName)],
                'returnType' => PHP_VERSION_ID < 700100 ? null : 'void',
            ], [
                'comments' => [$this->createComment($docblock)],
            ]
        ));

        return $result;
    }
}
