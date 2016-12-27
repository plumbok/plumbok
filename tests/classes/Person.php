<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 09.12.16
 * Time: 23:31
 */
namespace Plumbok\Test;

use Plumbok\Test\Day\{
    DayOfMonth
};

/**
 * @Data 
 */
class Person
{
    /**
     * @var array
     * @Getter @Setter
     */
    private $names = [];

    /**
     * Holds age
     * @var int
     * @Getter @Setter
     */
    private $age;

    /**
     * @var DayOfMonth
     * @Getter @Setter
     */
    private $nameDay;

    /**
     * @var int[]
     * @Getter @Setter
     */
    private $favouriteNumbers = [1, 7, 14, 21, 28];
}
