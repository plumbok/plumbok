<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 13.12.16
 * Time: 11:08
 */
namespace Plumbok\Compiler\Generator;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Context;

/**
 * Class WithTypeResolver
 * @package Plumbok\Compiler\Generator
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
trait WithTypeResolver
{
    /**
     * @var Context
     */
    private $typeContext;

    /**
     * @param Context $typeContext
     */
    public function setTypeContext(Context $typeContext)
    {
        $this->typeContext = $typeContext;
    }

    /**
     * @param Type $type
     * @return string
     */
    private function resolveType(Type $type) : string
    {
//        /** @var TypeResolver $typeResolver */
//        static $typeResolver;
//        if ($typeResolver === null) {
//            $typeResolver = new TypeResolver();
//        }
        if ($type instanceof Array_) {
            return 'array';
        }
        foreach($this->typeContext->getNamespaceAliases() as $alias => $namespace) {
            if ((string)$type === "\\{$namespace}") {
                return $alias;
            }
        }

        return (string)$type;
    }
}
