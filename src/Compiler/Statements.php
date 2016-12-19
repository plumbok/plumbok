<?php declare(strict_types=1);
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
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class Statements implements \IteratorAggregate, \Countable
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

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->statements);
    }
}
