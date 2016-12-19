<?php
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
     * @param string $filename Source file name
     * @return bool
     */
    public function isFresh(string $filename): bool
    {
        return false;
    }

    /**
     * @param string $filename
     */
    public function load(string $filename)
    {
        if (array_key_exists($filename, $this->code)) {
            eval($this->code[$filename]);
        }
    }

    /**
     * Write file to cache
     * @param string $filename
     * @param string $content
     */
    public function write(string $filename, string $content)
    {
        $this->code[$filename] = $content;
    }
}
