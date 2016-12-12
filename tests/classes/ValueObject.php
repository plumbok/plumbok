<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 09.12.16
 * Time: 23:31
 */
namespace Plumbok\Test;

use Plumbok\Annotation\Getter;
use Plumbok\Annotation\Setter;
/**
 * Class ValueObject
 * @package Plumbok\Test
 * @author Michał Brzuchalski <m.brzuchalski@madkom.pl>
 * @Value
 */
class ValueObject
{
    /**
     * Holds age
     * @var int
     * @Getter @Setter
     */
    private $age;

    /**
     * @var \DateTime
     * @Getter @Setter
     */
    private $date;

    /**
     * @var int[]
     * @Getter @Setter
     */
    private $days;

    /**
     * @var array
     * @Getter @Setter
     */
    private $names = [];
}
