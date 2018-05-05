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
use Plumbok\Compiler\Statements;

/**
 * Class AllArgsConstructor
 * @package Plumbok\Compiler\Generator
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class NoArgsConstructor extends GeneratorBase
{
    use WithClassName;

    /**
     * @return Compiler\Statements
     */
    public function generate(): Compiler\Statements
    {

        $docBlock = new DocBlock(
            $this->className . ' constructor.',
            null,
            []
        );
        $result = new Statements();
        $result->add(new Node\Stmt\ClassMethod(
            '__construct', [
                'flags' => Node\Stmt\Class_::MODIFIER_PUBLIC,
            ],[
                'comments' => [$this->createComment($docBlock)],
            ]
        ));

        return $result;

    }
}
