<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 10.12.16
 * Time: 10:12
 */
namespace Plumbok\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 * @Attributes({
 *   @Attribute("property", type = "string", required = true)
 * })
 * @property-read string $property
 */
final class ToString
{
    private $property;

    public function __construct(array $values)
    {
        $this->property = $values['property'];
    }

    public function __get($name)
    {
        if ($name === 'property') {
            return $this->property;
        }
    }
}
