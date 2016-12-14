<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 09.12.16
 * Time: 23:19
 */
namespace Plumbok;

use Composer\Autoload\ClassLoader;

/**
 * Class Autoload
 * @package Plumbok
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class Autoload
{
    /** @var string */
    private $namespace;
    /** @var int */
    private $length = 0;
    /** @var ClassLoader */
    private $classLoader;
    /** @var Compiler */
    private $compiler;
    /** @var Cache */
    private $cache;

    /**
     * @param string $namespace
     * @param Cache $cache
     * @return bool
     */
    public static function register(string $namespace, Cache $cache = null) : bool
    {
        if (true === empty($namespace)) {
            throw new \InvalidArgumentException('Invalid namespace, trying to registered empty namespace');
        }
        foreach (spl_autoload_functions() as $loader) {
            if (false === is_array($loader)) {
                continue;
            }
            if (is_a($loader[0], 'Composer\\Autoload\\ClassLoader') && method_exists($loader[0], 'findFile')) {
                $classLoader = $loader[0];
            }
        }
        if (isset($classLoader)) {
            $loader = new self($namespace, $classLoader, $cache);
            return spl_autoload_register([$loader, 'load'], true, true);
        }
        throw new \RuntimeException("Unable to find Composer ClassLoader, did you forget require 'autoload.php'?");
    }

    /**
     * Autoload constructor.
     * @param string $namespace
     * @param ClassLoader $classLoader
     * @param Cache $cache
     */
    private function __construct(string $namespace, ClassLoader $classLoader, Cache $cache = null)
    {
        $this->namespace = $namespace;
        $this->length = strlen($namespace);
        $this->classLoader = $classLoader;
        $this->compiler = new Compiler();
        $this->cache = $cache;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function load(string $class)
    {
        if (substr($class, 0, $this->length) === $this->namespace) {
            $filename = $this->classLoader->findFile($class);
            if ($this->cache instanceof Cache) {
                if ($this->cache->isFresh($filename)) {
                    $this->cache->load($filename);
                } else {
                    $compiled = $this->compiler->compile($filename);
                    if (empty($compiled)) {
                        return false;
                    }
                    $this->cache->write($filename, "<?php\n{$compiled}");
                    $this->cache->load($filename);
                }
            } else {
                if (file_exists($filename)) {
                    $compiled = $this->compiler->compile($filename);
                    if (empty($compiled)) {
                        return false;
                    }
                    eval($compiled);
                }
            }
        }
    }
}
