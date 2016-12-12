<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 11.12.16
 * Time: 13:32
 */
namespace Plumbok\Generator;

/**
 * Class WithPropertyName
 * @package Plumbok\Generator
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
trait WithPropertyName
{
    /**
     * @var string Holds property name
     */
    private $propertyName;

    /**
     * Sets property name
     * @param string $propertyName
     */
    public function setPropertyName(string $propertyName)
    {
        $this->propertyName = $propertyName;
    }
}
