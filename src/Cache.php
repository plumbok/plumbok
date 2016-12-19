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
     * @param string $filename Source file name
     * @return bool
     */
    public function isFresh(string $filename): bool;

    /**
     * @param string $filename
     */
    public function load(string $filename);

    /**
     * Write file to cache
     * @param string $filename
     * @param string $content
     */
    public function write(string $filename, string $content);
}
