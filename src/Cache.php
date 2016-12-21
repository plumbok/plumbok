<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 14.12.16
 * Time: 07:59
 */
namespace Plumbok;

/**
 * Interface Cache
 * @package Plumbok
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
interface Cache
{
    /**
     * Checks cached file freshness
     * @param string $className Source class name
     * @param int $time Source file modification time
     * @return bool
     */
    public function isFresh(string $className, int $time): bool;

    /**
     * @param string $className
     */
    public function load(string $className);

    /**
     * Write file to cache
     * @param string $className
     * @param string $content
     */
    public function write(string $className, string $content);
}
