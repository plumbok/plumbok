<?php
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

trait WithTypeResolver
{
    /**
     * @var Context
     */
    private $typeContext;

    public function setTypeContext(Context $typeContext)
    {
        $this->typeContext = $typeContext;
    }

    private function resolveType(string $type) : string
    {
        /** @var TypeResolver $typeResolver */
        static $typeResolver;
        if ($typeResolver === null) {
            $typeResolver = new TypeResolver();
        }

        $resolvedType = $typeResolver->resolve($type, $this->typeContext);
        if ($resolvedType instanceof Array_) {
            return 'array';
        }
        foreach($this->typeContext->getNamespaceAliases() as $alias => $namespace) {
            if ((string)$resolvedType === "\\{$namespace}") {
                return $alias;
            }
        }

        return (string)$resolvedType;
    }
}
