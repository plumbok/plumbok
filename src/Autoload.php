<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 09.12.16
 * Time: 23:19
 */
namespace Plumbok;

use Composer\Autoload\ClassLoader;
use PhpParser\PrettyPrinter\Standard;
use Plumbok\Compiler\NodeFinder;

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
    /** @var Standard */
    private $serializer;

    /**
     * @param string $namespace
     * @param Cache $cache Compiler cache, if null then cache in memory and eval code
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
            $loader = new self($namespace, $classLoader, is_null($cache) ? new Cache\NoCache() : $cache);
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
        $this->serializer = new Standard();
        $this->cache = $cache;
    }

    /**
     * @param string $class
     * @return null
     */
    public function load(string $class)
    {
        if (substr($class, 0, $this->length) === $this->namespace) {
            $filename = $this->classLoader->findFile($class);
            if ($this->cache->isFresh($filename)) {
                $this->cache->load($filename);
            } else {
                $nodes = $this->compiler->compile($filename);
                if (count($nodes)) {
                    $tagsUpdater = new TagsUpdater(new NodeFinder());
                    $tagsUpdater->applyNodes($filename, ...$nodes);
                    $this->cache->write($filename, $this->serializer->prettyPrint($nodes));
                    $this->cache->load($filename);
                }
            }
        }
        return null;
    }
}
