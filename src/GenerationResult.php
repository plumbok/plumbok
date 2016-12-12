<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 11.12.16
 * Time: 13:20
 */
namespace Plumbok;

use phpDocumentor\Reflection\DocBlock\Tag;
use PhpParser\Node\Stmt;

/**
 * Class GenerationResult
 * @package Plumbok
 * @author Michał Brzuchalski <m.brzuchalski@madkom.pl>
 */
class GenerationResult
{
    /**
     * @var Stmt[] Holds generated statements
     */
    private $stmts = [];

    /**
     * @var Tag[] Holds generated tags
     */
    private $tags = [];

    /**
     * GenerationResult constructor.
     * @param Stmt[] $stmts
     * @param Tag[] $tags
     */
    public function __construct(array $stmts = [], array $tags = [])
    {
        $this->stmts = $stmts;
        $this->tags = $tags;
    }

    /**
     * Retrieves statements
     * @return Stmt[]
     */
    public function getStmts(): array
    {
        return $this->stmts;
    }

    /**
     * Adds statement
     * @param Stmt[] ...$stmts
     */
    public function addStmt(Stmt ...$stmts)
    {
        foreach ($stmts as $stmt) {
            $this->stmts[] = $stmt;
        }
    }

    /**
     * Retrieves tags
     * @return Tag[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * Adds tag
     * @param Tag[] ...$tags
     */
    public function addTag(Tag ...$tags)
    {
        foreach ($tags as $tag) {
            $this->tags[] = $tag;
        }
    }

    /**
     * @param GenerationResult $other
     */
    public function merge(self $other)
    {
        $this->addStmt(...$other->stmts);
        $this->addTag(...$other->tags);
    }
}
