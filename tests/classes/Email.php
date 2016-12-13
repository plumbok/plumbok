<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 12.12.16
 * Time: 14:11
 */
namespace Plumbok\Test;

use Plumbok\Test\{
    Person
};
use Plumbok\Test\Node\Common;

/**
 * @Value
 */
class Email
{
    /**
     * @var string
     */
    private $email = '';

    /**
     * @var Common
     * @Setter @Getter
     */
    private $common;

    /**
     * @var Person
     * @Setter @Getter
     */
    private $person;

    private function setEmail(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email address is invalid, given: {$email}");
        }
        $this->email = $email;
    }
}
