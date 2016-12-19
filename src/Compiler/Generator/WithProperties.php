<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 18.12.16
 * Time: 07:56
 */
namespace Plumbok\Compiler\Generator;

use Plumbok\Compiler\Code\Property;

/**
 * Class WithProperties
 * @package Plumbok\Compiler\Generator
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
trait WithProperties
{
    /**
     * @var Property[]
     */
    private $properties;

    /**
     * @param Property[] ...$properties
     */
    public function setProperties(Property ...$properties)
    {
        $this->properties = $properties;
    }
}
