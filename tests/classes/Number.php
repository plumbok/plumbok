<?php declare(strict_types = 1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 27.12.16
 * Time: 06:04
 */
namespace Plumbok\Test;

use Plumbok\Annotation\Getter;
use Plumbok\Annotation\Setter;

/**
 * Class Number
 *
 * @method int getValue()
 * @method void setValue(int $value)
 */
class Number
{
    /**
     * @var int
     * @Getter @Setter()
     */
    private $value;
}