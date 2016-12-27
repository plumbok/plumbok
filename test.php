<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 13.12.16
 * Time: 07:06
 */
namespace Plumbok\Test;

use Plumbok\Autoload;
use Plumbok\Cache\FileCache;
use Plumbok\Test\Day\DayOfMonth;
use Plumbok\Test\Day\DayOfYear;

require_once 'vendor/autoload.php';

Autoload::register(__NAMESPACE__, new FileCache(__DIR__ . '/tests/cache'));

$email = new Email('michal.brzuchalski@gmail.com', new UnannotatedClass());
$email->getEmail();

$email = new Email('michal.brzuchalski@gmail.com', new UnannotatedClass());
$email->getEmail();


$person = new Person(34, new DayOfMonth(29,9));
dump($person);

$dayOfYear = new Day\DayOfYear(120, 2016);

$otherDayOfYear = new Day\DayOfYear(120, 2016);

dump($dayOfYear, $otherDayOfYear, $dayOfYear->equalTo($otherDayOfYear));
