<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 13.12.16
 * Time: 07:06
 */
namespace Plumbok\Test;
use Plumbok\Cache;

require_once 'vendor/autoload.php';

\Plumbok\Autoload::register(__NAMESPACE__, new Cache(__DIR__ . '/tests/cache'));

$email = new Email();
//$person = new Person();
$email->getEmail();