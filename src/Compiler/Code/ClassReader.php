<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 18.12.16
 * Time: 17:34
 */
namespace Plumbok\Compiler\Code;

use Doctrine\Common\Annotations\DocParser;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Node\Stmt\Class_;

/**
 * Class ClassReader
 * @package Plumbok\Compiler\Code
 * @author Michał Brzuchalski <m.brzuchalski@madkom.pl>
 */
class ClassReader
{
    /**
     * @var DocParser
     */
    private $parser;
    /**
     * @var Context
     */
    private $context;

    /**
     * ClassReader constructor.
     * @param DocParser $parser
     * @param Context $context
     */
    public function __construct(DocParser $parser, Context $context)
    {
        $this->parser = $parser;
        $this->context = $context;
    }

    /**
     * @param Class_ $class
     * @return array
     */
    public function readAnnotations(Class_ $class) : array
    {
        return $this->parser->parse((string)$class->getDocComment());
    }

    /**
     * @param Class_ $class
     * @return DocBlock
     */
    public function readDocBlock(Class_ $class) : DocBlock
    {
        if (empty((string)$class->getDocComment())) {
            return new DocBlock('', null, [], $this->context);
        }

        return DocBlockFactory::createInstance()->create((string)$class->getDocComment(), $this->context);
    }
}
