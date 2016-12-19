<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 18.12.16
 * Time: 07:06
 */
namespace Plumbok\Compiler\Generator;

/**
 * Class WithClassName
 * @package Plumbok\Compiler\Generator
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
trait WithClassName
{
    /**
     * @var string Holds class name
     */
    private $className;

    /**
     * @param string $className
     */
    public function setClassName(string $className)
    {
        $this->className = $className;
    }
}
