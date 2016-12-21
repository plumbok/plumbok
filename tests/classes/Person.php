<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 09.12.16
 * Time: 23:31
 */
namespace Plumbok\Test;

/**
 * Class Person
 *
 * @package Plumbok\Test
 * @author Michał Brzuchalski<m.brzuchalski@madkom.pl>
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
     * @var \DateTime
     * @Getter @Setter
     */
    private $birthdate;

    /**
     * @var int[]
     * @Getter @Setter
     */
    private $favouriteNumbers;
}
