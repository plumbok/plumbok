<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 11.12.16
 * Time: 13:33
 */
namespace Plumbok\Compiler\Generator;

use phpDocumentor\Reflection\Type;

/**
 * Class WithType
 * @package Plumbok\Compiler\Generator
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
trait WithType
{
    /**
     * @var Type
     */
    private $type;

    /**
     * Sets type
     * @param Type $type
     */
    public function setType(Type $type)
    {
        $this->type = $type;
    }
}
