<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 16.12.16
 * Time: 22:23
 */
namespace Plumbok\Compiler\Generator;

use phpDocumentor\Reflection\DocBlock;
use PhpParser\Node;
use Plumbok\Compiler;
use Plumbok\Compiler\Code\Property;
use Plumbok\Compiler\Statements;

/**
 * Class AllArgsConstructor
 * @package Plumbok\Compiler\Generator
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class AllArgsConstructor extends GeneratorBase
{
    use WithClassName, WithTypeResolver, WithProperties;

    /**
     * @return Compiler\Statements
     */
    public function generate(): Compiler\Statements
    {

        $docBlock = new DocBlock(
            $this->className . ' constructor.',
            null,
            array_map(function (Property $property) {
                return new DocBlock\Tags\Param($property->getName(), $property->getType());
            }, $this->properties),
            $this->typeContext
        );
        $result = new Statements();
        $result->add(new Node\Stmt\ClassMethod(
            '__construct', [
                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
                'params' => array_map(function (Property $property) {
                    return new Node\Param(
                        $property->getName(),
                        null,
                        $this->resolveType($property->getType())
                    );
                }, $this->properties),
                'statements' => array_map(function (Property $property) {
                    return $this->createPropertyMutation($property->getName(), $property->getSetter());
                }, $this->properties),
            ],[
                'comments' => [$this->createComment($docBlock)],
            ]
        ));

        return $result;

    }
}
