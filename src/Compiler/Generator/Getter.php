<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 11.12.16
 * Time: 13:12
 */
namespace Plumbok\Compiler\Generator;

use PhpParser\Node\Stmt\Class_;
use Doctrine\Common\Inflector\Inflector;
use PhpParser\Node;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Types\Boolean;
use Plumbok\Compiler\Statements;

/**
 * Class Getter
 * @package Plumbok\Compiler
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class Getter extends GeneratorBase
{
    use WithPropertyName, WithType, WithTypeResolver;

    /**
     * @return Statements
     */
    public function generate(): Statements
    {
        $docBlock = new DocBlock(
            'Retrieves ' . $this->propertyName,
            null,
            [new DocBlock\Tags\Return_($this->type)],
            $this->typeContext
        );
        $functionName = 'get' . ucfirst($this->propertyName);
        if (is_a($this->type, Boolean::class)) {
            $functionName = 'is' . ucfirst(Inflector::singularize($this->propertyName));
        }
        $result = new Statements();
        $result->add(new Node\Stmt\ClassMethod(
            $functionName, [
                'flags' => Class_::MODIFIER_PUBLIC,
                'statements' => [$this->createReturnProperty($this->propertyName)],
                'returnType' => $this->resolveType($this->type) ?: null,
            ],[
                'comments' => [$this->createComment($docBlock)],
            ]
        ));

        return $result;
    }
}
