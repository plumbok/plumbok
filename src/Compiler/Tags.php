<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 12.12.16
 * Time: 21:39
 */
namespace Plumbok\Compiler;

use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Mixed;
use PhpParser\Node\Stmt\ClassMethod;
use Traversable;

/**
 * Class Tags
 * @package Plumbok\Compiler
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class Tags implements \IteratorAggregate
{
    /**
     * @var Tag
     */
    private $tags = [];

    /**
     * @param Statements $statements
     * @param callable $docBlockFactory
     * @return Tags
     */
    public static function createFromStatements(Statements $statements, callable $docBlockFactory) : Tags
    {
        $instance = new self();
        foreach ($statements as $statement) {
            if ($statement instanceof ClassMethod) {
                $returnType = new Mixed();
                $paramTypes = [];
                if ($statement->getDocComment()) {
                    $propertyDocBlock = $docBlockFactory($statement->getDocComment()->getText());
                    if (count($returnTags = $propertyDocBlock->getTagsByName('return'))) {
                        $returnType = $returnTags[0]->getType();
                    }
                    if (count($paramTags = $propertyDocBlock->getTagsByName('param'))) {
                        /** @var Param $paramTag */
                        foreach ($paramTags as $paramTag) {
                            $paramTypes[$paramTag->getVariableName()] = $paramTag->getType();
                        }
                    }
                }
                $arguments = [];
                foreach ($statement->getParams() as $param) {
                    $arguments[] = [
                        'name' => $param->name,
                        'type' => array_key_exists($param->name, $paramTypes) ? $paramTypes[$param->name] : $param->getType(),
                    ];
                }
                $instance->add(new Method(
                    $statement->name,
                    $arguments,
                    $returnType,
                    $statement->isStatic(),
                    null
                ));
            }
        }

        return $instance;
    }

    /**
     * @param Tag[] ...$tags
     */
    public function add(Tag ...$tags)
    {
        foreach ($tags as $tag) {
            $this->tags[] = $tag;
        }
    }

    /**
     * @return Traversable
     */
    public function getIterator() : \Traversable
    {
        return new \ArrayIterator($this->tags);
    }
}
