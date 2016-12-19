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
     * Checks cached file freshness
     * @param string $filename Source file name
     * @return bool
     */
    public function isFresh(string $filename) : bool
    {
        if (false === file_exists($filename)) {
            throw new \InvalidArgumentException("Unable to read source file: {$filename}");
        }
        $cacheFilename = $this->getCacheFilename($filename);

        return file_exists($cacheFilename) && filemtime($cacheFilename) >= filemtime($filename);
    }

    /**
     * @param string $filename
     */
    public function load(string $filename)
    {
        load($this->getCacheFilename($filename));
    }

    /**
     * Write file to cache
     * @param string $filename
     * @param string $content
     */
    public function write(string $filename, string $content)
    {
        $cacheFilename = $this->getCacheFilename($filename);
        file_put_contents($cacheFilename, "<?php\n{$content}\n");
    }

    /**
     * @param string $filename
     * @return string
     */
    private function getCacheFilename(string $filename) : string
    {
        $fileInfo = new \SplFileInfo($filename);
        $directory = $this->directory . DIRECTORY_SEPARATOR . substr(md5($fileInfo->getPath()), 0, 8);
        if ((false == file_exists($directory)) && (false == is_dir($directory))) {
            mkdir($directory, 0777, true);
        }

        return $directory . DIRECTORY_SEPARATOR . $fileInfo->getFilename();
    }
}

function load(string $file)
{
    include_once $file;
}
