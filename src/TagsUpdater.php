<?php declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: brzuchal
 * Date: 18.12.16
 * Time: 12:43
 */
namespace Plumbok;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Plumbok\Compiler\Code\Class_;
use Plumbok\Compiler\NodeFinder;

/**
 * Class TagsInDocBlockFileWriter
 * @package Plumbok\Compiler\File
 * @author Michał Brzuchalski <michal.brzuchalski@gmail.com>
 */
class TagsUpdater
{
    /**
     * @var Parser
     */
    private $phpParser;
    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * TagsFileWriter constructor.
     * @param NodeFinder $nodeFinder
     * @internal param string $filename
     */
    public function __construct(NodeFinder $nodeFinder)
    {
        $this->phpParser = (new ParserFactory)->create(ParserFactory::ONLY_PHP7);
        $this->nodeFinder = $nodeFinder;
    }

    public function applyNodes(string $filename, array $nodes)
    {
        $nodes = $this->phpParser->parse(file_get_contents($filename));
        $namespaces = $this->nodeFinder->findNamespaces(...$nodes);
        $generated = false;
        foreach ($namespaces as $namespace) {
            $typeContext = new TypeContext((string)$namespace->name, $this->nodeFinder->findUses(...$namespace->stmts));
            foreach ($this->nodeFinder->findClasses(...$namespace->stmts) as $class) {
                $generated |= $this->processClass($class, $typeContext);
            }
        }
        if (!$namespaces) {
            $typeContext = new TypeContext('global', $this->nodeFinder->findUses(...$nodes));
            foreach ($this->nodeFinder->findClasses(...$nodes) as $class) {
                $generated |= $this->processClass($class, $typeContext);
            }
        }

    }

    private function createClass(string $namespace, Node\Stmt\Class_ $class)
    {
        return new Class_($namespace, $class);
    }

    /**
     * @param Node[] ...$nodes
     * @return array
     */
    private function findClasses(...$nodes)
    {
        $classes = [];
        $namespaces = $this->nodeFinder->findNamespaces(...$nodes);
        foreach ($namespaces as $namespace) {
            foreach ($this->nodeFinder->findClasses(...$namespace->stmts) as $class) {
                $classes[] = $this->createClass((string)$namespace->name, $class);
            }
//            foreach ($classes as $class) {
//                $positions["{$namespace->name}\\{$class->name}"] = [
//                    'line' => $class->getLine(),
//                    'docComment' => $class->getDocComment(),
//                ];
//            }
        }
        if (!$namespaces) {
//            $classes = $this->nodeFinder->findClasses(...$nodes);
            foreach ($this->nodeFinder->findClasses(...$nodes) as $class) {
                $classes[] = $this->createClass('', $class);
            }
//            foreach ($classes as $class) {
//                $positions[$class->name] = [
//                    'line' => $class->getLine(),
//                    'docComment' => $class->getDocComment(),
//                ];
//            }
        }

        return $classes;
    }

    public function write(string $filename)
    {
        $classes = $this->findClasses(...$this->nodes);
        dump($this->classes, $classes);

//        $fp = fopen($filename, 'rw+');
//        $fragments = [];
//        foreach ($oldDocBlocks as $className => $docBlockPosition) {
//            /** @var Doc $docComment */
//            $docComment = $docBlockPosition['docComment'];
//            /** @var Doc $newDocComment */
//            $newDocComment = $newDocBlocks[$className]['docComment'];
//            if ($docComment->getFilePos()) {
//                $fragments[] = fread($fp, $docComment->getFilePos() - ftell($fp));
//                $fragments[] = $newDocComment->getText();
//                fseek($fp, strlen($docComment->getText()), SEEK_CUR);
//            }
//        }
//        $fragments[] = fread($fp, filesize($filename) - ftell($fp));
//        file_put_contents($filename, implode($fragments));
    }
}
