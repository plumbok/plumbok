<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 26.12.16
 * Time: 06:12
 */

namespace Plumbok\Test\Day;

/**
 * @RequiredArgsConstructor 
 * @method void __construct(int $day)
 */
class DayOfMonth
{
    /**
     * @var int
     */
    private $day;
    /**
     * @var int
     */
    private $month = 1;
}
