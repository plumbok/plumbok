<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 26.12.16
 * Time: 06:18
 */

namespace Plumbok\Test\Day;

/**
 * @AllArgsConstructor 
 * @EqualTo 
 * @method void __construct(int $day, int $year)
 * @method bool equalTo(object $other)
 */
class DayOfYear
{
    /**
     * @var int
     */
    private $day;
    /**
     * @var int
     */
    private $year = 2016;
}