<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 11.12.16
 * Time: 13:15
 */
namespace Plumbok;

use phpDocumentor\Reflection\DocBlock\Serializer;

/**
 * Interface Generator
 * @package Plumbok
 * @author Michał Brzuchalski <m.brzuchalski@madkom.pl>
 */
interface Generator
{
    public function __construct(Serializer $docBlockSerializer);
    public function generate() : GenerationResult;
}
