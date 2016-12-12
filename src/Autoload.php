<?php
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 09.12.16
 * Time: 23:19
 */
namespace Plumbok;

use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\PhpParser;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

/**
 * Class Autoload
 * @package Plumbok
 * @author Michał Brzuchalski <m.brzuchalski@madkom.pl>
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

    /**
     * @param string $namespace
     * @return bool
     */
    public static function register(string $namespace) : bool
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
            $loader = new self($namespace, $classLoader);
            return spl_autoload_register([$loader, 'load'], true, true);
        }
        throw new \RuntimeException("Unable to find Composer ClassLoader, did you forget require 'autoload.php'?");
    }

    /**
     * Autoload constructor.
     * @param string $namespace
     * @param ClassLoader $classLoader
     */
    private function __construct(string $namespace, ClassLoader $classLoader)
    {
        $this->namespace = $namespace;
        $this->length = strlen($namespace);
        $this->classLoader = $classLoader;

        $parser = (new ParserFactory)->create(ParserFactory::ONLY_PHP7);
        $prettyPrinter = new Standard;
        $this->compiler = new Compiler($parser, $prettyPrinter);
    }

    public function load(string $class)
    {
        if (substr($class, 0, $this->length) === $this->namespace) {
            $filename = $this->classLoader->findFile($class);
            if (file_exists($filename)) {
                eval($this->compiler->compile($filename));
            }
        }
    }
}
