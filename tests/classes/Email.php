<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 12.12.16
 * Time: 14:11
 */
namespace Plumbok\Test;

/**
 * @Value 
 * @ToString (property = "email")
 */
class Email
{
    /**
     * @var string
     */
    private $email = '';

    /**
     * @var UnannotatedClass
     * @Setter @Getter
     */
    private $someObject;

    private function setEmail(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email address is invalid, given: {$email}");
        }
        $this->email = $email;
    }
}
