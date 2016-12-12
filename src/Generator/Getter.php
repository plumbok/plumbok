<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 11.12.16
 * Time: 13:12
 */
namespace Plumbok\Generator;

use PhpParser\Node\Stmt\Class_;
use Plumbok\GenerationResult;
use Doctrine\Common\Inflector\Inflector;
use PhpParser\Node;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Class Getter
 * @package Plumbok\Compiler
 * @author Michał Brzuchalski <m.brzuchalski@madkom.pl>
 */
class Getter extends GeneratorBase
{
    use WithPropertyName, WithType;

    /**
     * @return GenerationResult
     */
    public function generate(): GenerationResult
    {
        $docblock = new DocBlock('Retrieves ' . $this->propertyName, null, [
            new DocBlock\Tags\Return_($this->type),
        ]);
        $functionName = 'get' . ucfirst($this->propertyName);
        if (is_a($this->type, Boolean::class)) {
            $functionName = 'is' . ucfirst(Inflector::singularize($this->propertyName));
        }

        $result = new GenerationResult();
        $result->addTag(new DocBlock\Tags\Method($functionName, [], $this->type, false, $docblock->getDescription()));
        $result->addStmt(new Node\Stmt\ClassMethod(
            $functionName, [
                'flags' => Class_::MODIFIER_PUBLIC,
                'stmts' => [$this->createReturnProperty($this->propertyName)],
                'returnType' => $this->convertTypeToString($this->type),
            ],[
                'comments' => [$this->createComment($docblock)],
            ]
        ));

        return $result;
    }
}
