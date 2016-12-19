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
dump($email);
$person = new Person();
$person->setBirthdate(new \DateTime('12-02-1983'));
dump($person);