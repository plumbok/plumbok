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
 * Class FileCache
 * @package Plumbok\Cache
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class FileCache implements Cache
{
    /**
     * @var string
     */
    private $directory;

    /**
     * Cache constructor.
     * @param string $directory
     */
    public function __construct(string $directory)
    {
        $this->directory = $directory;

    }

    /**
     * Checks cached class freshness
     * @param string $className Source class name
     * @param int $time Source file modification time
     * @return bool
     */
    public function isFresh(string $className, int $time) : bool
    {
        $cacheFilename = $this->getCacheFilename($className);

        return file_exists($cacheFilename) && filemtime($cacheFilename) >= $time;
    }

    /**
     * @param string $className
     */
    public function load(string $className)
    {
        load($this->getCacheFilename($className));
    }

    /**
     * Write file to cache
     * @param string $className
     * @param string $content
     */
    public function write(string $className, string $content)
    {
        $cacheFilename = $this->getCacheFilename($className);
        file_put_contents($cacheFilename, "<?php\n{$content}\n");
    }

    /**
     * @param string $className
     * @return string
     */
    private function getCacheFilename(string $className) : string
    {
        if ((false == file_exists($this->directory)) && (false == is_dir($this->directory))) {
            mkdir($this->directory, 0777, true);
        }

        return $this->directory . DIRECTORY_SEPARATOR . str_replace('\\', '.', $className) . '.php';
    }
}

function load(string $file)
{
    include_once $file;
}
