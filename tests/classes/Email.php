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
 * @method void __construct(string $email, \Plumbok\Test\UnannotatedClass $someObject)
 * @method string getEmail()
 * @method \Plumbok\Test\UnannotatedClass getSomeObject()
 * @method void setSomeObject(\Plumbok\Test\UnannotatedClass $someObject)
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
