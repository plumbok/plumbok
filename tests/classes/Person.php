<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 09.12.16
 * Time: 23:31
 */
namespace Plumbok\Test;

use Plumbok\Annotation\Data;
use Plumbok\Annotation\Getter;
use Plumbok\Test\Day\DayOfMonth;

/**
 * @Data ()
 * @method void __construct(int $age, \Plumbok\Test\DayOfMonth $nameDay)
 * @method array getNames()
 * @method void setNames(array $names)
 * @method int getAge()
 * @method void setAge(int $age)
 * @method \Plumbok\Test\DayOfMonth getNameDay()
 * @method void setNameDay(\Plumbok\Test\DayOfMonth $nameDay)
 * @method int[] getFavouriteNumbers()
 * @method void setFavouriteNumbers(int[] $favouriteNumbers)
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
