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

require_once 'vendor/autoload.php';

Autoload::register(__NAMESPACE__, new FileCache(__DIR__ . '/tests/cache'));

$email = new Email('michal.brzuchalski@gmail.com', new UnannotatedClass());
$email->getEmail();

$email = new Email('michal.brzuchalski@gmail.com', new UnannotatedClass());
$email->getEmail();


$person = new Person(34, \DateTime::createFromFormat('Y-m-d', '1983-02-12'));
$person->setBirthdate(new \DateTime('12-02-1983'));


$dayOfYear = new Day\DayOfYear(120, 2016);

$otherDayOfYear = new Day\DayOfYear(120, 2016);

dump($dayOfYear, $otherDayOfYear, $dayOfYear->equalTo($otherDayOfYear));
