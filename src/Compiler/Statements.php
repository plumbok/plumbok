<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 12.12.16
 * Time: 21:32
 */
namespace Plumbok\Compiler;

use PhpParser\Node\Stmt;

/**
 * Class Statements
 * @package Plumbok\Compiler
 * @author Michał Brzuchalski <m.brzuchalski@madkom.pl>
 */
class Statements implements \IteratorAggregate
{
    /**
     * @var Stmt[] Holds generated statements
     */
    private $statements = [];

    /**
     * Adds statement
     * @param Stmt[] ...$stmts
     */
    public function add(Stmt ...$stmts)
    {
        foreach ($stmts as $stmt) {
            $this->statements[] = $stmt;
        }
    }

    /**
     * @param Statements $other
     * @return Statements
     */
    public function merge(Statements $other) : Statements
    {
        $this->add(...$other->statements);

        return $this;
    }

    /**
     * @return \Traversable
     */
    public function getIterator() : \Traversable
    {
        return new \ArrayIterator($this->statements);
    }
}
