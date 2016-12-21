<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 18.12.16
 * Time: 18:18
 */
namespace Plumbok\Compiler\Code;

use Doctrine\Common\Annotations\DocParser;
use phpDocumentor\Reflection\Types\Context;

/**
 * Class MethodReader
 * @package Plumbok\Compiler\Code
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class MethodReader
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
     * PropertyReader constructor.
     * @param DocParser $parser
     * @param Context $context
     */
    public function __construct(DocParser $parser, Context $context)
    {
        $this->parser = $parser;
        $this->context = $context;
    }
}
