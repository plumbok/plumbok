<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 18.12.16
 * Time: 13:31
 */
namespace Plumbok\Cache;

use Plumbok\Cache;

/**
 * Class NoCache
 * @package Plumbok\Cache
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class NoCache implements Cache
{
    /** @var array */
    private $code = [];
    /**
     * Checks cached file freshness
     * @param string $className Source file name
     * @param int $time Source file modification time
     * @return bool
     */
    public function isFresh(string $className, int $time): bool
    {
        return false;
    }

    /**
     * @param string $className
     */
    public function load(string $className)
    {
        if (array_key_exists($className, $this->code)) {
            eval($this->code[$className]);
        }
    }

    /**
     * Write file to cache
     * @param string $className
     * @param string $content
     */
    public function write(string $className, string $content)
    {
        $this->code[$className] = $content;
    }
}
