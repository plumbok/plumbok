<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 18.12.16
 * Time: 18:06
 */
namespace Plumbok\Compiler\Code;


use phpDocumentor\Reflection\Type;

class Property
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var Type
     */
    private $type;
    /**
     * @var array
     */
    private $annotations = [];
    /**
     * @var string
     */
    private $setter;

    /**
     * Property constructor.
     * @param string $name
     * @param Type $type
     * @param string $setter
     * @param array $annotations
     */
    public function __construct($name, Type $type, string $setter, array $annotations)
    {
        $this->name = $name;
        $this->type = $type;
        $this->annotations = $annotations;
        $this->setter = $setter;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return Type
     */
    public function getType() : Type
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getAnnotations() : array
    {
        return $this->annotations;
    }

    /**
     * @return string
     */
    public function getSetter(): string
    {
        return $this->setter;
    }
}
